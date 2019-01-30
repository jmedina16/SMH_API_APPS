<?php

ini_set('log_errors', true);
ini_set('error_log', dirname(__FILE__) . '/push.log');

class push_cron {

    protected $link = null;
    protected $login;
    protected $password;
    protected $database;
    protected $hostname;
    protected $port;
    protected $queuedEntries;
    protected $bsfPush;

    public function __construct() {
        $this->login = 'smh_mngmt';
        $this->password = '*AC54418D19B5CA7E6195A83CBA66B843ED7CC16C';
        $this->database = 'smh_management';
        $this->hostname = '127.0.0.1';
        $this->port = '3306';
        $this->bsfPush = array(13373, 13438, 12773, 10012, 13453);
    }

    public function run() {
        $this->get_queued_entries();
        $this->push_notification();
    }

    public function get_queued_entries() {
        $this->connect();
        try {
            $this->queuedEntries = $this->link->prepare("SELECT * FROM push_notifications_v2 WHERE sent = 0");
            $this->queuedEntries->execute();
        } catch (PDOException $e) {
            error_log("[push->get_queued_entries] ERROR: " . json_encode($e->getMessage()));
        }
    }

    public function push_notification() {
        $flavors_na = array(3, -1, 4);
        $flavor_status = array();
        $entries = array();
        foreach ($this->queuedEntries->fetchAll(PDO::FETCH_OBJ) as $row) {
            if (in_array($row->partner_id, $this->bsfPush)) {
                $ks = $this->impersonate($row->partner_id);
                $flavor = $this->get_flavor($ks, $row->assetId);
                if (!$flavor['isOriginal']) {
                    if ($flavor['status'] === 2) {
                        $this->bsfPush($flavor['partnerId'], $flavor['entryId'], $ks, $flavor);
                    } else if (in_array((int) $flavor['status'], $flavors_na)) {
                        $this->removeFlavor($flavor['partnerId'], $flavor['entryId'], $flavor['id']);
                    }
                }
            }
        }
    }

    public function removeFlavor($pid, $eid, $fid) {
        $this->connect();
        try {
            $query = $this->link->prepare("DELETE FROM push_notifications_v2 WHERE partner_id = $pid AND entryId = '$eid' AND assetId = '$fid'");
            $query->execute();
        } catch (PDOException $e) {
            error_log("[push->removeFlavor] ERROR: " . json_encode($e->getMessage()));
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
            error_log("[push->connect] ERROR: Cannot connect to database: " . json_encode($e->getMessage()));
        }
    }

    public function update_push_notify($pid, $eid, $fid) {
        $this->connect();
        $data = array(':partner_id' => $pid, ':entryId' => $eid, ':assetId' => $fid, ':updated_at' => date('Y-m-d H:i:s'));
        try {
            $query = $this->link->prepare("UPDATE push_notifications_v2 SET status = 2, sent = 1, updated_at = :updated_at WHERE partner_id = :partner_id AND entryId = :entryId AND assetId = :assetId");
            $query->execute($data);
        } catch (PDOException $e) {
            error_log("[push->update_push_notify] ERROR: Could not execute query: " . json_encode($e->getMessage()));
        }
    }

    public function getMimeType($filename) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $filename);
        finfo_close($finfo);
        return $mime;
    }

    public function bsfPush($pid, $eid, $ks, $flavor) {
        $entry = $this->get_entry_details($ks, $eid);
        $final_push_data = array();
        $root_fileType = '';
        if ($entry['mediaType'] === 1) {
            $root_fileType = 'video';
        } else if ($entry['mediaType'] === 5) {
            $root_fileType = 'audio';
        } else if ($entry['mediaType'] === 2) {
            $root_fileType = 'image';
        }

        $final_push_data['partner_id'] = $pid;
        $final_push_data['entry_id'] = $eid;
        $final_push_data['status'] = $entry['status'];
        $final_push_data['fileType'] = $root_fileType;
        $final_push_data['isSource'] = $flavor['isOriginal'];
        $flavors_tags = explode(',', $flavor['tags']);
        if (in_array('audio', $flavors_tags)) {
            $fileType = 'audio';
        } else {
            $fileType_pre = $this->getMimeType('/opt/kaltura/web/content/entry/data/' . $pid . '/' . $eid . '_' . $flavor['id'] . '_' . $flavor['version'] . '.' . $flavor['fileExt']);
            if (strpos($fileType_pre, 'video') !== false) {
                $fileType = 'video';
            } else if (strpos($fileType_pre, 'audio') !== false || $flavor['fileExt'] === 'mp3') {
                $fileType = 'audio';
            } else if (strpos($fileType_pre, 'image') !== false) {
                $fileType = 'image';
            }
        }
        $final_push_data['flavor'] = array('id' => $flavor['id'], 'width' => $flavor['width'], 'height' => $flavor['height'], 'bitrate' => $flavor['bitrate'], 'isWeb' => $flavor['isWeb'], 'status' => $flavor['status'], 'size' => $flavor['size'], 'fileExt' => $flavor['fileExt'], 'fileType' => $fileType, 'version' => $flavor['version']);

        $notification_url = '';
        $response = '';

        if ((int) $pid === 13373) {
            $json_str = json_encode($final_push_data);
            $notification_url = 'https://api.mybsf.org/completeLectureProcess/1.0.0';
            $response = $this->curlPostJsonBSF4($notification_url, $json_str);
        } else if ((int) $pid === 13453) {
            $json_str = "jsonStr=" . json_encode($final_push_data);
            $notification_url = 'https://prodlr70.bsfinternational.org/api/jsonws/media.buildmediarecords/smh-processing-complete/';
            $response = $this->curlPostJsonBSF1($notification_url, $json_str);
        } else if ((int) $pid === 13438) {
            $json_str = json_encode($final_push_data);
            $notification_url = 'https://uatapi.mybsf.org/completeLectureProcess/1.0.0';
            $response = $this->curlPostJsonBSF3($notification_url, $json_str);
        } else if ((int) $pid === 12773) {
            $json_str = json_encode($final_push_data);
            $notification_url = 'https://cicdapi.mybsf.org/completeLectureProcess/1.0.0';
            $response = $this->curlPostJsonBSF2($notification_url, $json_str);
        } else if ((int) $pid === 10012) {
            $json_str = json_encode($final_push_data);
            error_log("[push->bsfPush] (10012) JSON: " . $json_str);
            $response = 200;
        }

        if ($response === 200 || $response === 202) {
            $this->update_push_notify($pid, $eid, $flavor['id']);
        }
    }

    public function curlPostJsonBSF1($url, $data) {
        error_log("[push->curlPostJsonBSF1] (13453) URL: " . json_encode($url));
        error_log("[push->curlPostJsonBSF1] (13453) JSON: " . json_encode($data));
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
        error_log("[push->curlPostJsonBSF1] (13453) curlStatus: " . json_encode($status));
        if ($ch_error) {
            error_log("[push->curlPostJsonBSF1] (13453) curlError: " . json_encode($ch_error));
        }
        error_log("[push->curlPostJsonBSF1] (13453) curlResponse: " . json_encode($response));
        curl_close($ch);

        return $status;
    }

    public function curlPostJsonBSF2($url, $data) {
        error_log("[push->curlPostJsonBSF2] (12773) URL: " . json_encode($url));
        error_log("[push->curlPostJsonBSF2] (12773) JSON: " . json_encode($data));
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
            'Authorization: Bearer c9deb2ff-552e-35fa-a2d8-a0680ec505ba'
        ));
        $response = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $info = curl_getinfo($ch);
        $ch_error = curl_error($ch);
        error_log("[push->curlPostJsonBSF2] (12773) curlInfo: " . json_encode($info));
        error_log("[push->curlPostJsonBSF2] (12773) curlStatus: " . json_encode($status));
        if ($ch_error) {
            error_log(" [push->curlPostJsonBSF2] (12773) curlError: " . json_encode($ch_error));
        }
        error_log("[push->curlPostJsonBSF2] (12773) curlResponse: " . json_encode($response));
        curl_close($ch);

        return $status;
    }

    public function curlPostJsonBSF3($url, $data) {
        error_log("[push->curlPostJsonBSF3] (13438) URL: " . json_encode($url));
        error_log("[push->curlPostJsonBSF3] (13438) JSON: " . json_encode($data));
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
        error_log("[push->curlPostJsonBSF3] (13438) curlInfo: " . json_encode($info));
        error_log("[push->curlPostJsonBSF3] (13438) curlStatus: " . json_encode($status));
        if ($ch_error) {
            error_log(" [push->curlPostJsonBSF3] (13438) curlError: " . json_encode($ch_error));
        }
        error_log("[push->curlPostJsonBSF3] (13438) curlResponse: " . json_encode($response));
        curl_close($ch);

        return $status;
    }

    public function curlPostJsonBSF4($url, $data) {
        error_log("[push->curlPostJsonBSF4] (13373) URL: " . json_encode($url));
        error_log("[push->curlPostJsonBSF4] (13373) JSON: " . json_encode($data));
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
            'Authorization: Bearer eac98a4f-47b1-3f14-98bd-7edec44d799f'
        ));
        $response = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $info = curl_getinfo($ch);
        $ch_error = curl_error($ch);
        error_log("[push->curlPostJsonBSF4] (13373) curlInfo: " . json_encode($info));
        error_log("[push->curlPostJsonBSF4] (13373) curlStatus: " . json_encode($status));
        if ($ch_error) {
            error_log("[push->curlPostJsonBSF4] (13373) curlError: " . json_encode($ch_error));
        }
        error_log("[push->curlPostJsonBSF4] (13373) curlResponse: " . json_encode($response));
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

    public function get_flavor($ks, $fid) {
        $url = "https://mediaplatform.streamingmediahosting.com/api_v3/";
        $data = array(
            "service" => "flavorAsset",
            "action" => "get",
            "ks" => $ks,
            "id" => $fid,
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