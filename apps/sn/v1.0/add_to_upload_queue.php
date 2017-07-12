<?php

class uploadQueue {

    protected $notify_type;
    protected $partner_id;
    protected $entry_id;
    protected $status;
    protected $media_type;

    public function __construct() {
        $this->notify_type = $_POST["notification_type"];
        $this->partner_id = $_POST["partner_id"];
        $this->entry_id = $_POST["entry_id"];
        $this->status = $_POST["status"];
        $this->media_type = $_POST["media_type"];
    }

    //run api
    public function run() {
        if ($this->notify_type == 'entry_add' && $this->status == 7 && $this->media_type == 1) {
            $this->curl_request($this->partner_id, $this->entry_id);
        }
    }

    public function curl_request($pid, $eid) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://mediaplatform.streamingmediahosting.com/apps/sn/v1.0/index.php?action=add_to_upload_queue&pid=" . $pid . "&eid=" . $eid);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

}

$upload = new uploadQueue();
$upload->run();
?>