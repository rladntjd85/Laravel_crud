<?php

use App\Models\FileTables;

function disptime($regdate){

    $sec = strtotime(date("Y-m-d H:i:s")) - strtotime($regdate);
    if ($sec < 60) {
        $dispdates = $sec."초 전";
    } else if ($sec > 60 && $sec < 3600) {
        $f = floor($sec / 60);
        $dispdates = $f."분 전";
    } else if ($sec > 3600 && $sec < 86400) {
        $f = floor($sec / 3600);
        $dispdates = $f."시간 전";
    } else {
        $dispdates = date("Y-m-d",strtotime($regdate));
    }

    return $dispdates;

}

function dispmemo($memo_cnt, $memo_date){
    if((time()-strtotime($memo_date))<86400){
        return "<span style='color:red;'>[".$memo_cnt."]</span>";
    }else{
        return null;
    }
}

function dispnew($regdate){
    if((time()-strtotime($regdate))<86400){
        return "<span style='color:red;'>New</span>";
    }else{
        return null;
    }
}

function dispattach($bid){
    $attaches = FileTables::where('pid',$bid)->whereIn('code',['boardattach','editorattach'])->where('status',1)->first();
    if($attaches){
        return '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-card-image" viewBox="0 0 16 16">
        <path d="M6.002 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0"/>
        <path d="M1.5 2A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2zm13 1a.5.5 0 0 1 .5.5v6l-3.775-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12v.54L1 12.5v-9a.5.5 0 0 1 .5-.5z"/>
      </svg>';
    }else{
        return null;
    }
}

?>