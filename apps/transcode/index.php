<?php

class transcode {

    protected $link = null;
    protected $login;
    protected $password;
    protected $database;
    protected $hostname;
    protected $port;
    protected $file_sync_entries;
    protected $accounts;
    protected $partner_ids = array();
    protected $file_sync_entries_found = array();

    public function __construct() {
        $this->login = 'kaltura';
        $this->password = 'nUKFRl7bE9hShpV';
        $this->database = 'kaltura';
        $this->hostname = '127.0.0.1';
        $this->port = '3306';
    }

    public function run() {
        $this->connect();
        $this->get_accounts();
        $this->get_file_sync_conv();
        syslog(LOG_NOTICE, "SMH DEBUG : file_sync_entries_found: " . print_r($this->file_sync_entries_found, true));
    }

    public function get_accounts() {
        try {
            $this->accounts = $this->link->prepare("SELECT * FROM partner WHERE status = 1 AND id IN (10012,101) AND id NOT IN (0,99,-2,-1,-3, -4, -5)");
            $this->accounts->execute();
            if ($this->accounts->rowCount() > 0) {
                foreach ($this->accounts->fetchAll(PDO::FETCH_OBJ) as $row) {
                    array_push($this->partner_ids, $row->id);
                    array_push($this->file_sync_entries_found, array('partner_id' => $row->id, 'flavors' => array()));
                }
            }
        } catch (PDOException $e) {
            $date = date('Y-m-d H:i:s');
            print($date . " [transcode->get_file_sync_data] ERROR: Could not execute query (get_file_sync_data): " . $e->getMessage() . "\n");
        }
    }

    public function get_file_sync_conv() {
        $partner_ids = implode(",", $this->partner_ids);
        syslog(LOG_NOTICE, "SMH DEBUG : get_file_sync_data: " . $partner_ids);
        $data = array(':partner_ids' => $partner_ids);
        try {
            $date = new DateTime('now');
            $date->setTimezone(new DateTimeZone('UTC'));
            $month = $date->format('Y-m');
            $this->file_sync_entries = $this->link->prepare("SELECT * FROM file_sync WHERE partner_id IN (" . $partner_ids . ") AND status = 2 AND object_type = 4 AND file_size != -1 AND version = 0 AND created_at LIKE '%" . $month . "%'");
            $this->file_sync_entries->execute();
            if ($this->file_sync_entries->rowCount() > 0) {
                foreach ($this->file_sync_entries->fetchAll(PDO::FETCH_OBJ) as $row) {
                    foreach ($this->file_sync_entries_found as &$account) {
                        if ($account['partner_id'] === $row->partner_id) {
                            array_push($account['flavors'], $row->object_id);
                        }
                    }
                }
            }
        } catch (PDOException $e) {
            $date = date('Y-m-d H:i:s');
            print($date . " [transcode->get_file_sync_data] ERROR: Could not execute query (get_file_sync_data): " . $e->getMessage() . "\n");
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

}

$transcode = new transcode();
$transcode->run();
?>