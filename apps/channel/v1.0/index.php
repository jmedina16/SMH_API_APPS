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

    public function post_schedule() {
        print_r($this->get_channels());
    }
    
    public function get_channels(){
        $url = 'https://mediaplatform.streamingmediahosting.com/api_v3/index.php';
        $data = array('ks' => $_POST['ks'], 'service' => 'liveChannel', 'action' => 'list', 'format' => 1);
        $liveChannels = $this->curlPost($url, $data);
        return $liveChannels;
    }

}

$channel = new channel();
$channel->run();
?>
