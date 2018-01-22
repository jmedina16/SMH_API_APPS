<?php

class push {

    protected $post_data;

    public function run() {
        $this->post_data = $_POST;
        $this->push_notification();
    }

    public function push_notification() {
        $bsfPush = array(13373);
        if (in_array($this->post_data['partner_id'], $bsfPush)) {
            $this->bsfPush();
        }
    }

    public function bsfPush() {
        $ks = $this->impersonate($this->post_data['partner_id']);
        $flavors_response = $this->get_flavors($ks, $this->post_data['entry_id']);
        $flavor = array();
        foreach ($flavors_response['objects'] as $flavors) {
            array_push($flavor, array('id' => $flavors['id'], 'width' => $flavors['width'], 'height' => $flavors['height'], 'bitrate' => $flavors['bitrate'], 'isSource' => $flavors['isOriginal'], 'isWeb' => $flavors['isWeb'], 'status' => $flavors['status'], 'size' => $flavors['size'], 'fileExt' => $flavors['fileExt'], 'version' => $flavors['version']));
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

        //$notification_url = 'http://clients.streamingmediahosting.com/medina/demos/listener/sync.php';
        $notification_url = 'https://prodlr70.bsfinternational.org/api/jsonws/media.buildmediarecords/smh-processing-complete/';
        $response = $this->curlPostJson($notification_url, $json_str);
    }

    public function curlPostJson($url, $data) {
        syslog(LOG_NOTICE, "SMH DEBUG : curlPostJson1: " . print_r($data, true));
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_USERPWD, "test@liferay.com:test");
        $response = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $ch_error = curl_error($ch);
        syslog(LOG_NOTICE, "SMH DEBUG : curlStatus: " . $status);
        if ($ch_error) {
            syslog(LOG_NOTICE, "SMH DEBUG : curlError: " . print_r($ch_error, true));
        }
        syslog(LOG_NOTICE, "SMH DEBUG : curlPostJson2: " . print_r($response, true));
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