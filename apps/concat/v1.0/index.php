<?php
require 'vendor/autoload.php';

$ffmpeg = FFMpeg\FFMpeg::create();
$video = $ffmpeg->open( '/opt/kaltura/web/content/entry/data/10012/0_23vhczrn_0_9q4f1jgt_2.mp4' );
$format = new FFMpeg\Format\Video\X264();
$format->setAudioCodec("libmp3lame");

$video
    ->concat(array('/opt/kaltura/web/content/entry/data/10012/0_23vhczrn_0_9q4f1jgt_2.mp4', '/opt/kaltura/web/content/entry/data/10012/0_7dfoo2xk_0_hrrufbgp_2.mp4'))
    ->saveFromDifferentCodecs($format, '/opt/kaltura/web/content/entry/data/10012/test.mp4');
?>