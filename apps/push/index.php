<?php

class push {

    protected $post_data;
    protected $link = null;
    protected $login;
    protected $password;
    protected $database;
    protected $hostname;
    protected $port;

    public function __construct() {
        $this->login = 'smh_mngmt';
        $this->password = '*AC54418D19B5CA7E6195A83CBA66B843ED7CC16C';
        $this->database = 'smh_management';
        $this->hostname = '127.0.0.1';
        $this->port = '3306';
    }

    public function run() {
        $this->post_data = $_POST;
        $this->push_notification();
    }

    public function push_notification() {
        $bsfPush = array(13373, 12773);
        if (in_array($this->post_data['partner_id'], $bsfPush)) {
            $this->insert_push_notify();
        }
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

    public function insert_push_notify() {
        date_default_timezone_set("UTC");
        $this->connect();
        $entry_exists = $this->entry_exists();
        if (!$entry_exists['success']) {
            $data = array(':partner_id' => $this->post_data['partner_id'], ':entryId' => $this->post_data['entry_id'], ':sent' => 0, ':created_at' => date('Y-m-d H:i:s'), ':updated_at' => null);
            try {
                $query = $this->link->prepare("INSERT INTO push_notifications (partner_id,entryId,sent,created_at,updated_at) VALUES (:partner_id,:entryId,:sent,:created_at,:updated_at)");
                $query->execute($data);
            } catch (PDOException $e) {
                $date = date('Y-m-d H:i:s');
                print($date . " [push->insert_push_notify] ERROR: Could not execute query (insert_push_notify): " . $e->getMessage() . "\n");
            }
        }
    }

    public function entry_exists() {
        $success = array('success' => false);
        $data = array(':partner_id' => $this->post_data['partner_id'], ':entryId' => $this->post_data['entry_id']);
        try {
            $query = $this->link->prepare("SELECT * FROM push_notifications WHERE partner_id = :partner_id AND entryId = :entryId");
            $query->execute($data);
            if ($query->rowCount() > 0) {
                $success = array('success' => true);
            }
        } catch (PDOException $e) {
            $date = date('Y-m-d H:i:s');
            print($date . " [push->check_entry] ERROR: Could not execute query (check_entry): " . $e->getMessage() . "\n");
        }
        return $success;
    }

}

$push = new push();
$push->run();
?>