<?php

class uploadQueue {

    protected $notify_type;
    protected $partner_id;
    protected $entry_id;
    protected $status;

    public function __construct() {
        syslog(LOG_NOTICE, "SMH DEBUG : curl_request1: " . print_r($_POST, true));
        syslog(LOG_NOTICE, "SMH DEBUG : curl_request2: " . print_r($_POST["notification_type"], true));
        $this->notify_type = $_POST["notification_type"];
        $this->partner_id = $_POST["partner_id"];
        $this->entry_id = $_POST["entry_id"];
        $this->status = $_POST["status"];
        //syslog(LOG_NOTICE, "SMH DEBUG : notify_type: " . print_r($this->notify_type, true));
    }

    //run api
    public function run() {
        syslog(LOG_NOTICE, "SMH DEBUG : notify_type: " . print_r($this->notify_type, true));
        syslog(LOG_NOTICE, "SMH DEBUG : status: " . print_r($this->status, true));
        if ($this->notify_type == 'entry_update' && $this->status == 2) {
            $this->curl_request($this->partner_id, $this->entry_id);
        }
    }

    public function curl_request($pid, $eid) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://devplatform.streamingmediahosting.com/apps/sn/v1.0/dev.php?action=add_to_upload_queue&pid=" . $pid . "&eid=" . $eid);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        syslog(LOG_NOTICE, "SMH DEBUG : curl_request: " . print_r($output, true));
        return $output;
    }

}

$upload = new uploadQueue();
$upload->run();
?>