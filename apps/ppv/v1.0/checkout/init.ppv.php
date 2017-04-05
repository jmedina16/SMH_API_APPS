<?php

class ppv {

    protected $userId;
    protected $entryId;
    protected $kentry;
    protected $ticketId;
    protected $type;
    protected $pid;
    protected $sm_ak;
    protected $orderId;
    protected $subId;
    protected $currentURL;
    private $_link;
    public $title;
    public $description;
    public $price;
    public $symbol;
    public $thumb;
    public $period;
    public $email;
    public $name;
    public $currency;

    public function __construct() {
        $this->userId = $_GET['uid'];
        $this->entryId = $_GET['eid'];
        $this->kentry = $_GET['k_eid'];
        $this->ticketId = $_GET['tid'];
        $this->type = $_GET['type'];
        $this->sm_ak = $_GET['sm_ak'];
        $this->orderId = $_GET['oid'];
        $this->subId = $_GET['sid'];
        $this->pid = $_GET['pid'];
        $this->currentURL = $_GET['r_url'];

        /* ======== Database Connection ========= */
        $this->_link = @mysqli_connect("127.0.0.1", "smh_ppv", "*A095E628F563DE69BDE25AB08F6625B3B63654EF", "smh_ppv_dev", 3307) or die('Unable to establish a DB connection');
        /* ======== Database Connection ========= */
    }

    //run ppv api
    public function run() {
        if ($this->validateURL()) {
            $ticket = json_decode($this->get_checkout_details());
            $this->setTitle($ticket->title);
            $this->setDesc($ticket->description);
            $this->setPrice($ticket->price);
            $this->setCurrencySymbol($ticket->currency_symbol);
            $this->setThumb($ticket->thumb_img);
            $this->setPeriod($ticket->period);
            $this->setEmail($ticket->user_email);
            $this->setName($ticket->user_name);
            $this->setCurrency($ticket->currency);
        } else {
            if (!isset($this->currentURL) || $this->currentURL == '') {
                header('Location: https://streamingmediahosting.com');
            } else {
                header('Location: ' . base64_decode($this->currentURL));
            }
        }
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function getTitle() {
        return $this->title;
    }

    public function setDesc($desc) {
        $this->description = $desc;
    }

    public function getDesc() {
        return $this->description;
    }

    public function setPrice($price) {
        $this->price = $price;
    }

    public function getPrice() {
        return $this->price;
    }

    public function setCurrencySymbol($symbol) {
        $this->symbol = $symbol;
    }

    public function getCurrencySymbol() {
        return $this->symbol;
    }

    public function setThumb($thumb) {
        $this->thumb = $thumb;
    }

    public function getThumb() {
        return $this->thumb;
    }

    public function setPeriod($period) {
        $this->period = $period;
    }

    public function getPeriod() {
        return '<span id="period">'.$this->period.'</span>';
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getName() {
        return $this->name;
    }

    public function setCurrency($currency) {
        if ($currency == 'USD') {
            $this->currency = '';
        } else {
            $this->currency = '<sup>' . $currency . '</sup>';
        }
    }

    public function getCurrency() {
        return $this->currency;
    }

    public function get_checkout_details() {
        $action = "get_checkout_details";
        $args = '&entryId=' . $this->entryId . '&kentry=' . $this->kentry . '&ticket_id=' . $this->ticketId . "&sm_ak=" . $this->sm_ak . '&type=' . $this->type . "&uid=" . $this->userId;
        return $this->curl_request($action, $args);
    }

    public function curl_request($action, $args) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://mediaplatform.streamingmediahosting.com/apps/ppv/v1.0/dev.php?action=" . $action . "&pid=" . $this->pid . $args);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    public function validateURL() {
        $result = true;
//        if (!isset($this->userId) || $this->userId == '' || !isset($this->entryId) || $this->entryId == '' || !isset($this->ticketId) || $this->ticketId == '' || !isset($this->orderId) || $this->orderId == '' || !isset($this->subId) || $this->subId == '' || !isset($this->type) || $this->type == '' || !isset($this->sm_ak) || $this->sm_ak == '' || !isset($this->pid) || $this->pid == '' || !isset($this->currentURL) || $this->currentURL == '') {
//            $result = false;
//        }
        return $result;
    }

}

$ppv = new ppv();
$ppv->run();
?>