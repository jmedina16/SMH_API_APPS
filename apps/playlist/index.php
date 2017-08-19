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
            case "is_playlist_rb":
                $this->is_playlist_rb();
                break;
            default:
                echo "Action not found!";
        }
    }

    public function curl_request($args) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://mediaplatform.streamingmediahosting.com/api_v3/index.php?service=baseEntry&action=get&format=1&" . $args);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        $result = json_decode($output, true);
        curl_close($ch);
        $bool = ($result['playlistType'] === 10) ? 'true' : 'false';
        return $bool;
    }

    public function is_playlist_rb() {
        $eid = urlencode($_GET['eid']);
        $args = "entryId=" . $eid;
        echo $this->curl_request($args);
    }

}

$playlist = new playlist();
$playlist->run();
?>
