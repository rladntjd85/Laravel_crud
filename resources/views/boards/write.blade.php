@extends('boards.layout')

@section('header')
    <?php
        if($bid){
            $pid=$bid;
            $btitle = "수정";
        }else{
            $pid=time();
            $btitle = "쓰기";
        }
    ?>
    @include('boards.toptitle', ['toptitle'=>'게시판 '.$btitle, 'multi'=>$multi])
@endsection

@section('content')
<br />
    <form method="post" action="/boards/create" enctype="multipart/form-data">
        @csrf
        @method('post')
        <input type="hidden" name="pid" id="pid" value="{{ $pid }}">
        <input type="hidden" name="bid" id="bid" value="{{ $bid??0 }}">
        <input type="hidden" name="code" id="code" value="boardattach">
        <input type="hidden" name="attcnt" id="attcnt" value="0">
        <input type="hidden" name="imgUrl" id="imgUrl" value="">
        <div class="form-group">
            <div class="col-md-12">
                <input type="text" name="subject" id="subject" class="form-control input-lg" placeholder="제목을 입력하세요." value="{{ $boards->subject??'' }}" />
            </div>
        <br />
        </div>
        <div class="form-group">
            <div class="col-md-12">
                <iframe id="summerframe" src="{{ route('boards.summernote',['multi' => $multi, 'bid' => $bid]) }}" style="width:100%; height:450px; border:none" scrolling = "no"></iframe>
            </div>
        </div>
        <br />
        <div class="form-group">
            @if($attaches)
                <div id="attach_site" class="col-md-12">
                    <div class="row row-cols-1 row-cols-md-6 g-4" id="attachFiles" style="margin-left:0px;">
                        @foreach ($attaches as $att)
                            @if($att)
                                <div id="af_{{ $att->id }}" class='card h-100' style='width:120px;margin-right: 10px;margin-bottom: 10px;'><img src="/images/{{ $att->filename }}" width='100' /><div class='card-body'><button type='button' class='btn btn-warning' onclick="deletefile('{{ $att->filename }}','{{ $att->id }}')">삭제</button></div></div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @else
                <div id="attach_site" class="col-md-12">
                    <div class="row row-cols-1 row-cols-md-6 g-4" id="attachFiles" style="margin-left:0px;">
                    </div>
                </div>
            @endif
            <div class="col-md-12">
                    <input type="file" name="afile" id="afile" multiple accept="image/*" multiple class="form-control" aria-label="Large file input example">
            </div>
        </div>
        <br />
        <br />
        <div class="form-group">
            @if($bid)
                <div class="col-md-12 text-center">
                    <button type="button" name="edit" class="btn btn-primary input-lg" onclick="updatesubmit()">수정</button>
                </div>
            @else
                <div class="col-md-12 text-center">
                    <button type="button" name="edit" class="btn btn-primary input-lg" onclick="sendsubmit()">등록</button>
                </div>
            @endif
        </div>
    </form>
<script>

    $("#afile").change(function(){
        var formData = new FormData();
        var attcnt=$("#attcnt").val();
        var files = $('#afile').prop('files');
        var totcnt=parseInt(attcnt)+parseInt(files.length)

        if(totcnt>5){
            alert('5개까지만 등록할 수 있습니다.');
            return false;
        }

        for(var i=0; i < files.length; i++) {
            attachFile(files[i]);
        }
    });  

    function attachFile(file) {
        var formData = new FormData();
        var pid = $("#pid").val();
        var code = $("#code").val();
        formData.append("file", file);
        formData.append("uptype", "attach");
        formData.append("pid", pid);
        formData.append("code", code);
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
    //          console.log(JSON.stringify(return_data));
                if(return_data.result=='fail'){
                    alert(return_data.msg);
                    return false;
                }else{
                    //var img="<img src='"+data+"' width='50'><br>";
                    var html = "<div id='af_"+return_data.fid+"' class='card h-100' style='width:120px;margin-right: 10px;margin-bottom: 10px;'><img src='/images/"+return_data.fn+"' width='100' /><div class='card-body'><button type='button' class='btn btn-warning' onclick=\"deletefile('"+return_data.fn+"', '"+return_data.fid+"')\">삭제</button></div></div>";
                        $("#attachFiles").append(html);
                   
                    var rcnt=parseInt(attcnt)+1;
                    $("#attcnt").val(rcnt);
                    var attachFile=$("#attachFile").val();
                    if(attachFile){
                        attachFile=attachFile+",";
                    }
                    $("#attachFile").val(attachFile+return_data.fn);
                }
            }
            , beforeSend: function () {
                var width = 0;
                var height = 0;
                var left = 0;
                var top = 0;
                width = 50;
                height = 50;

                top = ( $(window).height() - height ) / 2 + $(window).scrollTop();
                left = ( $(window).width() - width ) / 2 + $(window).scrollLeft();

                if($("#div_ajax_load_image").length != 0) {
                        $("#div_ajax_load_image").css({
                                "top": top+"px",
                                "left": left+"px"
                        });
                        $("#div_ajax_load_image").show();
                }
                else {
                        $('body').append('<div id="div_ajax_load_image" style="position:absolute; top:' + top + 'px; left:' + left + 'px; width:' + width + 'px; height:' + height + 'px; z-index:9999;" class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div>');
                }

        }
            , complete: function () {
                        $("#div_ajax_load_image").hide();
        }
        });

    }

    function deletefile(fn,fid){
        var pid = $("#pid").val();
        var code = $("#code").val();
        var data = {
            fn : fn,
            pid : pid,
            code : code
        };
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: 'post',
            url: '{{ route('boards.deletefile') }}',
            dataType: 'json',
            data: data,
            success: function(data) {
                alert("삭제했습니다.");
                $("#af_"+fid).hide();
            },
            error: function(data) {
                console.log("error" +JSON.stringify(data));
            }
        });
    }


    function sendsubmit(){
        var subject=$("#subject").val();
        //var content=$("#content").val();
        var content=$('#summerframe').get(0).contentWindow.$('#summernote').summernote('code');//iframe에 있는 값을 가져온다
        var pid = $("#pid").val();
        var code = $("#code").val();
        var data = {
            multi : '{{ $multi }}',
            subject : subject,
            content : content,
            pid : pid,
            code : code
        };
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: 'post',
            url: '{{ route('boards.create') }}',
            dataType: 'json',
            enctype: 'multipart/form-data',
            data: data,
            success: function(data) {
                location.href='/boards/show/'+data.bid+'/1';
            },
            error: function(data) {
                console.log("error" +data);
            }
        });
    }

    function updatesubmit(){
        var subject=$("#subject").val();
        //var content=$("#content").val();
        var content=$('#summerframe').get(0).contentWindow.$('#summernote').summernote('code');//iframe에 있는 값을 가져온다
        var bid='{{ $bid }}';
        var data = {
            subject : subject,
            content : content,
            bid : bid
        };
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: 'post',
            url: '{{ route('boards.update') }}',
            dataType: 'json',
            enctype: 'multipart/form-data',
            data: data,
            success: function(data) {
                location.href='/boards/show/'+data.bid+'/1';
            },
            error: function(data) {
                console.log("error" +data);
            }
        });
    }
</script>    
@endsection