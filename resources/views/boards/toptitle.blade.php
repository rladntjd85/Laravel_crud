<?php
    if($multi=="free"){
        $boardtitle="자유";
    }else if($multi=="humor"){
        $boardtitle="유머";
    }
?>
<div class="d-flex flex-column flex-md-row align-items-center pb-3 mb-4 border-bottom">
    <span class="fs-4">{{ $boardtitle." ".$toptitle }}</span>
    <nav class="d-inline-flex mt-2 mt-md-0 ms-md-auto">
    @guest()
        <a href="{{route('login')}}" class="text-xl">로그인</a> /
        <a href="{{route('auth.signup')}}" class="text-xl">회원가입</a>
    @endguest
    @auth()
        <form action="/logout" method="post" class="inline-block">
            @csrf
            <span class="text-xl text-blue-500">{{auth()->user()->userid}}</span> /
            <a href="/logout"><button class="text-xl">로그아웃</button></a>
        </form>
    @endauth
    </nav>
</div>