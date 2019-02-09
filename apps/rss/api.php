<?php

class GdnRssData
{
    private $link = null;
    private $login;
    private $password;
    private $database;
    private $hostname;
    private $port;
    private $ks;
    private $pid;
    private $show;

    public function __construct()
    {
        $this->pid = isset($_GET['pid']) ? $_GET['pid'] : null;
        $this->ks = isset($_GET['ks']) ? $_GET['ks'] : null;
        $this->show = isset($_GET['show']) ? $_GET['show'] : null;
        $this->login = 'kaltura';
        $this->password = 'nUKFRl7bE9hShpV';
        $this->database = 'kaltura';
        $this->hostname = '127.0.0.1';
        $this->port = '3306';
    }

    public function run()
    {
        if ($this->validateTokenSession()) {
            $this->connect();
            if ($this->link) {
                $response = $this->getEntryData();
                $data = array(
                    'data' => $response,
                    'totalCount' => count($response)
                );
                header('Content-type: application/json');
                echo json_encode($data);
            } else {
                $data = array('data' => 'No data found');
                header('Content-type: application/json');
                echo json_encode($data);
            }
        } else {
            header("HTTP/1.0 403 Forbidden");
            echo "Not Authorized";
        }
    }

    //connect to database
    private function connect()
    {
        if (!is_null($this->link)) {
            return;
        }

        try {
            $this->link = new PDO("mysql:host=$this->hostname;port=3306;dbname=$this->database", $this->login, $this->password);
            $this->link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            $date = date('Y-m-d H:i:s');
            syslog(LOG_NOTICE, $date . " [GdnRssData->connect] ERROR: Cannot connect to database: " . print_r($e->getMessage(), true));
        }
    }

    private function getEntryData()
    {
        $data = array();
        $query = $this->link->prepare("SELECT e.partner_id, e.id as entry_id, e.name, e.description, e.length_in_msecs, e.updated_at, fa.id as flavor_id, fa.file_ext, fa.size FROM entry as e, flavor_asset as fa WHERE e.partner_id = $this->pid AND e.media_type IN (1,5) AND e.status = 2 AND e.categories = '$this->show' AND fa.tags REGEXP 'mp3audio|audio|mp3' AND e.id = fa.entry_id ORDER BY e.name DESC;");
        $query->execute();
        if ($query->rowCount() > 0) {
            foreach ($query->fetchAll(PDO::FETCH_OBJ) as $row) {
                array_push($data, array(
                'partner_id' => $row->partner_id,
                'entry_id' => $row->entry_id,
                'name' => $row->name,
                'description' => $row->description,
                'duration' => $row->length_in_msecs,
                'updated_at' => $row->updated_at,
                'flavor_id' => $row->flavor_id,
                'file_ext' => $row->file_ext,
                'size' => $row->size,
              ));
            }
        }
        return $data;
    }

    //Valid KS with Media Platform backend
    private function validateTokenSession()
    {
        $serviceUrl = 'https://mediaplatform.streamingmediahosting.com/api_v3/';
        $data = array(
          'ks' => $this->ks,
          'service' => 'partner',
          'action' => 'get',
          'id' => $this->pid,
          'format' => 1
        );

        try {
            $result = $this->curlPost($serviceUrl, $data);
            if (!isset($result['code'])) {
                $partner_id = $result['id'];

                if (isset($partner_id) && $partner_id == $this->pid) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } catch (Exception $ex) {
            return false;
        }
    }

    private function curlPost($url, $data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }
}

$rss_data = new GdnRssData;
$rss_data->run();
