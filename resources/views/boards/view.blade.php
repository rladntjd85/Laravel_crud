@extends('boards.layout')

@section('header')
    @include('boards.toptitle', ['toptitle'=>'게시판 보기', 'multi'=>$boards->multi])
@endsection

@section('content')

    <table class="table table-striped table-hover">
        <tbody>
            <tr>
                <th width="200">제목</th>
                <td>{{ $boards->subject }}</td>
            </tr>
            <tr>
                <td colspan="2">글쓴이 : {{ $boards->userid }} / 조회수 : {{ number_format($boards->cnt) }} / 등록일 : {{ $boards->regdate }}</td>
            </tr>
            <tr>
                <th width="200">내용</th>
                <td>{!! nl2br($boards->content) !!}</td>
            </tr>
            @if(count($attaches)>0)
            <tr>
                <th width="200">첨부 이미지</th>
                <td>
                    <div class="row row-cols-1 row-cols-md-6 g-4" id="attachFiles" style="margin-left:0px;">
                    @foreach ($attaches as $att)
                    <div id='af_{{ $att->id }}' class='card h-100' style='width:120px;margin-right: 10px;margin-bottom: 10px;'><a href="#" onclick="window.open('/boards/imgpop/{{ $att->filename }}','imgpop','width=600,height=400,scrollbars=yes');"><img src='/images/{{ $att->filename }}' width='100' /></a></div>
                    @endforeach
                    </div>
                </td>
            </tr>
            @endif
        </tbody>
    </table>
    <div align="right">
        @auth()
            @if($boards->userid==auth()->user()->userid)
                <a href="/boards/write/{{ $boards->multi }}/{{ $boards->bid }}"><button type="button" class="btn btn-secondary">수정</button></a>
                <a href="/boards/delete/{{ $boards->bid }}/{{ $boards->pagenumber }}" class="btn btn-secondary" onclick="return confirm('삭제하시겠습니까?');">삭제</a>
            @endif
        @endauth
        <a href="/boards/{{ $boards->multi }}/?page={{ $boards->pagenumber }}" class="btn btn-primary">목록</a>
    </div>
    <div style="padding:10px;">
    </div>

    <!--댓글 리스트 시작 -->
    <div id="reply">
        @foreach ($memos as $m)
        @if ($m->pid)
            <div class="d-flex">
                <div class="p-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-return-right" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M1.5 1.5A.5.5 0 0 0 1 2v4.8a2.5 2.5 0 0 0 2.5 2.5h9.793l-3.347 3.346a.5.5 0 0 0 .708.708l4.2-4.2a.5.5 0 0 0 0-.708l-4-4a.5.5 0 0 0-.708.708L13.293 8.3H3.5A1.5 1.5 0 0 1 2 6.8V2a.5.5 0 0 0-.5-.5z"></path>
                </svg>
                </div>
                <div class="flex-fill" style="width:100%">
                    <div class="card mt-2">
                        <div class="card-header">
                            <table>
                                <tbody><tr class="align-middle">
                                    <td rowspan="2" class="pr-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-card-text" viewBox="0 0 16 16">
                                            <path d="M14.5 3a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5zm-13-1A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2z"/>
                                            <path d="M3 5.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5M3 8a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9A.5.5 0 0 1 3 8m0 2.5a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5"/>
                                          </svg>
                                    </td>
                                    <td class="ml">{{ $m->userid }}</td>
                                </tr>
                                <tr>
                                    <td>
                                        <font size="2">{{ disptime($m->created_at) }}</font>
                                        <span style="cursor:pointer" onclick="#"></span>
                                    </td>
                                </tr>
                            </tbody></table>
                        </div>
                        <div class="card-body" id="{{ 'memolist_'.$m->id }}">
                            <p class="card-text">{!! nl2br($m->memo) !!}</p>
                            @if(auth()->check() && $m->userid == auth()->user()->userid)
                            <span style="float:right;">
                                <span class="badge bg-dark" style="cursor:pointer;padding:10px;"><a onclick="memo_modify('{{ $m->id }}','r')">수정</a></span>
                                <span class="badge bg-dark" style="cursor:pointer;padding:10px;"><a onclick="memo_delete('{{ $m->id }}','{{ $boards->bid }}')">삭제</a></span>
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="card mt-2">
                <div class="card-header p-2">
                    <table>
                        <tbody>
                            <tr class="align-middle">
                                <td rowspan="2" class="pr-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-chat-square-dots" viewBox="0 0 16 16">
                                        <path d="M14 1a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1h-2.5a2 2 0 0 0-1.6.8L8 14.333 6.1 11.8a2 2 0 0 0-1.6-.8H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1zM2 0a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h2.5a1 1 0 0 1 .8.4l1.9 2.533a1 1 0 0 0 1.6 0l1.9-2.533a1 1 0 0 1 .8-.4H14a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z"/>
                                        <path d="M5 6a1 1 0 1 1-2 0 1 1 0 0 1 2 0m4 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0m4 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0"/>
                                    </svg>
                                </td>
                                <td class="ml">{{ $m->userid }}</td>
                            </tr>
                            <tr>
                                <td>
                                    {{ disptime($m->created_at) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-body" id="{{ 'memolist_'.$m->id }}">
                    <p class="card-text">
                        @if($m->filename)
                            <img src='/images/{{ $m->filename }}' width='100' />
                        @endif
                        {!! nl2br($m->memo) !!}
                    </p>
                    @auth()
                    <span class="badge bg-dark" style="cursor:pointer;padding:10px;"><a onclick="reply_write('{{ $m->id }}','{{ $boards->bid }}')">댓글</a></span>
                        <span style="float:right;">
                            @if(auth()->check() && $m->userid == auth()->user()->userid)
                                <span class="badge bg-dark" style="cursor:pointer;padding:10px;"><a onclick="memo_modify('{{ $m->id }}')">수정</a></span>
                                <span class="badge bg-dark" style="cursor:pointer;padding:10px;"><a onclick="memo_delete('{{ $m->id }}','{{ $boards->bid }}')">삭제</a></span>
                            @endif
                        </span>
                    @endauth
                </div>
                <div class="input-group" style="margin-top:10px;margin-bottom:10px;display:none;" id="{{ 'memo_reply_area_'.$m->id }}">
                    <div class="p-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-return-right" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M1.5 1.5A.5.5 0 0 0 1 2v4.8a2.5 2.5 0 0 0 2.5 2.5h9.793l-3.347 3.346a.5.5 0 0 0 .708.708l4.2-4.2a.5.5 0 0 0 0-.708l-4-4a.5.5 0 0 0-.708.708L13.293 8.3H3.5A1.5 1.5 0 0 1 2 6.8V2a.5.5 0 0 0-.5-.5z"></path>
                        </svg>
                    </div>
                    <textarea class="form-control" aria-label="With textarea" name="{{ 'memo_reply_'.$m->id }}" id="{{ 'memo_reply_'.$m->id }}" placeholder="대댓글을 입력해주세요"></textarea>
                    @auth()
                        <button type="button" class="btn btn-secondary" style="float:right;" id="{{ 'memo_submit_reply_'.$m->id }}" onclick="memo_reply_up('{{ $m->id }}','{{ $boards->bid }}')">입력</button>
                    @else
                        <button type="button" class="btn btn-secondary" style="float:right;" onclick="alert('로그인 하셔야 입력할 수 있습니다.');">입력</button>
                    @endauth
                </div>
            </div>
        @endif
        @endforeach
    </div>
    <!-- 댓글 리스트 끝 -->

    <!-- 댓글 입력 -->
    <div class="input-group" id="firstmemo" style="margin-top:10px;margin-bottom:10px;">
        <span class="input-group-text" id="memo_image_view" style="display:none;"></span>
        <button type="button" id="attmemoimg" class="btn btn-secondary">이미지첨부</button>
        <input type="hidden" name="memopid" id="memopid" value="{{ time() }}">
        <input type="hidden" name="modimemoid" id="modimemoid" value="0">
        <input type="hidden" name="memo_file" id="memo_file">
        <input type="file" name="upfile" id="upfile" accept="image/*" style="display:none;">
        <textarea class="form-control" aria-label="With textarea" style="height:100px;" name="memo" id="memo" placeholder="댓글을 입력해주세요"></textarea>
        @auth()
            <button type="button" class="btn btn-secondary" style="float:right;" id="memo_submit" onclick="memoup()">입력</button>
        @else
            <button type="button" class="btn btn-secondary" style="float:right;" id="memo_submit" onclick="alert('로그인 하셔야 입력할 수 있습니다.');">입력</button>
        @endauth
    </div>
    <!-- 댓글 입력 끝-->
    <div style="padding:20px;">
    </div>
    <script>
        function memoup(){
            var memo=$("#memo").val();
            var memo_file=$("#memo_file").val();
            var data = {
                memo : memo,
                memo_file : memo_file,
                bid : {{ $boards->bid }}
            };
            $.ajax({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                type: 'post',
                url: '{{ route('boards.memoup') }}',
                dataType: 'json',
                data: data,
                success: function(data) {
                    location.reload();
                },
                error: function(data) {
                console.log("error" +JSON.stringify(data));
                }
            });
        }

    function memoatt(m){
        $("#modimemoid").val(m);
        $('#upfile').click();
    }

    $("#attmemoimg, .reply_attach").click(function () {
        $('#upfile').click();
    });
   
    $("#upfile").change(function(){
        var formData = new FormData();
        var files = $('#upfile').prop('files');
        for(var i=0; i < files.length; i++) {
            attachFile(files[i]);
        }
    });

    function attachFile(file) {
        var memopid = $("#memopid").val();
        var modimemoid = $("#modimemoid").val();
        var formData = new FormData();
        formData.append("file", file);
        formData.append("pid", memopid);
        formData.append("code", "memoattach");
        formData.append("modimemoid", modimemoid);
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            url: '{{ route('boards.saveimage') }}',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            dataType : 'json' ,
            type: 'POST',
            success: function (return_data) {
                var html = "<img src='/images/"+return_data.fn+"' style='max-width:100%;height:88px;'>";
                if(modimemoid>0){
                    $("#modi_att_view_"+modimemoid).html(html);
                    $("#modi_att_view_"+modimemoid).show();
                    $("#modimemoimg_"+modimemoid).hide();
                }else{
                    $("#memo_image_view").html(html);
                    $("#memo_image_view").show();
                    $("#attmemoimg").hide();
                    $("#memo_file").val(return_data.fn);
                }
            }
            , beforeSend: function () {
                if(modimemoid>0){
                    $("#modimemoimg_"+modimemoid).hide();
                    $("#modi_att_view_"+modimemoid).show();
                    $('#modi_att_view_'+modimemoid).html('<div class="spinner-border text-dark" role="status"><span class="visually-hidden">Loading...</span></div>');
                }else{
                    $("#attmemoimg").hide();
                    $("#memo_image_view").show();
                    $('#memo_image_view').html('<div class="spinner-border text-dark" role="status"><span class="visually-hidden">Loading...</span></div>');
                }
            }
            , complete: function () {
            }
            });
    }

    function memo_modify(m, r){
            var data = {
                memoid : m
            };
            $.ajax({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                type: 'post',
                url: '{{ route('boards.memomodi') }}',
                dataType: 'json',
                data: data,
                success: function(data) {
                    if(data.attfile == true){
                        var html='<div class="input-group" id="modifymemo" style="margin-top:10px;margin-bottom:10px;"><div id="af_'+data.att.id+'" class="card h-100" style="text-align:center;"><img src="/images/'+data.att.filename+'" width="80" /><a href="javascript:;" onclick=\'memodeletefile("'+data.att.filename+'","'+data.att.id+'","'+data.att.pid+'")\'><span class="badge text-bg-warning">삭제</span></a></div><input type="hidden" name="memopid" id="memopid" value="'+m+'"><input type="hidden" name="memo_modi_file" id="memo_modi_file"><textarea class="form-control" aria-label="With textarea" style="height:100px;" name="memomodify_'+m+'" id="memomodify_'+m+'">'+data.memos.memo+'</textarea><button type="button" class="btn btn-secondary" style="float:right;" id="memo_modifyup" onclick="memomodifyup('+m+')">수정</button></div>';
                    }else{
                        if(r=="r"){
                            var html='<div class="input-group" id="modifymemo" style="margin-top:10px;margin-bottom:10px;"><input type="hidden" name="memopid" id="memopid" value="'+m+'"><textarea class="form-control" aria-label="With textarea" style="height:100px;" name="memomodify_'+m+'" id="memomodify_'+m+'">'+data.memos.memo+'</textarea><button type="button" class="btn btn-secondary" style="float:right;" id="memo_modifyup" onclick="memomodifyup('+m+')">수정</button></div>';
                        }else{
                            var html='<div class="input-group" id="modifymemo" style="margin-top:10px;margin-bottom:10px;"><span class="input-group-text" id="modi_att_view_'+m+'" style="display:none;"></span><button type="button" id="modimemoimg_'+m+'" class="btn btn-secondary" onclick="memoatt('+m+')">이미지첨부</button><input type="hidden" name="memopid" id="memopid" value="'+m+'"><input type="hidden" name="memo_modi_file" id="memo_modi_file"><textarea class="form-control" aria-label="With textarea" style="height:100px;" name="memomodify_'+m+'" id="memomodify_'+m+'">'+data.memos.memo+'</textarea><button type="button" class="btn btn-secondary" style="float:right;" id="memo_modifyup" onclick="memomodifyup('+m+')">수정</button></div>';
                        }
                    }
                    $("#memolist_"+m).html(html);
                },
                error: function(data) {
                    console.log("error" +JSON.stringify(data));
                }
            });
    }

    function memomodifyup(m){
            var memo=$("#memomodify_"+m).val();
            var data = {
                memo : memo,
                memoid : m
            };
            $.ajax({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                type: 'post',
                url: '{{ route('boards.memomodifyup') }}',
                dataType: 'json',
                data: data,
                success: function(data) {
                    location.reload();
                },
                error: function(data) {
                    console.log("error" +JSON.stringify(data));
                }
            });
    }

    function memodeletefile(fn,fid,pid){
        if(!confirm('삭제하시겠습니까?')){
            return false;
        }

        var data = {
            fn : fn,
            fid : fid,
            pid : pid
        };
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: 'post',
            url: '{{ route('boards.memodeletefile') }}',
            dataType: 'json',
            data: data,
            success: function(data) {
                alert("삭제했습니다.");
                memo_modify(pid);
            },
            error: function(data) {
                console.log("error" +JSON.stringify(data));
            }
        });
    }

    function memo_delete(m, b){
        if(!confirm('삭제하시겠습니까?')){
            return false;
        }
        var data = {
            id : m,
            bid : b
        };
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: 'post',
            url: '{{ route('boards.memodelete') }}',
            dataType: 'json',
            data: data,
            success: function(data) {
                location.reload();
            },
            error: function(data) {
                console.log("error" +JSON.stringify(data));
            }
        });
    }

    function reply_write(m, b){
        $("#memo_reply_area_"+m).toggle();
    }

    function memo_reply_up(m, b){
        var memo=$("#memo_reply_"+m).val();
        var data = {
            memo : memo,
            pid : m,
            bid : b
        };
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: 'post',
            url: '{{ route('boards.memoup') }}',
            dataType: 'json',
            data: data,
            success: function(data) {
            console.log(JSON.stringify(data));
            location.reload();
            },
            error: function(data) {
            console.log("error" +JSON.stringify(data));
            }
        });
    }
</script>
    @endsection    