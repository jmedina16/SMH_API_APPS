<?php

class link {

    protected $mid;
    protected $m_title;
    protected $client_ip;

    public function __construct() {
        $this->mid = $_GET['mid'];
        $this->m_title = $_GET['m_title'];
        $this->client_ip = $_SERVER['REMOTE_ADDR'];
    }

    public function run() {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://ppv.streamingmediahosting.com/index.php/api/ppv_affiliate/get_aff_link?mid=" . $this->mid . "&ip=" . $this->inet_aton($this->client_ip) . "&format=json");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        $output_array = json_decode($output);
        $this->save_hit($output_array->pid, $output_array->mid, $output_array->aid, $output_array->cid, $this->inet_aton($this->client_ip), $output_array->cookie_life, $output_array->cookie_data, $output_array->url);
    }

    public function save_hit($pid, $mid, $aid, $cid, $ip, $cookie_life, $cookie_data, $t_url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://ppv.streamingmediahosting.com/index.php/api/ppv_affiliate/save_aff_link?pid=" . $pid . "&mid=" . $mid . "&aid=" . $aid . "&cid=" . $cid . "&ip=" . $ip . "&format=json");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        if ($output == 'true') {
            $this->set_cookie($cookie_life, $cookie_data, $t_url);
        } else {
            header('Location: ' . $t_url);
        }
    }

    public function set_cookie($cookie_life, $cookie_data, $t_url) {
        $query = parse_url($t_url, PHP_URL_QUERY);

        if ($query) {
            $t_url .= '&smh_aff=' . $cookie_data . "&smh_exp=" . $cookie_life;
        } else {
            $t_url .= '?smh_aff=' . $cookie_data . "&smh_exp=" . $cookie_life;
        }

        header('Location: ' . $t_url);
    }

    public function inet_aton($ip) {
        $ip = trim($ip);
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6))
            return 0;
        return sprintf("%u", ip2long($ip));
    }

    public function inet_ntoa($num) {
        $num = trim($num);
        if ($num == "0")
            return "0.0.0.0";
        return long2ip(-(4294967295 - ($num - 1)));
    }

    function get_domain($url) {
        $pieces = parse_url($url);
        $domain = isset($pieces['host']) ? $pieces['host'] : '';
        if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
            return $regs['domain'];
        }
        return false;
    }

}

$link = new link();
$link->run();
?>
