<?php

class transcode {

    protected $link = null;
    protected $login;
    protected $password;
    protected $database;
    protected $link2 = null;
    protected $login2;
    protected $password2;
    protected $database2;
    protected $hostname;
    protected $port;
    protected $file_sync_entries;
    protected $accounts;
    protected $file_sync_entries_found = array();
    protected $file_sync_entries_file_sizes = array();
    protected $partner_ids = array();

    public function __construct() {
        $this->login = 'kaltura';
        $this->password = 'nUKFRl7bE9hShpV';
        $this->database = 'kaltura';
        $this->login2 = 'smhstats';
        $this->password2 = 'tVuasxXqy33Z3WkTbXHRruSC34dbVLnLNgq';
        $this->database2 = 'smh_statistics';
        $this->hostname = '127.0.0.1';
        $this->port = '3306';
    }

    public function run() {
        $this->connect2();
        $this->get_accounts();
        if (count($this->partner_ids) > 0) {
            $this->connect();
//            $now_date = new DateTime('now');
//            $now_date->setTimezone(new DateTimeZone('UTC'));
//            $month1 = $now_date->format('Y-m-d');
//            $now_date->modify('-1 day');
//            $yest_date = $now_date->format('Y-m-d');
//            $dates = array($month1, $yest_date);

            $dates = array('2018');

            foreach ($this->partner_ids as $partner_id) {
                $this->file_sync_entries_found = array();
                $this->file_sync_entries_file_sizes = array();
                $this->update_account_status($partner_id);
                foreach ($dates as $date) {
                    $this->get_file_sync_conv($partner_id, $date);
                    $this->get_file_sync_sizes($date);
                    //$this->update_transcoded_flavors();
                }
                //$this->remove_account($partner_id);
            }
        }
    }

    public function get_accounts() {
        try {
//            $this->accounts = $this->link2->prepare("SELECT * FROM transcoding_queue WHERE executing = 0");
//            $this->accounts->execute();
//            if ($this->accounts->rowCount() > 0) {
//                foreach ($this->accounts->fetchAll(PDO::FETCH_OBJ) as $row) {
//                    array_push($this->partner_ids, $row->partner_id);
//                }
//            }
            array_push($this->partner_ids, 10012);
        } catch (PDOException $e) {
            $date = date('Y-m-d H:i:s');
            print($date . " [transcode->get_accounts] ERROR: Could not execute query (get_accounts): " . $e->getMessage() . "\n");
        }
    }

    public function update_account_status($partner_id) {
        try {
            $date = date('Y-m-d H:i:s');
            $this->accounts = $this->link2->prepare("UPDATE transcoding_queue SET executing = 1, updated_at = '$date' WHERE partner_id =  $partner_id");
            $this->accounts->execute();
        } catch (PDOException $e) {
            $date = date('Y-m-d H:i:s');
            print($date . " [transcode->update_account_status] ERROR: Could not execute query (update_account_status): " . $e->getMessage() . "\n");
        }
    }

    public function remove_account($partner_id) {
        try {
            $this->accounts = $this->link2->prepare("DELETE FROM transcoding_queue WHERE partner_id =  $partner_id");
            $this->accounts->execute();
        } catch (PDOException $e) {
            $date = date('Y-m-d H:i:s');
            print($date . " [transcode->remove_account] ERROR: Could not execute query (remove_account): " . $e->getMessage() . "\n");
        }
    }

    public function get_file_sync_conv($partner_id, $month) {
        try {
            $this->file_sync_entries = $this->link->prepare("SELECT * FROM file_sync WHERE partner_id IN (" . $partner_id . ") AND status IN (2,3) AND object_type = 4 AND file_size != -1 AND version = 0 AND ready_at LIKE '%" . $month . "%'");
            $this->file_sync_entries->execute();
            if ($this->file_sync_entries->rowCount() > 0) {
                foreach ($this->file_sync_entries->fetchAll(PDO::FETCH_OBJ) as $row) {
                    if ($this->multi_array_search($row->partner_id, $this->file_sync_entries_found)) {
                        foreach ($this->file_sync_entries_found as &$account) {
                            if ($account['partner_id'] === $row->partner_id) {
                                array_push($account['flavors'], '\'' . $row->object_id . '\'');
                            }
                        }
                    } else {
                        array_push($this->file_sync_entries_found, array('partner_id' => $row->partner_id, 'flavors' => array('\'' . $row->object_id . '\'')));
                    }
                }
            }
        } catch (PDOException $e) {
            $date = date('Y-m-d H:i:s');
            print($date . " [transcode->get_file_sync_data] ERROR: Could not execute query (get_file_sync_data): " . $e->getMessage() . "\n");
        }
    }

    public function get_file_sync_sizes($month) {
        try {
            foreach ($this->file_sync_entries_found as $file_sync_entries) {
                $flavors = implode(",", $file_sync_entries['flavors']);
                $this->file_sync_entries = $this->link->prepare("SELECT fs.partner_id, fa.entry_id, e.media_type, fs.object_id, fa.width, fa.height, fa.bitrate, fa.frame_rate, fs.file_size, e.length_in_msecs, fs.version, fs.ready_at FROM file_sync fs, flavor_asset fa, entry e WHERE fs.partner_id = " . $file_sync_entries['partner_id'] . " AND fs.status IN (2,3) AND fa.status IN (2,3) AND fs.object_type = 4 AND fs.object_id IN (" . $flavors . ") AND fs.file_size != -1 AND fs.version = 0 AND fs.ready_at LIKE '%" . $month . "%' AND fs.object_id = fa.id AND fa.entry_id = e.id");
                $this->file_sync_entries->execute();
                if ($this->file_sync_entries->rowCount() > 0) {
                    foreach ($this->file_sync_entries->fetchAll(PDO::FETCH_OBJ) as $row) {
                        array_push($this->file_sync_entries_file_sizes, array('partner_id' => $row->partner_id, 'entry_id' => $row->entry_id, 'media_type' => $row->media_type, 'flavor' => $row->object_id, 'width' => $row->width, 'height' => $row->height, 'bitrate' => $row->bitrate, 'frame_rate' => $row->frame_rate, 'version' => $row->version, 'file_size' => $row->file_size, 'length_in_msecs' => $row->length_in_msecs, 'ready_at' => $row->ready_at));
                    }
                }
            }
            foreach ($this->file_sync_entries_file_sizes as $flavors) {
                if ($flavors['entry_id'] == '0_rys7oi7a') {
                    print_r($flavors);
                }
            }
        } catch (PDOException $e) {
            $date = date('Y-m-d H:i:s');
            print($date . " [transcode->get_file_sync_sizes] ERROR: Could not execute query (get_file_sync_sizes): " . $e->getMessage() . "\n");
        }
    }

    public function update_transcoded_flavors() {
        $this->connect2();
        foreach ($this->file_sync_entries_file_sizes as $flavors) {
            try {
//                $date_dt = new DateTime($flavors['ready_at'], new DateTimeZone('America/Los_Angeles'));
//                $date_dt->setTimeZone(new DateTimeZone('UTC'));
//                $ready_at = $date_dt->format('Y-m-d H:i:s');
//                $query = $this->link2->prepare("INSERT INTO transcoding (partner_id, entry_id, media_type, flavor, width, height, bitrate, frame_rate, version, file_size, length_in_msecs, ready_at) SELECT * FROM (SELECT " . $flavors['partner_id'] . " as partner_id, '" . $flavors['entry_id'] . "' as entry_id, " . $flavors['media_type'] . " as media_type, '" . $flavors['flavor'] . "' as flavor, '" . $flavors['width'] . "' as width, '" . $flavors['height'] . "' as height, '" . $flavors['bitrate'] . "' as bitrate, '" . $flavors['frame_rate'] . "' as frame_rate, " . $flavors['version'] . " as version, " . $flavors['file_size'] . " as file_size, " . $flavors['length_in_msecs'] . " as length_in_msecs, '" . $ready_at . "' as ready_at) AS tmp WHERE NOT EXISTS (SELECT flavor, version FROM transcoding WHERE flavor = '" . $flavors['flavor'] . "' AND version = " . $flavors['version'] . ") LIMIT 1;");
                $query = $this->link2->prepare("UPDATE transcoding SET media_type =  " . $flavors['media_type'] . ", width = " . $flavors['width'] . ", height = " . $flavors['height'] . ", bitrate = " . $flavors['bitrate'] . ", frame_rate = " . $flavors['frame_rate'] . " WHERE partner_id = " . $flavors['partner_id'] . " AND entry_id = '" . $flavors['entry_id'] . "' AND flavor = '" . $flavors['flavor'] . "' AND version = " . $flavors['version']);
                $query->execute();
            } catch (PDOException $e) {
                $date = date('Y-m-d H:i:s');
                print($date . " [transcode->update_transcoded_flavors] ERROR: Could not execute query (update_transcoded_flavors): " . $e->getMessage() . "\n");
            }
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

    public function connect2() {
        if (!is_null($this->link2)) {
            return;
        }

        try {
            $this->link2 = new PDO("mysql:host=$this->hostname;port=3306;dbname=$this->database2", $this->login2, $this->password2);
            $this->link2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            $date = date('Y-m-d H:i:s');
            syslog(LOG_NOTICE, $date . " [Channel->connect] ERROR: Cannot connect to database: " . print_r($e->getMessage(), true));
        }
    }

}

$transcode = new transcode();
$transcode->run();
?>