<?php

//header('Access-Control-Allow-Origin: *');
class sn {

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
            case "get_sn_config":
                $this->get_sn_config();
                break;
            case "store_youtube_authorization":
                $this->store_youtube_authorization();
                break;
            case "remove_youtube_authorization":
                $this->remove_youtube_authorization();
                break;
            case "create_sn_livestreams":
                $this->create_sn_livestreams();
                break;
            case 'update_sn_livestreams':
                $this->update_sn_livestreams();
                break;
            case 'update_sn_metadata':
                $this->update_sn_metadata();
                break;
            case 'update_sn_thumbnail':
                $this->update_sn_thumbnail();
                break;
            case 'delete_sn_livestream':
                $this->delete_sn_livestream();
                break;
            case 'get_youtube_broadcast_id':
                $this->get_youtube_broadcast_id();
                break;
            case 'get_sn_livestreams':
                $this->get_sn_livestreams();
                break;
            case 'check_youtube_entries':
                $this->check_youtube_entries();
                break;
            case 'youtube_entry_complete':
                $this->youtube_entry_complete();
                break;
            default:
                echo "Action not found!";
        }
    }

    public function curl_request($action, $args) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://api.streamingmediahosting.com/index.php/api/" . $action . "pid=" . $this->pid . "&format=json&" . $args);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    public function get_sn_config() {
        $ks = urlencode($_GET['ks']);
        $action = "sn_config/get_sn_config?";
        $args = "ks=" . $ks;
        echo $this->curl_request($action, $args);
    }

    public function store_youtube_authorization() {
        $ks = urlencode($_GET['ks']);
        $code = $_GET['code'];
        $action = "sn_config/store_youtube_authorization?";
        $args = "ks=" . $ks . "&code=" . $code;
        echo $this->curl_request($action, $args);
    }

    public function remove_youtube_authorization() {
        $ks = urlencode($_POST['ks']);
        $action = "sn_config/remove_youtube_authorization?";
        $args = "ks=" . $ks;
        echo $this->curl_request($action, $args);
    }

    public function create_sn_livestreams() {
        $ks = urlencode($_POST['ks']);
        $name = urlencode($_POST['name']);
        $desc = urlencode($_POST['desc']);
        $eid = urlencode($_POST['eid']);
        $platforms = urlencode($_POST['platforms']);
        $projection = urlencode($_POST['projection']);
        $action = "sn_config/create_sn_livestreams?";
        $args = "ks=" . $ks . "&name=" . $name . "&desc=" . $desc . "&eid=" . $eid . "&platforms=" . $platforms . '&projection=' . $projection;
        echo $this->curl_request($action, $args);
    }

    public function update_sn_livestreams() {
        $ks = urlencode($_POST['ks']);
        $name = urlencode($_POST['name']);
        $desc = urlencode($_POST['desc']);
        $eid = urlencode($_POST['eid']);
        $platforms = urlencode($_POST['platforms']);
        $projection = urlencode($_POST['projection']);
        $action = "sn_config/update_sn_livestreams?";
        $args = "ks=" . $ks . "&name=" . $name . "&desc=" . $desc . "&eid=" . $eid . "&platforms=" . $platforms . '&projection=' . $projection;
        echo $this->curl_request($action, $args);
    }

    public function update_sn_metadata() {
        $ks = urlencode($_POST['ks']);
        $name = urlencode($_POST['name']);
        $desc = urlencode($_POST['desc']);
        $eid = urlencode($_POST['eid']);
        $action = "sn_config/update_sn_metadata?";
        $args = "ks=" . $ks . "&name=" . $name . "&desc=" . $desc . "&eid=" . $eid;
        echo $this->curl_request($action, $args);
    }

    public function update_sn_thumbnail() {
        $ks = urlencode($_POST['ks']);
        $eid = urlencode($_POST['eid']);
        $action = "sn_config/update_sn_thumbnail?";
        $args = "ks=" . $ks . "&eid=" . $eid;
        echo $this->curl_request($action, $args);
    }

    public function delete_sn_livestream() {
        $ks = urlencode($_POST['ks']);
        $eid = urlencode($_POST['eid']);
        $action = "sn_config/delete_sn_livestream?";
        $args = "ks=" . $ks . "&eid=" . $eid;
        echo $this->curl_request($action, $args);
    }

    public function get_youtube_broadcast_id() {
        $eid = urlencode($_GET['eid']);
        $action = "sn_config/get_youtube_broadcast_id?";
        $args = "eid=" . $eid;
        echo $this->curl_request($action, $args);
    }

    public function get_sn_livestreams() {
        $ks = urlencode($_GET['ks']);
        $eid = urlencode($_GET['eid']);
        $action = "sn_config/get_sn_livestreams?";
        $args = "ks=" . $ks . "&eid=" . $eid;
        echo $this->curl_request($action, $args);
    }

    public function check_youtube_entries() {
        $action = "sn_config/check_youtube_entries?";
        $args = "";
        echo $this->curl_request($action, $args);
    }

    public function youtube_entry_complete() {
        $ks = urlencode($_POST['ks']);
        $eid = urlencode($_POST['eid']);
        $action = "sn_config/youtube_entry_complete?";
        $args = "ks=" . $ks . "&eid=" . $eid;
        echo $this->curl_request($action, $args);
    }

}

$sn = new sn();
$sn->run();
?>
