<?php

//header('Access-Control-Allow-Origin: *');
class channel {

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
            case "post_schedule":
                $this->post_schedule();
                break;
            case "get_channels":
                $this->get_channels();
                break;
            case "get_channel_entries":
                $this->get_channel_entries();
                break;
            case "delete_channel":
                $this->delete_channel();
                break;
            case "add_program":
                $this->add_program();
                break;
            case "update_segment":
                $this->update_segment();
                break;
            default:
                echo "Action not found!";
        }
    }

    public function curl_request($action, $args) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://api.streamingmediahosting.com/index.php/api_dev/" . $action . "pid=" . $this->pid . "&format=json&" . $args);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
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

    public function post_schedule() {
        $ks = urlencode($_POST['ks']);
        $action = "channel_config/post_schedule?";
        $args = "ks=" . $ks;
        echo $this->curl_request($action, $args);
    }

    public function get_channels() {
        $ks = urlencode($_GET['ks']);
        $category = urlencode($_GET['category']);
        $ac = urlencode($_GET['ac']);
        $search = urlencode($_GET['search']);
        $action = "channel_config/get_channels?";
        $args = "ks=" . $ks . "&category=" . $category . "&ac=" . $ac . "&search=" . $search;
        $channels = $this->curl_request($action, $args);
        echo json_decode($channels, true);
    }

    public function get_channel_entries() {
        $ks = urlencode($_POST['ks']);
        $start = urlencode($_POST['start']);
        $length = urlencode($_POST['length']);
        $draw = urlencode($_POST['draw']);
        $tz = urlencode($_POST['tz']);
        $cid = urlencode($_POST['cid']);
        $search = urlencode($_POST['search']);
        $action = "channel_config/get_channel_entries?";
        $args = "ks=" . $ks . "&start=" . $start . "&length=" . $length . "&draw=" . $draw . "&tz=" . $tz . "&cid=" . $cid . "&search=" . $search;
        echo $this->curl_request($action, $args);
    }

    public function delete_channel() {
        $ks = urlencode($_POST['ks']);
        $cid = urlencode($_POST['cid']);
        $action = "channel_config/delete_channel?";
        $args = "ks=" . $ks . "&cid=" . $cid;
        echo $this->curl_request($action, $args);
    }

    public function add_program() {
        $ks = urlencode($_POST['ks']);
        $cid = urlencode($_POST['cid']);
        $eid = urlencode($_POST['eid']);
        $start_date = urlencode($_POST['start_date']);
        $end_date = urlencode($_POST['end_date']);
        $repeat = urlencode($_POST['repeat']);
        $rec_type = urlencode($_POST['rec_type']);
        $event_length = urlencode($_POST['event_length']);
        $action = "channel_config/add_program?";
        $args = "ks=" . $ks . "&cid=" . $cid . "&eid=" . $eid . "&start_date=" . $start_date . "&end_date=" . $end_date . "&repeat=" . $repeat . "&rec_type=" . $rec_type . "&event_length=" . $event_length;
        echo $this->curl_request($action, $args);
    }

    public function update_segment() {
        $ks = urlencode($_POST['ks']);
        $sid = urlencode($_POST['sid']);
        $cid = urlencode($_POST['cid']);
        $eid = urlencode($_POST['eid']);
        $name = urlencode($_POST['name']);
        $desc = urlencode($_POST['desc']);
        $repeat = urlencode($_POST['repeat']);
        $scheduled = urlencode($_POST['scheduled']);
        $action = "channel_config/update_segment?";
        $args = "ks=" . $ks . "&sid=" . $sid . "&cid=" . $cid . "&eid=" . $eid . "&name=" . $name . "&desc=" . $desc . "&repeat=" . $repeat . "&scheduled=" . $scheduled;
        echo $this->curl_request($action, $args);
    }

}

$channel = new channel();
$channel->run();
?>
