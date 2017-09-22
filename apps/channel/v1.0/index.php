<?php

//header('Access-Control-Allow-Origin: *');
class channel {

    protected $pid;
    protected $action;
    protected $link = null;
    protected $login;
    protected $password;
    protected $database;
    protected $hostname;
    protected $port;

    public function __construct() {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method == 'POST') {
            isset($_POST["pid"]) ? $this->pid = $_POST["pid"] : $this->pid = '';
            isset($_POST["action"]) ? $this->action = $_POST["action"] : $this->action = '';
        } elseif ($method == 'GET') {
            isset($_GET["pid"]) ? $this->pid = $_GET["pid"] : $this->pid = '';
            isset($_GET["action"]) ? $this->action = $_GET["action"] : $this->action = '';
        }
        $this->login = 'kaltura';
        $this->password = 'nUKFRl7bE9hShpV';
        $this->database = 'kaltura';
        $this->hostname = '127.0.0.1';
        $this->port = '3306';
    }

    //run ppv api
    public function run() {
        switch ($this->action) {
            case "post_schedule":
                $this->post_schedule();
                break;
            default:
                echo "Action not found!";
        }
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
        $channel_ids = array();
        $live_channels = $this->get_channels();
        foreach ($live_channels['objects'] as $live_channel) {
            array_push($channel_ids, $live_channel['id']);
        }
    }

    public function get_channels() {
        $url = 'https://mediaplatform.streamingmediahosting.com/api_v3/index.php';
        $data = array('ks' => $_POST['ks'], 'service' => 'liveChannel', 'action' => 'list', 'format' => 1);
        $liveChannels = $this->curlPost($url, $data);
        return $liveChannels;
    }

}

$channel = new channel();
$channel->run();
?>
