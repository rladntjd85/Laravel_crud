<?php

namespace App\Http\Controllers;
use App\Models\Members;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class MemberController extends Controller
{
    public function login(){
        return view('member.login');
    }

    public function signup()
    {
        return view('member.signup');
    }

    public function signupok(Request $request)
    {
        // 'username' 필드명으로 요청받으므로 Validator 필드명도 맞춤
        $validator = Validator::make($request->all(), [
            'username' => 'required',          // 프론트엔드의 username과 맞춰야 함
            'email' => 'required|email',
            'password' => [
                'required',
                'confirmed',                  // password_confirmation과 비교
                Password::min(8)
                    ->letters()
                    ->numbers()
                    ->symbols()
                    ->mixedCase()              // 대문자 소문자 혼용까지 권장
            ],
        ], [
            'username.required' => '이름(닉네임)을 입력해주세요.',
            'email.required' => '이메일을 입력해주세요.',
            'email.email' => '유효한 이메일 주소를 입력해주세요.',
            'password.required' => '비밀번호를 입력해주세요.',
            'password.confirmed' => '비밀번호 확인이 일치하지 않습니다.',
            'password.min' => '비밀번호는 최소 :min자 이상이어야 합니다.',
            // 기타 메시지 필요 시 추가
        ]);

        if ($validator->fails()) {
            return response()->json([
                'msg' => '필수값이 빠졌거나 비밀번호 규칙을 위반했습니다.',
                'result' => false,
                'errors' => $validator->errors()
            ], 200);
        }

        $passwd = hash('sha512', $request->password);
        $uid = explode("@", $request->email);
        $form_data = [
            'userid' => $uid[0],
            'email' => $request->email,
            'passwd' => $passwd,
            'username' => $request->username, // name -> username 변경
        ];

        $ms = Members::where('email', $request->email)->count();
        if ($ms) {
            return response()->json([
                'msg' => '이미 사용중인 이메일입니다.',
                'result' => false,
            ], 200);
        }

        $rs = Members::create($form_data);

        if ($rs) {
            return response()->json([
                'msg' => '가입해 주셔서 감사합니다.',
                'result' => true,
            ], 200);
        } else {
            return response()->json([
                'msg' => '실패했습니다. 관리자에게 문의해주세요.',
                'result' => false,
            ], 200);
        }
    }

    public function emailcheck(Request $request){
        $email = $request->email;
       
        $rs = Members::where('email',$email)->count();
        if($rs){
            return response()->json(array('msg'=> "이미 사용중인 이메일입니다.", 'result'=>false), 200);
        }else{
            return response()->json(array('msg'=> "사용할 수 있는 이메일입니다.", 'result'=>true), 200);
        }
    }

    public function loginok(Request $request){

        $validated = $request->validate([
            'email' => 'required',
            'passwd' => 'required',
        ]);
       
        $email = $request->email;
        $passwd = $request->passwd;
        $passwd = hash('sha512',$passwd);
        $remember = $request->remember;
        $loginInfo = array(
            'email' => $email,
            'passwd' => $passwd
        );

        $ismember = Members::where($loginInfo)->first();
        if($ismember){
            Auth::login($ismember, $remember);
            return redirect() -> route('boards.index');
        }else{
            return redirect() -> route('login')->with('loginFail', '아이디나 비밀번호가 틀렸습니다.');
        }
    }

    public function logout(){
        auth() -> logout();
        return redirect() -> route('boards.index');
    }
}