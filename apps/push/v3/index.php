<?php

ini_set('log_errors', true);
ini_set('error_log', dirname(__FILE__) . '/push.log');

include(dirname(__FILE__) . '/BsfFlavorSelector.php');

class push {

    protected $post_data;
    protected $link = null;
    protected $login;
    protected $password;
    protected $database;
    protected $hostname;
    protected $port;
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
        $this->post_data = $_POST;
        $this->pushNotification();
    }

    public function pushNotification() {
        if (in_array($this->post_data['partner_id'], $this->bsfPush)) {
            $this->insertPushNotify();
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

    public function insertPushNotify() {
        date_default_timezone_set("UTC");
        $this->connect();
        $flavors_na = array(3, -1, 4);
        $ks = $this->impersonate($this->post_data['partner_id']);
        $flavors_response = $this->getFlavors($ks, $this->post_data['entry_id']);
        foreach ($flavors_response['objects'] as $flavor) {
            $entry_exists = $this->entryExists($flavor['entryId'], $flavor['id']);
            if (!$entry_exists['success']) {
                if (!in_array((int) $flavor['status'], $flavors_na)) {
                    if ($flavor['isOriginal']) {
                        $payload = $this->buildPayload($flavor['partnerId'], $flavor['entryId'], $ks, $flavor);
                        $bsfFlavorSelector = new BsfFlavorSelector($this->link,$ks,$payload);
                        $bsfFlavorSelector->convertFlavors();
                        $this->bsfPush($flavor['partnerId'], $payload);
                        $data = array(':partner_id' => $this->post_data['partner_id'], ':entryId' => $this->post_data['entry_id'], ':assetId' => $flavor['id'], ':isSource' => $flavor['isOriginal'], ':status' => $flavor['status'], ':sent' => 1, ':created_at' => date('Y-m-d H:i:s'), ':updated_at' => null);
                    } else {
                        $data = array(':partner_id' => $this->post_data['partner_id'], ':entryId' => $this->post_data['entry_id'], ':assetId' => $flavor['id'], ':isSource' => $flavor['isOriginal'], ':status' => $flavor['status'], ':sent' => 0, ':created_at' => date('Y-m-d H:i:s'), ':updated_at' => null);
                    }

                    try {
                        $query = $this->link->prepare("INSERT INTO push_notifications_v2 (partner_id,entryId,assetId,isSource,status,sent,created_at,updated_at) VALUES (:partner_id,:entryId,:assetId,:isSource,:status,:sent,:created_at,:updated_at)");
                        $query->execute($data);
                    } catch (PDOException $e) {
                        error_log("[push->update_push_notify] ERROR: Could not execute query: " . json_encode($e->getMessage()));
                    }
                }
            }
        }
    }

    public function entryExists($eid, $fid) {
        $success = array('success' => false);
        $data = array(':partner_id' => $this->post_data['partner_id'], ':entryId' => $eid, ':assetId' => $fid);
        try {
            $query = $this->link->prepare("SELECT * FROM push_notifications_v2 WHERE partner_id = :partner_id AND entryId = :entryId AND assetId = :assetId");
            $query->execute($data);
            if ($query->rowCount() > 0) {
                $success = array('success' => true);
            }
        } catch (PDOException $e) {
            error_log("[push->entry_exists] ERROR: Could not execute query: " . json_encode($e->getMessage()));
        }
        return $success;
    }

    public function getFlavors($ks, $eid) {
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

    public function getEntryDetails($ks, $eid) {
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

    public function getMimeType($filename) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $filename);
        finfo_close($finfo);
        return $mime;
    }

    public function buildPayload($pid, $eid, $ks, $flavor) {
      $final_push_data = array();
      $root_fileType = '';
      $entry = $this->getEntryDetails($ks, $eid);
      if ($entry['mediaType'] === 1) {
          $root_fileType = 'video';
      } elseif ($entry['mediaType'] === 5) {
          $root_fileType = 'audio';
      } elseif ($entry['mediaType'] === 2) {
          $root_fileType = 'image';
      }

      $final_push_data['partner_id'] = $pid;
      $final_push_data['entry_id'] = $eid;
      $final_push_data['status'] = $entry['status'];
      $final_push_data['fileType'] = $root_fileType;
      $final_push_data['isSource'] = $flavor['isOriginal'];
      if ($flavor['status'] === 2) {
          $flavors_tags = explode(',', $flavor['tags']);
          if (in_array('audio', $flavors_tags)) {
              $fileType = 'audio';
          } else {
              $fileType_pre = $this->getMimeType('/opt/kaltura/web/content/entry/data/' . $pid . '/' . $eid . '_' . $flavor['id'] . '_' . $flavor['version'] . '.' . $flavor['fileExt']);
              if (strpos($fileType_pre, 'video') !== false) {
                  $fileType = 'video';
              } elseif (strpos($fileType_pre, 'audio') !== false || $flavor['fileExt'] === 'mp3') {
                  $fileType = 'audio';
              } elseif (strpos($fileType_pre, 'image') !== false) {
                  $fileType = 'image';
              }
          }
      } else {
          $fileType = null;
      }
      $final_push_data['flavor'] = array('id' => $flavor['id'], 'width' => $flavor['width'], 'height' => $flavor['height'], 'bitrate' => $flavor['bitrate'], 'isWeb' => $flavor['isWeb'], 'status' => $flavor['status'], 'size' => $flavor['size'], 'fileExt' => $flavor['fileExt'], 'fileType' => $fileType, 'version' => $flavor['version']);
      return $final_push_data;
    }

    public function bsfPush($pid, $payload) {
        $success = array('success' => false);
        $notification_url = '';
        $response = '';

        if ((int) $pid === 13373) {
            $json_str = json_encode($payload);
            $notification_url = 'https://api.mybsf.org/completeLectureProcess/1.0.0';
            $response = $this->curlPostJsonBSF4($notification_url, $json_str);
        } elseif ((int) $pid === 13453) {
            $json_str = "jsonStr=" . json_encode($payload);
            $notification_url = 'https://prodlr70.bsfinternational.org/api/jsonws/media.buildmediarecords/smh-processing-complete/';
            $response = $this->curlPostJsonBSF1($notification_url, $json_str);
        } elseif ((int) $pid === 13438) {
            $json_str = json_encode($payload);
            $notification_url = 'https://uatapi.mybsf.org/completeLectureProcess/1.0.0';
            $response = $this->curlPostJsonBSF3($notification_url, $json_str);
        } elseif ((int) $pid === 12773) {
            $json_str = json_encode($payload);
            $notification_url = 'https://cicdapi.mybsf.org/completeLectureProcess/1.0.0';
            $response = $this->curlPostJsonBSF2($notification_url, $json_str);
        } elseif ((int) $pid === 10012) {
            $json_str = json_encode($payload);
            error_log("[push->bsfPush] (10012) JSON: " . $json_str);
            $response = 200;
        }

        if ($response === 200 || $response === 202) {
            $success = array('success' => true);
        }
        return $success = array('success' => false);
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

}

$push = new push();
$push->run();
