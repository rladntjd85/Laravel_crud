@extends('boards.layout')

@section('content')

<a href="{{ route('boards.index') }}">목록</a>
<form action="{{ route('boards.update', $board->id) }}" method="post">
    @csrf
    @method('PUT')
    <table border=1>
        <tr>
            <th>제목</th>
            <td><input type="text" name="subject" value="{{ $board->subject }}"></td>
        </tr>
        <tr>
            <th>내용</th>
            <td><textarea name="content" id="" cols="30" rows="10">{{ $board->content }}</textarea></td>
        </tr>
        <tr>
            <td colspan=2>
                <input type="submit" value="수정">
            </td>
        </tr>

    </table>
</form>

@endsection