<?php

//header('Access-Control-Allow-Origin: *');
require_once "/var/www/vhosts/api/application/libraries/PHPExcel/Classes/PHPExcel.php";

class stats {

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
    }

    //run ppv api
    public function run() {
        switch ($this->action) {
            case "get_child_stats":
                $this->get_child_stats();
                break;
            case "get_all_child_stats":
                $this->get_all_child_stats();
                break;
            default:
                echo "Action not found!";
        }
    }

    public function curl_request($action, $args) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://api.streamingmediahosting.com/index.php/api/" . $action . "pid=" . $this->pid . "&format=json&" . $args);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, true);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    public function get_child_stats() {
        $ks = urlencode($_GET['ks']);
        $cpid = urlencode($_GET['cpid']);
        $start_date = urlencode($_GET['start_date']);
        $end_date = urlencode($_GET['end_date']);
        $action = "stats_config/get_child_stats?";
        $args = "ks=" . $ks . "&cpid=" . $cpid . "&start_date=" . $start_date . "&end_date=" . $end_date;
        $resp = $this->curl_request($action, $args);

        list($headers, $response) = explode("\r\n\r\n", $resp, 2);
        $headers = explode("\n", $headers);
        foreach ($headers as $header) {
            header($header);
        }

        echo $response;
    }

    public function get_all_child_stats() {
        $ks = urlencode($_GET['ks']);
        $start_date = urlencode($_GET['start_date']);
        $end_date = urlencode($_GET['end_date']);
        $action = "stats_config/get_all_child_stats?";
        $args = "ks=" . $ks . "&start_date=" . $start_date . "&end_date=" . $end_date;
        $resp = $this->curl_request($action, $args);

        list($headers, $response) = explode("\r\n\r\n", $resp, 2);
        $headers = explode("\n", $headers);
        foreach ($headers as $header) {
            header($header);
        }

        echo $response;
    }

}

$stats = new stats();
$stats->run();
?>
