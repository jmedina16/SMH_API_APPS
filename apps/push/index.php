<?php

class push {

    protected $post_data;

    public function run() {
        $this->post_data = $_POST;
        $this->push_notification();
    }

    public function push_notification() {
        $bsfPush = array(13373, 10012);
        if (in_array($this->post_data['partner_id'], $bsfPush)) {
            $this->bsfPush();
        }
    }

    public function bsfPush() {
        $ks = $this->impersonate($this->post_data['partner_id']);
        $flavors_response = $this->get_flavors($ks, $this->post_data['entry_id']);
        $flavor = array();
        foreach ($flavors_response['objects'] as $flavors) {
            array_push($flavor, array('id' => $flavors['id'], 'width' => $flavors['width'], 'height' => $flavors['height'], 'bitrate' => $flavors['bitrate'], 'isSource' => $flavors['isOriginal'], 'isWeb' => $flavors['isWeb'], 'status' => $flavors['status'], 'size' => $flavors['size'], 'fileExt' => $flavors['fileExt']));
        }

        $final_push_data = array();
        $final_push_data['partner_id'] = $this->post_data['partner_id'];
        $final_push_data['entry_id'] = $this->post_data['entry_id'];
        $final_push_data['name'] = $this->post_data['name'];
        $final_push_data['tags'] = $this->post_data['tags'];
        $final_push_data['thumbnail_url'] = $this->post_data['thumbnail_url'];
        $final_push_data['partner_data'] = $this->post_data['partner_data'];
        $final_push_data['status'] = $this->post_data['status'];
        $final_push_data['flavors'] = $flavor;

        $json_str = "jsonStr='" . json_encode($final_push_data) . "'";
        
        $json_str_test = 'jsonStr=\'{"partner_id":"13373","entry_id":"0_qwu778pt","name":"SampleVideo_1280x720_1mb","tags":"","thumbnail_url":"https:\/\/mediaplatform.streamingmediahosting.com\/p\/10012\/sp\/1001200\/thumbnail\/entry_id\/0_2us0xt65\/version\/0\/acv\/122","partner_data":"","status":"2","flavors":[{"id":"0_vq7freli","width":1280,"height":720,"bitrate":1590,"isSource":true,"isWeb":true,"status":2,"size":1034,"fileExt":"mp4"},{"id":"0_joj5pyq6","width":640,"height":360,"bitrate":447,"isSource":false,"isWeb":true,"status":2,"size":292,"fileExt":"mp4"},{"id":"0_dusampos","width":848,"height":480,"bitrate":754,"isSource":false,"isWeb":true,"status":2,"size":492,"fileExt":"mp4"},{"id":"0_ogjbtgbr","width":1024,"height":576,"bitrate":1383,"isSource":false,"isWeb":true,"status":2,"size":897,"fileExt":"mp4"},{"id":"0_ltrk6itf","width":1280,"height":720,"bitrate":1415,"isSource":false,"isWeb":true,"status":2,"size":918,"fileExt":"mp4"},{"id":"0_aagdfxk9","width":0,"height":0,"bitrate":128,"isSource":false,"isWeb":true,"status":2,"size":84,"fileExt":"mp3"}]}\'';

        $notification_url = 'http://clients.streamingmediahosting.com/medina/demos/listener/sync.php';
        $response = $this->curlPostJson($notification_url, $json_str_test);
    }

    public function curlPostJson($url, $data) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_USERPWD, "test@liferay.com:test");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data)
        ));
        $response = curl_exec($ch);
        syslog(LOG_NOTICE, "SMH DEBUG : curlPostJson: " . print_r($response, true));
        curl_close($ch);

        return json_decode($response, true);
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