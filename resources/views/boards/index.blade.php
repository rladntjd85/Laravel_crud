@extends('boards.layout')
@section('header')
    @include('boards.toptitle', ['toptitle'=>'게시판 목록', 'multi'=>$multi])
@endsection
@section('content')
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    <div style="text-align:right;">
        @if(auth()->check())
        <a href="/boards/write/{{ $multi }}"><button class="text-xl">등록</button></a>
        @endif
    </div>
    <table class="table table-striped table-hover">
        <colgroup>
            <col width="10%"/>
            <col width="15%"/>
            <col width="45%"/>
            <col width="15%"/>
            <col width="15%"/>
        </colgroup>
        <thead>
        <tr>
            <th scope="col">번호</th>
            <th scope="col">이름</th>
            <th scope="col">제목</th>
            <th scope="col">조회수</th>
            <th scope="col">등록일</th>
        </tr>
        </thead>
        <tbody>
            <?php
                $pagenumber = $_GET["page"]??1;
                $total = $boards->total();
                $idx = $total-(($boards->currentPage()-1) * 20);
            ?>
            @foreach ($boards as $board)
                <tr>
                    <th scope="row">{{ $idx-- }}</th>
                    <td>{{ $board->userid }}</td>
                    <td><a href="/boards/show/{{$board->bid}}/{{$pagenumber}}">{{ $board->subject }}</a> {!! dispattach($board->bid) !!} {!! dispmemo($board->memo_cnt,$board->memo_date) !!} {!! dispnew($board->regdate) !!} </td>
                    <td>{{ number_format($board->cnt) }}</td>
                    <td>{{ disptime($board->regdate) }}</td>
                </tr>
            @endforeach
            @if(!$total)
                <tr>
                    <th scope="row" colspan="5">게시물이 없습니다.</td>
                </tr>
            @endif
        </tbody>
    </table>
    <div>
        {!! $boards->withQueryString()->links() !!}
    </dvi>
@endsection