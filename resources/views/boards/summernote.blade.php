<meta name="csrf-token" content="{{ csrf_token() }}">
<link href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>

@if($boards)
    <div id="summernote">{!! $boards->content !!}</div>
@else
    <div id="summernote"></div>
@endif

<script>
    $(document).ready(function() {
            var $summernote = $('#summernote').summernote({
            codeviewFilter: false,
            codeviewIframeFilter: true,
            lang: 'ko-KR',
            height: 400,
            callbacks: {
                onImageUpload: function (files) {//이미지등록
                    for(var i=0; i < files.length; i++) {
                        saveFile($summernote, files[i]);
                    }
                   
                }
            }
        });
    });

    function saveFile($summernote, file){
        var pid = parent.$("#pid").val();
        var formData = new FormData();
        formData.append("file", file);
        formData.append("code", "editorattach");
        formData.append("pid", pid);
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            url: '/boards/saveimage',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            type: 'POST',
            success: function (data) {
                if(data.result==-1){
                    alert('용량이 너무크거나 이미지 파일이 아닙니다.');
                    return;
                }else{
                    $('#summernote').summernote('insertImage', '/images/'+data.fn, function ($image) {
                        $image.css('max-width', '100%');
                        $image.css('padding', '10px');
                    });
                }
            }
        });
    }
  </script>