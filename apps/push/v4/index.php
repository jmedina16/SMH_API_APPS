<?php

ini_set('log_errors', true);
ini_set('error_log', dirname(__FILE__) . '/push.log');

include(dirname(__FILE__) . '/BsfFlavorSelector.php');

class push {

    private $post_data = array();
    private $link = null;
    private $login;
    private $password;
    private $database;
    private $hostname;
    private $port;
    private $bsfPush;
    private $bsfNewService;
    private $service_url = 'https://mediaplatform.streamingmediahosting.com/api_v3/';

    public function __construct() {
        $this->login = 'smh_mngmt';
        $this->password = '*AC54418D19B5CA7E6195A83CBA66B843ED7CC16C';
        $this->database = 'smh_management';
        $this->hostname = '127.0.0.1';
        $this->port = '3306';
        $this->bsfPush = array(13373, 13438, 12773, 10012, 13453, 14005, 14010, 14015, 14020);
        $this->bsfNewService = false;
    }

    public function run() {
        $this->post_data = $_POST;
        $this->pushNotification();
    }

    private function pushNotification() {
        if (count($this->post_data)) {
            if (in_array($this->post_data['partner_id'], $this->bsfPush)) {
                $this->insertPushNotify();
            }
        }
    }

    //connect to database
    private function connect() {
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

    private function insertPushNotify() {
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
                        $bsfFlavorSelector = new BsfFlavorSelector($this->link, $ks, $payload);
                        $bsfFlavorSelector->convertFlavors();
                        $this->bsfPush($flavor['partnerId'], $payload);
                        $data = array(':partner_id' => $this->post_data['partner_id'], ':entryId' => $this->post_data['entry_id'], ':assetId' => $flavor['id'], ':isSource' => $flavor['isOriginal'], ':status' => $flavor['status'], ':sent' => 1, ':created_at' => date('Y-m-d H:i:s'), ':updated_at' => null);
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
    }

    private function entryExists($eid, $fid) {
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

    private function getFlavors($ks, $eid) {
        $data = array(
            "service" => "flavorAsset",
            "action" => "list",
            "ks" => $ks,
            "filter:objectType" => "KalturaAssetFilter",
            "filter:entryIdEqual" => $eid,
            "format" => 1
        );

        $response = $this->curlPost($this->service_url, $data);
        return $response;
    }

    private function getEntryDetails($ks, $eid) {
        $data = array(
            "service" => "media",
            "action" => "get",
            "ks" => $ks,
            "entryId" => $eid,
            "format" => 1
        );

        $response = $this->curlPost($this->service_url, $data);
        return $response;
    }

    private function impersonate($pid) {
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

        $response = $this->curlPost($this->service_url, $data);
        return $response;
    }

    private function buildPayload($pid, $eid, $ks, $flavor) {
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

        $final_push_data['flavor'] = array('id' => $flavor['id'], 'width' => $flavor['width'], 'height' => $flavor['height'], 'bitrate' => $flavor['bitrate'], 'videoCodec' => $flavor['videoCodecId'], 'isWeb' => $flavor['isWeb'], 'status' => $flavor['status'], 'size' => $flavor['size'], 'fileExt' => $flavor['fileExt'], 'fileType' => $root_fileType, 'version' => $flavor['version']);
        return $final_push_data;
    }

    private function bsfPush($pid, $payload) {
        $success = array('success' => false);
        $notification_url = '';
        $response = '';

        if ((int) $pid === 13373) {
            $json_str = json_encode($payload);
            if($this->bsfNewService){
                $token_url = 'https://login.microsoftonline.com/3d917cb9-43aa-4c51-ab1f-0cc552d4a6a1/oauth2/v2.0/token';
                $tokenRequestData = array(
                    "grant_type" => "client_credentials",
                    "client_secret" => "XsfhGPRz~_3id-1f.K4EkAO713-yr~B2_3",
                    "client_id" => "d2e3732a-ad50-47e3-a618-f538e630702c",
                    "scope" => "https://bsfmcaiamdev.onmicrosoft.com/1dafa800-a627-4e9a-9334-a37c5ebd832b/.default openid"
                );
                $response = $this->generateBsfToken($token_url, $tokenRequestData);

                if (isset($response['access_token']) && $response['access_token'] != NULL && !empty($response['access_token'])) {
                    $notification_url = 'https://bsf-mca-lecture-process-api-dev.azurewebsites.net/service/v1/smh/process';
                    $response = $this->curlPostJsonBSFNew($pid, $response['access_token'], $notification_url, $json_str);
                } else {
                    error_log("[push->bsfPush] (" . $pid . ") Error: " . json_encode($response));
                }                
            } else {
                $notification_url = 'https://api.mybsf.org/completeLectureProcess/1.0.0';
                $response = $this->curlPostJsonBSF4($notification_url, $json_str);                
            }
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
        } elseif ((int) $pid === 14005) {
            $json_str = json_encode($payload);
            $token_url = 'https://login.microsoftonline.com/3d917cb9-43aa-4c51-ab1f-0cc552d4a6a1/oauth2/v2.0/token';
            $tokenRequestData = array(
                "grant_type" => "client_credentials",
                "client_secret" => "XsfhGPRz~_3id-1f.K4EkAO713-yr~B2_3",
                "client_id" => "d2e3732a-ad50-47e3-a618-f538e630702c",
                "scope" => "https://bsfmcaiamdev.onmicrosoft.com/1dafa800-a627-4e9a-9334-a37c5ebd832b/.default openid"
            );
            $response = $this->generateBsfToken($token_url, $tokenRequestData);

            if (isset($response['access_token']) && $response['access_token'] != NULL && !empty($response['access_token'])) {
                $notification_url = 'https://bsf-mca-lecture-process-api-dev.azurewebsites.net/service/v1/smh/process';
                $response = $this->curlPostJsonBSFNew($pid, $response['access_token'], $notification_url, $json_str);
            } else {
                error_log("[push->bsfPush] (" . $pid . ") Error: " . json_encode($response));
            }
        } elseif ((int) $pid === 14010) {
            $json_str = json_encode($payload);
            $token_url = 'https://login.microsoftonline.com/3d917cb9-43aa-4c51-ab1f-0cc552d4a6a1/oauth2/v2.0/token';
            $tokenRequestData = array(
                "grant_type" => "client_credentials",
                "client_secret" => "BJ6NfdBBSB.4-C218y~.WEHL9f13FM~Y7R",
                "client_id" => "0ec4d0e7-29d0-4ee6-af28-84fae71a96ad",
                "scope" => "https://bsfmcaiamdev.onmicrosoft.com/1dafa800-a627-4e9a-9334-a37c5ebd832b/.default openid"
            );
            $response = $this->generateBsfToken($token_url, $tokenRequestData);

            if (isset($response['access_token']) && $response['access_token'] != NULL && !empty($response['access_token'])) {
                $notification_url = 'https://bsf-mca-lecture-process-api-qa.azurewebsites.net/service/v1/smh/process';
                $response = $this->curlPostJsonBSFNew($pid, $response['access_token'], $notification_url, $json_str);
            } else {
                error_log("[push->bsfPush] (" . $pid . ") Error: " . json_encode($response));
            }
        } elseif ((int) $pid === 14015) {
            $json_str = json_encode($payload);
            $token_url = 'https://login.microsoftonline.com/3d917cb9-43aa-4c51-ab1f-0cc552d4a6a1/oauth2/v2.0/token';
            $tokenRequestData = array(
                "grant_type" => "client_credentials",
                "client_secret" => "~Fw4RQkpM35_I.s291PbJn8FG3JE~9Qito",
                "client_id" => "89ee9c0f-79c3-442b-9389-629dace49707",
                "scope" => "https://bsfmcaiamdev.onmicrosoft.com/1dafa800-a627-4e9a-9334-a37c5ebd832b/.default openid"
            );
            $response = $this->generateBsfToken($token_url, $tokenRequestData);

            if (isset($response['access_token']) && $response['access_token'] != NULL && !empty($response['access_token'])) {
                $notification_url = 'https://bsf-mca-lecture-process-api-sme.azurewebsites.net/service/v1/smh/process';
                $response = $this->curlPostJsonBSFNew($pid, $response['access_token'], $notification_url, $json_str);
            } else {
                error_log("[push->bsfPush] (" . $pid . ") Error: " . json_encode($response));
            }
        } elseif ((int) $pid === 14020) {
            $json_str = json_encode($payload);
            $token_url = 'https://login.microsoftonline.com/3d917cb9-43aa-4c51-ab1f-0cc552d4a6a1/oauth2/v2.0/token';
            $tokenRequestData = array(
                "grant_type" => "client_credentials",
                "client_secret" => "L0X6NB-k_p.vM6SzUMXd1_.G6wL9w0YZ7j",
                "client_id" => "b17c1227-6d2f-4f53-892d-6bbd09b2fed5",
                "scope" => "https://bsfmcaiamdev.onmicrosoft.com/1dafa800-a627-4e9a-9334-a37c5ebd832b/.default openid"
            );
            $response = $this->generateBsfToken($token_url, $tokenRequestData);

            if (isset($response['access_token']) && $response['access_token'] != NULL && !empty($response['access_token'])) {
                $notification_url = 'https://bsf-mca-lecture-process-api-mca1.azurewebsites.net/service/v1/smh/process';
                $response = $this->curlPostJsonBSFNew($pid, $response['access_token'], $notification_url, $json_str);
            } else {
                error_log("[push->bsfPush] (" . $pid . ") Error: " . json_encode($response));
            }
        }

        if ($response === 200 || $response === 202) {
            $success = array('success' => true);
        }
        return $success = array('success' => false);
    }

    private function curlPostJsonBSF1($url, $data) {
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

    private function curlPostJsonBSF2($url, $data) {
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

    private function curlPostJsonBSF3($url, $data) {
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

    private function curlPostJsonBSF4($url, $data) {
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

    private function curlPost($url, $data) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    private function generateBsfToken($url, $data) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/x-www-form-urlencoded'
        ));
        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    private function curlPostJsonBSFNew($pid, $token, $url, $data) {
        error_log("[push->curlPostJsonBSFNew] (" . $pid . ") URL: " . json_encode($url));
        error_log("[push->curlPostJsonBSFNew] (" . $pid . ") JSON: " . json_encode($data));
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
            'Authorization: Bearer ' . $token
        ));
        $response = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $info = curl_getinfo($ch);
        $ch_error = curl_error($ch);
        error_log("[push->curlPostJsonBSFNew] (" . $pid . ") curlInfo: " . json_encode($info));
        error_log("[push->curlPostJsonBSFNew] (" . $pid . ") curlStatus: " . json_encode($status));
        if ($ch_error) {
            error_log("[push->curlPostJsonBSFNew] (" . $pid . ") curlError: " . json_encode($ch_error));
        }
        error_log("[push->curlPostJsonBSFNew] (" . $pid . ") curlResponse: " . json_encode($response));
        curl_close($ch);

        return $status;
    }

}

$push = new push();
$push->run();
