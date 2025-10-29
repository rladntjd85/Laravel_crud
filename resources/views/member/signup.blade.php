@extends('boards.layout')
@section('content')

<div class="container section-title" style="margin-bottom:0px;margin-top:10px;" data-aos="fade-up">
  <div class="section-title-container d-flex align-items-center justify-content-between" style="padding-bottom:0px;">
    <h2>회원가입</h2>
    <p>회원가입 페이지입니다.</p>
  </div>
</div>
<!-- End Section Title -->

<div class="container">
  <div class="row">
    <div class="col-lg-12">

    <section class="vh-100">
      <div class="container h-100">
        <div class="row d-flex justify-content-center align-items-center h-100">
          <div class="col-xl-9">
            <div class="card" style="border-radius: 15px;">
              <div class="card-body">
                <div class="row align-items-center pt-4 pb-3">
                  <div class="col-md-3 ps-5">
                    <h6 class="mb-0">이름(닉네임)</h6>
                  </div>
                  <div class="col-md-9 pe-5">
                    <input type="text" name="username" id="username" class="form-control form-control-lg" />
                    <br>
                    <span id="usernamemsg"></span>
                  </div>
                </div>
                <hr class="mx-n3">
                <div class="row align-items-center py-3">
                  <div class="col-md-3 ps-5">
                    <h6 class="mb-0">이메일</h6>
                  </div>
                  <div class="col-md-9 pe-5">
                    <input type="email" name="email" id="email" class="form-control form-control-lg" placeholder="example@example.com" /><br>
                    <span id="emailmsg"></span>
                  </div>
                </div>
                <hr class="mx-n3">
                <div class="row align-items-center py-3">
                  <div class="col-md-3 ps-5">
                    <h6 class="mb-0">비밀번호</h6>
                  </div>
                  <div class="col-md-9 pe-5">
                      <input type="password" name="password" id="password" class="form-control form-control-lg" />
                      <span id="passwordmsg">비밀번호는 문자, 숫자, 특수문자포함해서 8자 이상 입력해 주십시오.</span>
                  </div>
                </div>
                <hr class="mx-n3">
                <div class="row align-items-center py-3">
                  <div class="col-md-3 ps-5">
                    <h6 class="mb-0">비밀번호 확인</h6>
                  </div>
                  <div class="col-md-9 pe-5">
                      <input type="password" name="password_confirmation" id="password_confirmation" class="form-control form-control-lg" />
                  </div>
                </div>
                <hr class="mx-n3">
                <div class="px-5 py-4" style="text-align:center;">
                  <button type="button" data-mdb-button-init data-mdb-ripple-init class="btn btn-primary btn-lg" id="signup">가입하기</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

  </div>
{{-- @include('blog.classroomside') --}}
</div>
</div>

  <script>
    $("#signup").click(function () {
        var username=$("#username").val();
        var email=$("#email").val();
        var password=$("#password").val();
        var password_confirmation=$("#password_confirmation").val();

        if(!username || !email || !password || !password_confirmation){
          alert('필수값을 입력해주세요.');
          return false;
        }
        if(password!=password_confirmation){
          alert('비밀번호를 다시 확인해 주십시오.');
          return false;
        }
       
        var data = {
          username : username,
          email : email,
          password : password,
          password_confirmation : password_confirmation
        };
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: 'post',
            url: '{{ route('auth.signupok') }}',
            dataType: 'json',
            data: data,
            success: function(data) {
                if(data.result==true){
                    alert(data.msg);
                    location.href='/login';
                }else{
                    alert(data.msg);
                    return false;
                }
            },
            error: function(data) {
            console.log("error" +JSON.stringify(data));
            }
        });
    });

    $("#email").on("keyup", function() {
        var email=$("#email").val();
        if(!email){
          return false;
        }
        var data = {
            email : email
        };
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: 'post',
            url: '{{ route('auth.emailcheck') }}',
            dataType: 'json',
            data: data,
            success: function(data) {
                if(data.result==true){
                    $("#emailmsg").html("<font color='blue'>"+data.msg+"</font>");
                }else{
                    $("#emailmsg").html("<font color='red'>"+data.msg+"</font>");
                }
            },
            error: function(data) {
            console.log("error" +JSON.stringify(data));
            }
        });
    });
  </script>
@endsection  