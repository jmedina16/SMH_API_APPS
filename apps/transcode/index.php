<?php

class transcode {

    protected $link = null;
    protected $login;
    protected $password;
    protected $database;
    protected $hostname;
    protected $port;
    protected $partner_id;

    public function __construct() {
        $this->login = 'smhstats';
        $this->password = 'tVuasxXqy33Z3WkTbXHRruSC34dbVLnLNgq';
        $this->database = 'smh_statistics';
        $this->hostname = '127.0.0.1';
        $this->port = '3306';
        $options = getopt("p:");
        $this->partner_id = $options['p'];
    }

    public function run() {
        $this->connect();
        $this->insert_account();

    }

    public function check_account() {
        $found_account = false;
        try {
            $this->account = $this->link->prepare("SELECT * FROM transcoding_queue WHERE partner_id = $this->partner_id");
            $this->account->execute();
            if ($this->account->rowCount() > 0) {
                $found_account = true;
            }
            return $found_account;
        } catch (PDOException $e) {
            $date = date('Y-m-d H:i:s');
            print($date . " [transcode->get_file_sync_data] ERROR: Could not execute query (get_file_sync_data): " . $e->getMessage() . "\n");
            return $found_account;
        }
    }

    public function insert_account() {
        $is_running = $this->check_account();
        if (!$is_running) {
            try {
                $date = date('Y-m-d H:i:s');
                $query = $this->link->prepare("INSERT INTO transcoding_queue (partner_id, executing, created_at) VALUES ($this->partner_id,0,'$date')");
                $query->execute();
            } catch (PDOException $e) {
                $date = date('Y-m-d H:i:s');
                print($date . " [transcode->insert_account] ERROR: Could not execute query (insert_account): " . $e->getMessage() . "\n");
            }
        } else {
            exit;
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
            syslog(LOG_NOTICE, $date . " [transcode->connect] ERROR: Cannot connect to database: " . print_r($e->getMessage(), true));
        }
    }

}

$transcode = new transcode();
$transcode->run();
?>