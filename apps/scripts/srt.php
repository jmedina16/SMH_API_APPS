<?php

$data = $_POST['data'];
$srt_name = $_POST['name'];

if (strpos($_SERVER['HTTP_USER_AGENT'], "MSIE") !== false) {
    header('Content-Disposition: attachment; filename="'.$srt_name.'.srt"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Content-Transfer-Encoding: binary');
    header('Content-Type: application/text');
    header('Pragma: public');
    header('Content-Length: ' . strlen($data));

} else {
    header('Content-Disposition: attachment; filename="'.$srt_name.'.srt"');
    header('Content-Transfer-Encoding: binary');
    header('Content-Type: application/text');
    header('Expires: 0');
    header('Pragma: no-cache');
    header('Content-Length: ' . strlen($data));
}
exit($data);
?>
