<?php

//header('Access-Control-Allow-Origin: *');
class playlist {

    protected $pid;
    protected $action;

    public function __construct() {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method == 'POST') {
            isset($_POST["pid"]) ? $this->pid = $_POST["pid"] : $this->pid = '';
            isset($_POST["action"]) ? $this->action = $_POST["action"] : $this->action = '';
        } elseif ($method == 'GET') {
            isset($_GET["pid"]) ? $this->pid = $_GET["pid"] : $this->pid = '';
            isset($_GET["action"]) ? $this->action = $_GET["action"] : $this->action = '';
        }
    }

    //run ppv api
    public function run() {
        switch ($this->action) {
            case "has_ac_rules":
                $this->has_ac_rules();
                break;
            default:
                echo "Action not found!";
        }
    }

    public function curl_request($args) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://mediaplatform.streamingmediahosting.com/api_v3/index.php?service=accessControlProfile&action=list&format=1&" . $args);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        $result = json_decode($output, true);
        curl_close($ch);
        $bool = false;
        foreach ($result['objects'] as $object) {
            if ($object['isDefault']) {
                $bool = (count($object['rules']) > 0) ? 'true' : 'false';
            }
        }
        return $bool;
    }

    public function has_ac_rules() {
        $eid = urlencode($_GET['eid']);
        $ks = $this->impersonate($_GET["pid"]);
        $args = "entryId=" . $eid . "&ks=" . $ks;
        echo $this->curl_request($args);
    }

    public function impersonate($pid) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://mediaplatform.streamingmediahosting.com/api_v3/index.php?service=baseEntry&action=get&service=session&action=impersonate&secret=68b329da9893e34099c7d8ad5cb9c940&type=2&partnerId=-2&expiry=60&impersonatedPartnerId=" . $pid);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        $xml = new SimpleXmlElement($output);
        $smh_ks = (string) $xml->result[0];
        return $smh_ks;
    }

}

$playlist = new playlist();
$playlist->run();
?>
