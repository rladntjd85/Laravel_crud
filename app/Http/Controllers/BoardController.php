<?php

namespace App\Http\Controllers;
use App\Models\Board;
use App\Models\FileTables;
use App\Models\Memos;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BoardController extends Controller
{
    public function index($multi = "free"){
        $boards = Board::where('multi',$multi)
                        ->where('status',1)
                        ->orderBy('bid','desc')->paginate(20);
        return view('boards.index', ['boards' => $boards, 'multi' => $multi]);
    }

    public function show($bid,$page)
    {
        Board::find($bid)->increment('cnt');
        $boards = Board::findOrFail($bid);
        $boards->content = htmlspecialchars_decode($boards->content);
        $boards->pagenumber = $page??1;
        $attaches = FileTables::where('pid',$bid)->where('code','boardattach')->where('status',1)->get();

        //DB::enableQueryLog();
        // $memos = DB::table('memos')
        //         ->leftJoinSub('select pid, filename from file_tables where code=\'memoattach\' and status=1', 'f', 'memos.id', 'f.pid')
        //         ->select('memos.*', 'f.filename')
        //         ->where('memos.bid', $bid)->where('memos.status',1)
        //         ->orderByRaw('IFNULL(memos.pid,memos.id), memos.pid ASC')
        //         ->orderBy('memos.id', 'asc')
        //         ->get();

        $memos = DB::table('memos')
                ->leftJoin('file_tables as f', function ($join) {
                    $join->on('memos.id', '=', 'f.pid')
                        ->where('f.code', '=', 'memoattach')
                        ->where('f.status', '=', 1);
                })
                ->select('memos.*', DB::raw('SUBSTRING_INDEX(GROUP_CONCAT(f.filename ORDER BY f.id DESC), ",", 1) as filename'))
                ->where('memos.bid', $bid)
                ->where('memos.status', 1)
                ->groupBy('memos.id') // memos.id를 기준으로 그룹화하여 중복을 제거합니다.
                ->orderByRaw('IFNULL(memos.pid, memos.id), memos.pid ASC')
                ->orderBy('memos.id', 'asc')
                ->get();
        // print_r($memos);
        return view('boards.view', ['boards' => $boards, 'attaches' => $attaches, 'memos' => $memos]);
    }

    public function write($multi,$bid=null)
    {
        if(auth()->check()){
            $boards = array();
            $attaches = array();
            $bid = $bid??0;
            if($bid){
                $boards = Board::findOrFail($bid);
                $attaches = FileTables::where('pid',$bid)->where('status',1)->where('code','boardattach')->get();
                return view('boards.write', ['multi' => $multi, 'bid' => $bid, 'boards' => $boards, 'attaches' => $attaches]);
            }else{
                return view('boards.write', ['multi' => $multi, 'bid' => $bid, 'boards' => $boards, 'attaches' => $attaches]);
            }
        }else{
            return redirect()->back()->withErrors('로그인 하십시오.');
        }
    }

    public function create(Request $request)
    {
        $form_data = array(
            'subject' => $request->subject,
            'content' => $request->content,
            'userid' => Auth::user()->userid,
            'email' => Auth::user()->email,
            'multi' => $request->multi??'free',
            'status' => 1
        );

        if(auth()->check()){
            $rs=Board::create($form_data);
            FileTables::where('pid', $request->pid)->where('userid', Auth::user()->userid)->wherein('code',['boardattach','editorattach'])->update(array('pid' => $rs->bid));
            return response()->json(array('msg'=> "succ", 'bid'=>$rs->bid), 200);
        }
    }

    public function saveimage(Request $request)
    {
        $request->validate([
            'file' => 'required|image|max:2048'
        ]);

        if(auth()->check()){
            $image = $request->file('file');
            $new_name = rand().'_'.time().'.'.$image->getClientOriginalExtension();
            $image->move(public_path('images'), $new_name);
            $pid = $request->modimemoid?$request->modimemoid:$request->pid;
            $fid = rand();
            $form_data = array(
                'pid' => $pid,
                'userid' => Auth::user()->userid,
                'code' => $request->code,
                'filename' => $new_name
            );
            $rs=FileTables::create($form_data);
            return response()->json(array('msg'=> "등록했습니다.", 'result'=>'succ', 'fn'=>$new_name, 'fid'=>$fid), 200);
        }else{
            return response()->json(array('msg'=> "로그인 하십시오", 'result'=>'fail'), 200);
        }
    }

    public function deletefile(Request $request)
    {
        $image = $request->fn;
        if(unlink(public_path('images')."/".$image)){
            FileTables::where('filename', $image)->where('code', $request->code)->where('userid', Auth::user()->userid)->update(array('status' => 0));
        }

        return response()->json(array('msg'=> "succ", 'fn'=>$image, 'fid'=>substr($image,0,10)), 200);
    }

    public function imgpop($imgfile)
    {
        return view('boards.imgpop', ['imgfile' => $imgfile]);
    }

    public function update(Request $request)
    {
        $form_data = array(
            'subject' => $request->subject,
            'content' => $request->content
        );

        if(auth()->check()){
            $boards = Board::findOrFail($request->bid);
            if(Auth::user()->userid==$boards->userid){
                $attaches = FileTables::where('pid',$request->bid)->where('status',1)->where('code','editorattach')->get();
                foreach($attaches as $att){//file_tables에 있는 파일명이 본문에 있는지 확인해서 없으면 삭제한다.
                    if(!strpos($request->content, $att->filename)){
                        unlink(public_path('images')."/".$att->filename);
                        FileTables::where('id', $att->id)->update(array('status' => 0));
                    }
                }
                Board::where('bid', $request->bid)->update($form_data);
                return response()->json(array('msg'=> "succ", 'bid'=>$request->bid), 200);
            }else{
                return response()->json(array('msg'=> "fail", 200));
            }
        }
    }

    public function delete($bid,$page)
    {
        $boards = Board::findOrFail($bid);
        if(Auth::user()->userid==$boards->userid){
            $attaches = FileTables::where('pid',$bid)->where('status',1)->get();
            foreach($attaches as $att){
                unlink(public_path('images')."/".$att->filename);
                FileTables::where('id', $att->id)->update(array('status' => 0));
            }
            $boards->delete();
            return redirect('/boards/'.$boards->multi.'?page='.$page);
        }else{
            return redirect('/boards/show/'.$bid.'/'.$page);
        }
    }

    public function summernote($multi, $bid = null)
    {
        if($bid){
            $boards = Board::findOrFail($bid);
        }else{
            $boards = array();
        }
        return view('boards.summernote', ['multi' => $multi, 'boards' => $boards]);
    }

    public function memoup(Request $request)
    {
        $form_data = array(
            'memo' => $request->memo,
            'bid' => $request->bid,
            'pid' => $request->pid??null,
            'userid' => Auth::user()->userid
        );

        if(auth()->check()){
            $rs=Memos::create($form_data);
            
            if($rs){
                Board::find($request->bid)->increment('memo_cnt');//부모글의 댓글 갯수 업데이트
                Board::where('bid', $request->bid)->update([//부모글의 댓글 날짜 업데이트
                    'memo_date' => date('Y-m-d H:i:s')
                ]);
                if($request->memo_file){
                    FileTables::where('filename', $request->memo_file)->where('userid', Auth::user()->userid)->where('code','memoattach')->update(array('pid' => $rs->id));
                }
            }

            return response()->json(array('msg'=> "succ", 'num'=>$rs), 200);
        }
    }

    public function memomodi(Request $request)
    {
        $memos = Memos::findOrFail($request->memoid);
        if(Auth::user()->userid==$memos->userid){
            $attaches = FileTables::where('pid',$memos->id)->where('code','memoattach')->where('status',1)->get();
            return response()->json(array('msg'=> "succ", 'memos'=>$memos, 'att'=>$attaches), 200);
        }else{
            return response()->json(array('msg'=> "fail"), 200);
        }
    }

    public function memomodifyup(Request $request)
    {
        $memos = Memos::findOrFail($request->memoid);
        if(Auth::user()->userid==$memos->userid){
            $form_data = array(
                'memo' => $request->memo
            );
            Memos::where('id', $request->memoid)->update($form_data);
            return response()->json(array('msg'=> "succ", 'data'=>$request->memoid), 200);
        }else{
            return response()->json(array('msg'=> "fail"), 200);
        }
    }

    public function memodelete(Request $request)
    {
        $data = Memos::findOrFail($request->id);
        if(Auth::user()->userid==$data->userid){
            $rs = Memos::where('id', $request->id)->update(array('status' => 0));
            if($rs){
                Board::find($request->bid)->decrement('memo_cnt');
                $fs=FileTables::where('pid', $data->id)->get();
                if($fs){
                    foreach($fs as $f){
                        if(FileTables::where('id', $f->id)->where('userid', Auth::user()->userid)->update(array('status' => 0))){
                            unlink(public_path('images')."/".$f->filename);
                        }
                    }
                }
            }
            return response()->json(array('msg'=> "succ", 'num'=>$rs), 200);
        }else{
            return response()->json(array('msg'=> "fail"), 200);
        }
    }
 
 
}
