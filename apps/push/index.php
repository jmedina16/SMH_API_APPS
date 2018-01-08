<?php

class push {

    protected $post_data;

    public function run() {
        $this->post_data = $_POST;
        $this->push_notification();
    }

    public function push_notification() {
        $ks = $this->impersonate($this->post_data['partner_id']);
        $flavors_response = $this->get_flavors($ks, $this->post_data['entry_id']);
        foreach($flavors_response['objects'] as $flavors){
           syslog(LOG_NOTICE, "SMH DEBUG : push_notification: " . print_r($flavors['id'], true)); 
        }
        
        $url = '';
        $notification_url = 'http://clients.streamingmediahosting.com/medina/demos/listener/sync.php';
    }

    public function curlPost($url, $data) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    public function get_flavors($ks, $eid) {
        $url = "https://mediaplatform.streamingmediahosting.com/api_v3/";
        $data = array(
            "service" => "flavorAsset",
            "action" => "list",
            "ks" => $ks,
            "filter:objectType" => "KalturaAssetFilter",
            "filter:entryIdEqual" => $eid,
            "format" => 1
        );

        $response = $this->curlPost($url, $data);
        return $response;
    }

    public function impersonate($pid) {
        $url = "https://mediaplatform.streamingmediahosting.com/api_v3/";
        $data = array(
            "service" => "session",
            "action" => "impersonate",
            "secret" => "68b329da9893e34099c7d8ad5cb9c940",
            "type" => "2",
            "partnerId" => "-2",
            "impersonatedPartnerId" => $pid,
            "expiry" => "60",
            "format" => 1
        );

        $response = $this->curlPost($url, $data);
        return $response;
    }

}

$push = new push();
$push->run();
?>