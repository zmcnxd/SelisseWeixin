<?php
// 日志记录
function writelog($str)
{
    $open=fopen("log.txt","a" );
    fwrite($open,$str);
    fclose($open);
}
?>    