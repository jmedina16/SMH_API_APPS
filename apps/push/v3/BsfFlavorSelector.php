<?php

class BsfFlavorSelector {

    private $ks;
    private $payload;
    private $db_link;
    private $service_url = 'https://mediaplatform.streamingmediahosting.com/api_v3/';

    public function __construct($db_link, $ks, $payload) {
        $this->db_link = $db_link;
        $this->ks = $ks;
        $this->payload = $payload;
    }

    public function convertFlavors() {
        $flavors = array();
        if ($this->payload['status'] == 2) {
            if ($this->payload['flavor']['fileType'] == 'video') {
                $flavors = $this->getVideoFlavors($this->payload['partner_id'], $this->payload['flavor']['id'], $this->payload['flavor']['height'], $this->payload['flavor']['bitrate'], $this->payload['flavor']['fileExt']);
            } else if ($this->payload['flavor']['fileType'] == 'audio') {
                $flavors = $this->getAudioFlavors($this->payload['partner_id'], $this->payload['flavor']['id'], $this->payload['flavor']['bitrate'], $this->payload['flavor']['fileExt']);
            }
            $flavors_count = count($flavors);
            if ($flavors_count) {
                if ($flavors_count > 1) {
                    $this->convertMultiFlavors($this->ks, $this->payload['entry_id'], $flavors);
                } else {
                    $this->convertSingleFlavor($this->ks, $this->payload['entry_id'], $flavors[0]);
                }
            }
            $this->insertFlavorNotify();
        }
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

    private function entryExists($eid, $fid) {
        $success = array('success' => false);
        $data = array(':partner_id' => $this->payload['partner_id'], ':entryId' => $eid, ':assetId' => $fid);
        try {
            $query = $this->db_link->prepare("SELECT * FROM push_notifications_v2 WHERE partner_id = :partner_id AND entryId = :entryId AND assetId = :assetId");
            $query->execute($data);
            if ($query->rowCount() > 0) {
                $success = array('success' => true);
            }
        } catch (PDOException $e) {
            error_log("[push->entry_exists] ERROR: Could not execute query: " . json_encode($e->getMessage()));
        }
        return $success;
    }

    private function getVideoFlavors($pid, $assetId, $height, $bitrate, $fileExt) {
        $flavors_to_convert = array();
        $audio_flavor = 0;
        if ($pid == 10012) {
            $audio_flavor = 10408;
        } else if ($pid == 13373) {
            $audio_flavor = 10463;
        } else if ($pid == 13438) {
            $audio_flavor = 10498;
        } else if ($pid == 12773) {
            $audio_flavor = 10598;
        } else if ($pid == 13453) {
            $audio_flavor = 10538;
        }
        //convert audio flavor first
        array_push($flavors_to_convert, $audio_flavor);
        if ($fileExt == 'mp4') {
            if ($bitrate >= 1000) {
                switch ($height) {
                    case ($height >= 480):
                        array_push($flavors_to_convert, 10003);
                        break;
                    case ($height >= 352):
                        array_push($flavors_to_convert, 9);
                        break;
                    default:
                        array_push($flavors_to_convert, 16);
                }
            }
        } else {
            if ($bitrate >= 1000) {
                switch ($height) {
                    case ($height >= 720):
                        array_push($flavors_to_convert, 10);
                        array_push($flavors_to_convert, 10003);
                        break;
                    case ($height >= 576):
                        array_push($flavors_to_convert, 10004);
                        array_push($flavors_to_convert, 10003);
                        break;
                    case ($height >= 480):
                        array_push($flavors_to_convert, 10003);
                        break;
                    case ($height >= 352):
                        array_push($flavors_to_convert, 9);
                        break;
                    default:
                        array_push($flavors_to_convert, 16);
                }
            } else {
                switch ($height) {
                    case ($height >= 480):
                        array_push($flavors_to_convert, 10003);
                        break;
                    case ($height >= 352):
                        array_push($flavors_to_convert, 9);
                        break;
                    default:
                        array_push($flavors_to_convert, 16);
                }
            }
        }
        return $flavors_to_convert;
    }

    private function getAudioFlavors($pid, $assetId, $bitrate, $fileExt) {
        $flavors_to_convert = array();
        if ($fileExt != 'mp3') {
            $audio_flavor = 0;
            if ($pid == 10012) {
                $audio_flavor = 10408;
            } else if ($pid == 13373) {
                $audio_flavor = 10463;
            } else if ($pid == 13438) {
                $audio_flavor = 10498;
            } else if ($pid == 12773) {
                $audio_flavor = 10598;
            } else if ($pid == 13453) {
                $audio_flavor = 10538;
            }
            array_push($flavors_to_convert, $audio_flavor);
        }
        return $flavors_to_convert;
    }

    private function insertFlavorNotify() {
        $flavors_na = array(3, -1, 4);
        $flavors_response = $this->getFlavors($this->ks, $this->payload['entry_id']);
        foreach ($flavors_response['objects'] as $flavor) {
            $entry_exists = $this->entryExists($flavor['entryId'], $flavor['id']);
            if (!$entry_exists['success']) {
                if (!in_array((int) $flavor['status'], $flavors_na)) {
                    if (!$flavor['isOriginal']) {
                        $data = array(':partner_id' => $this->payload['partner_id'], ':entryId' => $this->payload['entry_id'], ':assetId' => $flavor['id'], ':isSource' => $flavor['isOriginal'], ':status' => $flavor['status'], ':sent' => 0, ':created_at' => date('Y-m-d H:i:s'), ':updated_at' => null);
                        try {
                            $query = $this->db_link->prepare("INSERT INTO push_notifications_v2 (partner_id,entryId,assetId,isSource,status,sent,created_at,updated_at) VALUES (:partner_id,:entryId,:assetId,:isSource,:status,:sent,:created_at,:updated_at)");
                            $query->execute($data);
                        } catch (PDOException $e) {
                            error_log("[push->update_push_notify] ERROR: Could not execute query: " . json_encode($e->getMessage()));
                        }
                    }
                }
            }
        }
    }

    private function convertSingleFlavor($ks, $eid, $paramsId) {
        $data = array(
            "service" => "flavorAsset",
            "action" => "convert",
            "ks" => $ks,
            "entryId" => $eid,
            "flavorParamsId" => $paramsId,
            "format" => 1
        );

        $response = $this->curlPost($this->service_url, $data);
        return $response;
    }

    private function convertMultiFlavors($ks, $eid, $flavors) {
        $count = 1;
        $data = array();
        $data['service'] = 'multirequest';
        $data['ks'] = $ks;
        $data['format'] = 1;
        foreach ($flavors as $flavor) {
            $data[$count . ':service'] = 'flavorAsset';
            $data[$count . ':action'] = 'convert';
            $data[$count . ':entryId'] = $eid;
            $data[$count . ':flavorParamsId'] = $flavor;
            $count++;
        }
        $response = $this->curlPost($this->service_url, $data);
        return $response;
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

}

?>
