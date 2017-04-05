<?php

class ppv {

    protected $pid;
    protected $sm_ak;
    protected $orderId;
    protected $subId;

    public function __construct() {
        $this->sm_ak = $_GET['sm_ak'];
        $this->orderId = $_GET['oid'];
        $this->subId = $_GET['sid'];
        $this->pid = $_GET['pid'];
    }

    //run ppv api
    public function run() {
        if ($this->validateURL()) {
            if ($this->subId == -1) {
                $result = json_decode($this->cancelOrder());
                if ($result->success) {
                    echo "<script type='text/javascript'>";
                    echo "window.close();";
                    echo "</script>";
                }
            } else {
                $result = json_decode($this->cancelSubOrder());
                if ($result->success) {
                    echo "<script type='text/javascript'>";
                    echo "window.close();";
                    echo "</script>";
                }
            }
            echo "<script type='text/javascript'>";
            echo "window.close();";
            echo "</script>";
        }
    }

    public function cancelOrder() {
        $action = "w_delete_order";
        $args = '&order_id=' . $this->orderId . '&sm_ak=' . $this->sm_ak;
        return $this->curl_request($action, $args);
    }

    public function cancelSubOrder() {
        $action = "w_delete_sub";
        $args = '&sub_id=' . $this->subId . '&sm_ak=' . $this->sm_ak;
        return $this->curl_request($action, $args);
    }

    public function curl_request($action, $args) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://mediaplatform.streamingmediahosting.com/apps/ppv/v1.0/index.php?action=" . $action . "&pid=" . $this->pid . $args);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    public function validateURL() {
        $result = true;
        if (!isset($this->orderId) || $this->orderId == '' || !isset($this->subId) || $this->subId == '' || !isset($this->sm_ak) || $this->sm_ak == '' || !isset($this->pid) || $this->pid == '') {
            $result = false;
        }
        return $result;
    }

}

$ppv = new ppv();
$ppv->run();
?>