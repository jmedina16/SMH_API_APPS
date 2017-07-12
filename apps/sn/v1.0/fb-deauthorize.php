<?php

class deauthorize {

    protected $signed_request;

    public function __construct() {
        $this->signed_request = $_POST["signed_request"];
    }

    //run api
    public function run() {
        $this->curl_request($this->signed_request);
    }

    public function curl_request($signed_request) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://mediaplatform.streamingmediahosting.com/apps/sn/v1.0/index.php?action=facebook_deauthorization&signed_request=" . $signed_request);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

}

$fb = new deauthorize();
$fb->run();
?>