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
        $bsfPush = array(13373, 13438, 12773, 10012, 13453);
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

    public function getMimeType($filename) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $filename);
        finfo_close($finfo);
        return $mime;
    }

    public function bsfPush($pid, $eid) {
        $ks = $this->impersonate($pid);
        $entry = $this->get_entry_details($ks, $eid);
        $flavors_response = $this->get_flavors($ks, $eid);
        $flavor = array();
        $root_fileType = '';
        //$fileType = '';
        foreach ($flavors_response['objects'] as $flavors) {
            if ($flavors['status'] === 2) {
                $flavors_tags = explode(',', $flavors['tags']);
                if (in_array('audio', $flavors_tags)) {
                    $fileType = 'audio';
                } else {
                    $fileType_pre = $this->getMimeType('/opt/kaltura/web/content/entry/data/' . $pid . '/' . $eid . '_' . $flavors['id'] . '_' . $flavors['version'] . '.' . $flavors['fileExt']);
                    if (strpos($fileType_pre, 'video') !== false) {
                        $fileType = 'video';
                    } else if (strpos($fileType_pre, 'audio') !== false) {
                        $fileType = 'audio';
                    } else if (strpos($fileType_pre, 'image') !== false) {
                        $fileType = 'image';
                    }
                }
                $hlsPlayback = 'https://secure.streamingmediahosting.com/8019BC0/nginxtransmux/' . $pid . '/' . $eid . '_' . $flavors['id'] . '_' . $flavors['version'] . '.' . $flavors['fileExt'] . '/index.m3u8';
                $httpPlayback = 'https://secure.streamingmediahosting.com/8019BC0/content/ec/' . $pid . '/' . $eid . '_' . $flavors['id'] . '_' . $flavors['version'] . '.' . $flavors['fileExt'];
            } else {
                //$fileType = null;
                $hlsPlayback = null;
                $httpPlayback = null;
            }

            array_push($flavor, array('id' => $flavors['id'], 'width' => $flavors['width'], 'height' => $flavors['height'], 'bitrate' => $flavors['bitrate'], 'isSource' => $flavors['isOriginal'], 'isWeb' => $flavors['isWeb'], 'status' => $flavors['status'], 'size' => $flavors['size'], 'fileExt' => $flavors['fileExt'], 'fileType' => $fileType, 'hlsPlayback' => $hlsPlayback, 'httpPlayback' => $httpPlayback, 'version' => $flavors['version']));
        }

        if ($entry['mediaType'] === 1) {
            $root_fileType = 'video';
        } else if ($entry['mediaType'] === 5) {
            $root_fileType = 'audio';
        } else if ($entry['mediaType'] === 2) {
            $root_fileType = 'image';
        }

        $final_push_data = array();
        $final_push_data['partner_id'] = $pid;
        $final_push_data['entry_id'] = $eid;
        $final_push_data['name'] = $entry['name'];
        $final_push_data['tags'] = $entry['tags'];
        $final_push_data['thumbnail_url'] = str_replace("mediaplatform", "ecimages", $entry['thumbnailUrl']);
        $final_push_data['partner_data'] = $entry['partnerData'];
        $final_push_data['status'] = $entry['status'];
        $final_push_data['fileType'] = $root_fileType;
        $final_push_data['flavors'] = $flavor;

        $notification_url = '';
        $response = '';

        if ((int) $pid === 13373) {
            $json_str = "jsonStr=" . json_encode($final_push_data);
            $notification_url = 'https://prodlr70.bsfinternational.org/api/jsonws/media.buildmediarecords/smh-processing-complete/';
            $response = $this->curlPostJsonBSF1($notification_url, $json_str);
        } else if ((int) $pid === 13453) {
            $json_str = json_encode($final_push_data);
            $notification_url = 'https://prodlr70.bsfinternational.org/api/jsonws/media.buildmediarecords/smh-processing-complete/';
            $response = $this->curlPostJsonBSF1($notification_url, $json_str);
        } else if ((int) $pid === 13438) {
            $json_str = json_encode($final_push_data);
            $notification_url = 'https://uatapi.mybsf.org:8243/completeLectureProcess/1.0.0';
            $response = $this->curlPostJsonBSF3($notification_url, $json_str);
        } else if ((int) $pid === 10012 || (int) $pid === 12773) {
            $json_str = json_encode($final_push_data);
            syslog(LOG_NOTICE, "SMH DEBUG : bsfPush: " . print_r($final_push_data, true));
            $response = 200;
        }

        if ($response === 200 || $response === 202) {
            $this->update_push_notify($pid, $eid);
        }
    }

    public function curlPostJsonBSF1($url, $data) {
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

    public function curlPostJsonBSF2($url, $data) {
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
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Bearer 49be2a3b-5094-3540-931c-526d062d0bbd'
        ));
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

    public function curlPostJsonBSF3($url, $data) {
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
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Bearer 64ad83f3-3e3c-3a2d-9f9d-3ef42a7ea7c3'
        ));
        $response = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $info = curl_getinfo($ch);
        $ch_error = curl_error($ch);
        syslog(LOG_NOTICE, "SMH DEBUG : curlInfo: " . print_r($info, true));
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
            "service" => "media",
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