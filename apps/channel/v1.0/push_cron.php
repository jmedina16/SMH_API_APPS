<?php

//header('Access-Control-Allow-Origin: *');
class push {

    //run api
    public function run() {
        $this->curl_request();
    }

    public function curl_request() {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://api.streamingmediahosting.com/index.php/api_dev/channel_config/push_routine");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        echo $output;
    }

}

$push = new push();
$push->run();
?>
