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
            case "delete_channel":
                $this->delete_channel();
                break;
            case "add_channel":
                $this->add_channel();
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
        $action = "channel_config/get_channels.json?";
        $args = "ks=" . $ks;
        $channels = $this->curl_request($action, $args);
        echo json_decode($channels, true);
    }

    public function get_channelsX() {
        $ks = urlencode($_POST['ks']);
        $start = urlencode($_POST['start']);
        $length = urlencode($_POST['length']);
        $draw = urlencode($_POST['draw']);
        $tz = urlencode($_POST['tz']);
        $search = urlencode($_POST['search']);
        $action = "channel_config/get_channels?";
        $args = "ks=" . $ks . "&start=" . $start . "&length=" . $length . "&draw=" . $draw . "&tz=" . $tz . "&search=" . $search;
        echo $this->curl_request($action, $args);
    }

    public function delete_channel() {
        $ks = urlencode($_POST['ks']);
        $cid = urlencode($_POST['cid']);
        $action = "channel_config/delete_channel?";
        $args = "ks=" . $ks . "&cid=" . $cid;
        echo $this->curl_request($action, $args);
    }

    public function add_channel() {
        $ks = urlencode($_POST['ks']);
        $eids = urlencode($_POST['eids']);
        $name = urlencode($_POST['name']);
        $desc = urlencode($_POST['desc']);
        $action = "channel_config/add_channel?";
        $args = "ks=" . $ks . "&eids=" . $eids . "&name=" . $name . "&desc=" . $desc;
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
