<?php

//header('Access-Control-Allow-Origin: *');
class cache {

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

    //run cache api
    public function run() {
        switch ($this->action) {
            case "purge_cache":
                $this->purge_cache();
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

    public function purge_cache() {
        $ks = urlencode($_POST['ks']);
        $asset= urlencode($_POST['asset']);
        $action = "cache_config/purge_cache?";
        $args = "ks=" . $ks . "&asset=" . $asset;
        echo $this->curl_request($action, $args);
    }

}

$cache = new cache();
$cache->run();
?>
