<?php

class push_cron {

    protected $link = null;
    protected $login;
    protected $password;
    protected $database;
    protected $hostname;
    protected $port;
    protected $queuedEntries;

    public function __construct() {
        $this->login = 'smh_mngmt';
        $this->password = '*AC54418D19B5CA7E6195A83CBA66B843ED7CC16C';
        $this->database = 'smh_management';
        $this->hostname = '127.0.0.1';
        $this->port = '3306';
    }

    public function run() {
        $this->get_queued_entries();
        $this->push_notification();
    }

    public function get_queued_entries() {
        $this->connect();
        try {
            $this->queuedEntries = $this->link->prepare("SELECT * FROM push_notifications WHERE sent = 0");
            $this->queuedEntries->execute();
        } catch (PDOException $e) {
            $date = date('Y-m-d H:i:s');
            print($date . " [smhGarbageCollection->get_queued_entries] ERROR: Could not execute query (get_queued_entries): " . $e->getMessage() . "\n");
        }
    }

    public function push_notification() {
        $bsfPush = array(10012);
        $flavors_not_ready = array(1, 9, 7, 0, 5, 8, 6);
        $flavor_status = array();
        $entries = array();
        foreach ($this->queuedEntries->fetchAll(PDO::FETCH_OBJ) as $row) {
            if (in_array($row->partner_id, $bsfPush)) {
                $ks = $this->impersonate($row->partner_id);
                $flavors_response = $this->get_flavors($ks, $row->entryId);
                foreach ($flavors_response['objects'] as $flavors) {
                    array_push($flavor_status, $flavors['status']);
                }
                $entries['partner_id'] = $row->partner_id;
                $entries['entryId'] = $row->entryId;
                $entries['flavor_status'] = $flavor_status;
            }
        }

        syslog(LOG_NOTICE, "SMH DEBUG : push_notification " . print_r($entries, true));

//        if (in_array($this->post_data['partner_id'], $bsfPush)) {
//            $this->bsfPush();
//        } else {
//            $this->insert_push_notify();
//        }
    }

    //connect to database
    public function connect() {
        if (!is_null($this->link)) {
            return;
        }

        try {
            $this->link = new PDO("mysql:host=$this->hostname;port=3306;dbname=$this->database", $this->login, $this->password);
            $this->link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            $date = date('Y-m-d H:i:s');
            syslog(LOG_NOTICE, $date . " [Channel->connect] ERROR: Cannot connect to database: " . print_r($e->getMessage(), true));
        }
    }

    public function insert_push_notify() {
        $this->connect();
        $entry_exists = $this->entry_exists();
        if (!$entry_exists['success']) {
            $data = array(':partner_id' => $this->post_data['partner_id'], ':entryId' => $this->post_data['entry_id'], ':sent' => 0, ':created_at' => date('Y-m-d H:i:s'), 'updated_at' => null);
            try {
                $query = $this->link->prepare("INSERT INTO push_notifications (partner_id,entryId,sent,created_at,updated_at) VALUES (:partner_id,:entryId,:sent,:created_at,:updated_at)");
                $query->execute($data);
            } catch (PDOException $e) {
                $date = date('Y-m-d H:i:s');
                print($date . " [push->insert_push_notify] ERROR: Could not execute query (insert_push_notify): " . $e->getMessage() . "\n");
            }
        }
    }

    public function entry_exists() {
        $success = array('success' => false);
        $data = array(':partner_id' => $this->post_data['partner_id'], ':entryId' => $this->post_data['entry_id']);
        try {
            $query = $this->link->prepare("SELECT * FROM push_notifications WHERE partner_id = :partner_id AND entryId = :entryId");
            $query->execute($data);
            if ($query->rowCount() > 0) {
                $success = array('success' => true);
            }
        } catch (PDOException $e) {
            $date = date('Y-m-d H:i:s');
            print($date . " [push->check_entry] ERROR: Could not execute query (check_entry): " . $e->getMessage() . "\n");
        }
        return $success;
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

$push_cron = new push_cron();
$push_cron->run();
?>