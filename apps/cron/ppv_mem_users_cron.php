<?php

//header('Access-Control-Allow-Origin: *');
class ppv_mem_users_cron {
    
    //run cron api
    public function run() {
        $this->curl_request();
    }

    public function curl_request() {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://api.streamingmediahosting.com/index.php/api_dev/mem_user/logout_inactive_users?format=json");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

}

$pmuc = new ppv_mem_users_cron();
$pmuc->run();
?>
