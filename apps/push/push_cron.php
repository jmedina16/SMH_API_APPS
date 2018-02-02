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
        $bsfPush = array(13373);
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
                array_push($entries, array('partner_id' => $row->partner_id, 'entryId' => $row->entryId, 'flavor_status' => $flavor_status));
            }
        }

        foreach ($entries as $entry) {
            if (!array_intersect($entry['flavor_status'], $flavors_not_ready)) {
                $this->bsfPush($entry['partner_id'], $entry['entryId']);
            }
        }
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

    public function update_push_notify($pid, $eid) {
        $this->connect();
        $data = array(':partner_id' => $pid, ':entryId' => $eid, ':updated_at' => date('Y-m-d H:i:s'));
        try {
            $query = $this->link->prepare("UPDATE push_notifications SET sent = 1, updated_at = :updated_at WHERE partner_id = :partner_id AND entryId = :entryId");
            $query->execute($data);
        } catch (PDOException $e) {
            $date = date('Y-m-d H:i:s');
            print($date . " [push->update_push_notify] ERROR: Could not execute query (update_push_notify): " . $e->getMessage() . "\n");
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

    public function bsfPush($pid, $eid) {
        $ks = $this->impersonate($pid);
        $entry = $this->get_entry_details($ks, $eid);
        $flavors_response = $this->get_flavors($ks, $eid);
        $flavor = array();
        foreach ($flavors_response['objects'] as $flavors) {
            array_push($flavor, array('id' => $flavors['id'], 'width' => $flavors['width'], 'height' => $flavors['height'], 'bitrate' => $flavors['bitrate'], 'isSource' => $flavors['isOriginal'], 'isWeb' => $flavors['isWeb'], 'status' => $flavors['status'], 'size' => $flavors['size'], 'fileExt' => $flavors['fileExt'], 'version' => $flavors['version']));
        }

        $final_push_data = array();
        $final_push_data['partner_id'] = $pid;
        $final_push_data['entry_id'] = $eid;
        $final_push_data['name'] = $entry['name'];
        $final_push_data['tags'] = $entry['tags'];
        $final_push_data['thumbnail_url'] = $entry['thumbnailUrl'];
        $final_push_data['partner_data'] = $entry['partnerData'];
        $final_push_data['status'] = $entry['status'];
        $final_push_data['flavors'] = $flavor;

        $json_str = "jsonStr='" . json_encode($final_push_data) . "'";
        //syslog(LOG_NOTICE, "SMH DEBUG : bsfPush: " . print_r($json_str, true));
        $notification_url = 'http://clients.streamingmediahosting.com/medina/demos/listener/sync.php';
        //$notification_url = 'https://prodlr70.bsfinternational.org/api/jsonws/media.buildmediarecords/smh-processing-complete/';
        $response = $this->curlPostJson($notification_url, $json_str);
        if ($response === 200) {
            $this->update_push_notify($pid, $eid);
        }
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

        return $status;
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

    public function get_entry_details($ks, $eid) {
        $url = "https://mediaplatform.streamingmediahosting.com/api_v3/";
        $data = array(
            "service" => "baseEntry",
            "action" => "get",
            "ks" => $ks,
            "entryId" => $eid,
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