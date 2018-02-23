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
    protected $file_sync_entries_file_sizes = array();

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
        $this->get_file_sync_fie_sizes();
        syslog(LOG_NOTICE, "SMH DEBUG : file_sync_entries_file_sizes: " . print_r($this->file_sync_entries_file_sizes, true));
    }

    public function get_accounts() {
        try {
            $this->accounts = $this->link->prepare("SELECT * FROM partner WHERE status = 1 AND id IN (10012,101) AND id NOT IN (0,99,-2,-1,-3, -4, -5)");
            $this->accounts->execute();
            if ($this->accounts->rowCount() > 0) {
                foreach ($this->accounts->fetchAll(PDO::FETCH_OBJ) as $row) {
                    array_push($this->partner_ids, $row->id);
                    //array_push($this->file_sync_entries_found, array('partner_id' => $row->id, 'flavors' => array()));
                    //array_push($this->file_sync_entries_file_sizes, array('partner_id' => $row->id, 'flavors' => array()));
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
            $this->file_sync_entries = $this->link->prepare("SELECT * FROM file_sync WHERE partner_id IN (" . $partner_ids . ") AND status IN (2,3) AND object_type = 4 AND file_size != -1 AND version = 0 AND ready_at LIKE '%" . $month . "%'");
            $this->file_sync_entries->execute();
            if ($this->file_sync_entries->rowCount() > 0) {
                foreach ($this->file_sync_entries->fetchAll(PDO::FETCH_OBJ) as $row) {
                    if ($this->multi_array_search($row->partner_id, $this->file_sync_entries_found)) {
                        foreach ($this->file_sync_entries_found as &$account) {
                            if ($account['partner_id'] === $row->partner_id) {
                                array_push($account['flavors'], $row->object_id);
                            }
                        }
                    } else {
                        array_push($this->file_sync_entries_found, array('partner_id' => $row->partner_id, 'flavors' => array($row->object_id)));
                    }
                }
            }
        } catch (PDOException $e) {
            $date = date('Y-m-d H:i:s');
            print($date . " [transcode->get_file_sync_data] ERROR: Could not execute query (get_file_sync_data): " . $e->getMessage() . "\n");
        }
    }

    public function get_file_sync_fie_sizes() {
        $partner_ids = implode(",", $this->partner_ids);
        syslog(LOG_NOTICE, "SMH DEBUG : get_file_sync_data: " . $partner_ids);
        $data = array(':partner_ids' => $partner_ids);
        try {
            $date = new DateTime('now');
            $date->setTimezone(new DateTimeZone('UTC'));
            $month = $date->format('Y-m');
            foreach ($this->file_sync_entries_found as $file_sync_entries) {
                $flavors = implode(",", $file_sync_entries['flavors']);
                $this->file_sync_entries = $this->link->prepare("SELECT * FROM file_sync WHERE partner_id = " . $file_sync_entries['partner_id'] . " AND status IN (2,3) AND object_type = 4 AND object_id IN (" . $flavors . ") AND file_size != -1 AND version > 0 AND ready_at LIKE '%" . $month . "%'");
                $this->file_sync_entries->execute();
                if ($this->file_sync_entries->rowCount() > 0) {
                    foreach ($this->file_sync_entries->fetchAll(PDO::FETCH_OBJ) as $row) {
                        array_push($this->file_sync_entries_file_sizes, array('partner_id' => $row->partner_id, 'flavor' => $row->object_id, 'version' => $row->version, 'file_size' => $row->file_size, 'ready_at' => $row->ready_at));
                    }
                }
            }
        } catch (PDOException $e) {
            $date = date('Y-m-d H:i:s');
            print($date . " [transcode->get_file_sync_fie_sizes] ERROR: Could not execute query (get_file_sync_fie_sizes): " . $e->getMessage() . "\n");
        }
    }

    public function multi_array_search($search_for, $search_in) {
        foreach ($search_in as $element) {
            if (($element === $search_for)) {
                return true;
            } elseif (is_array($element)) {
                $result = $this->multi_array_search($search_for, $element);
                if ($result == true)
                    return true;
            }
        }
        return false;
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