@extends('boards.layout')
@section('content')
<style>
    html,
    body {
    height: 100%;
    }

    body {
    display: flex;
    align-items: center;
    padding-top: 40px;
    padding-bottom: 40px;
    background-color: #f5f5f5;
    }

    .form-signin {
    max-width: 330px;
    padding: 15px;
    }

    .form-signin .form-floating:focus-within {
    z-index: 2;
    }

    .form-signin input[type="email"] {
    margin-bottom: -1px;
    border-bottom-right-radius: 0;
    border-bottom-left-radius: 0;
    }

    .form-signin input[type="password"] {
    margin-bottom: 10px;
    border-top-left-radius: 0;
    border-top-right-radius: 0;
    }


    .bd-placeholder-img {
      font-size: 1.125rem;
      text-anchor: middle;
      -webkit-user-select: none;
      -moz-user-select: none;
      user-select: none;
    }

    @media (min-width: 768px) {
      .bd-placeholder-img-lg {
        font-size: 3.5rem;
      }
    }

    .b-example-divider {
      height: 3rem;
      background-color: rgba(0, 0, 0, .1);
      border: solid rgba(0, 0, 0, .15);
      border-width: 1px 0;
      box-shadow: inset 0 .5em 1.5em rgba(0, 0, 0, .1), inset 0 .125em .5em rgba(0, 0, 0, .15);
    }

    .b-example-vr {
      flex-shrink: 0;
      width: 1.5rem;
      height: 100vh;
    }

    .bi {
      vertical-align: -.125em;
      fill: currentColor;
    }

    .nav-scroller {
      position: relative;
      z-index: 2;
      height: 2.75rem;
      overflow-y: hidden;
    }

    .nav-scroller .nav {
      display: flex;
      flex-wrap: nowrap;
      padding-bottom: 1rem;
      margin-top: -1px;
      overflow-x: auto;
      text-align: center;
      white-space: nowrap;
      -webkit-overflow-scrolling: touch;
    }
  </style>
<main class="form-signin w-100 m-auto">
<form method="post" action="/loginok">
  @csrf
<div style="text-align:center;">
  <img class="mb-4" src="/images/bootstrap-logo.svg" alt="" width="72" height="57">
  <h1 class="h3 mb-3 fw-normal">로그인 페이지</h1>
</div>
  <div class="form-floating">
    <input type="email" name="email" class="form-control" id="floatingInput" placeholder="name@example.com"
 value="{{ old('email') }}"
>
    <label for="floatingInput">아이디(이메일)</label>
  </div>
  <div class="form-floating">
    <input type="password" name="passwd" class="form-control" id="floatingPassword" placeholder="Password">
    <label for="floatingPassword">암호</label>
  </div>

  <div class="checkbox mb-3">
    <label>
      <input type="checkbox" value="1" name="remember"> Remember me
    </label>
  </div>
  <button class="w-100 btn btn-lg btn-primary" type="submit">로그인</button>

</form>
</main>
@if ($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
@if(Session::has('loginFail'))
  <script type="text/javascript" >
    alert("{{ session()->get('loginFail') }}");
  </script>
@endif
@endsection