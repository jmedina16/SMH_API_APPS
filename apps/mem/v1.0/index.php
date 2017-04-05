<?php

//header('Access-Control-Allow-Origin: *');
class mem {

    protected $pid;
    protected $action;

    public function __construct() {
        isset($_GET["pid"]) ? $this->pid = $_GET["pid"] : $this->pid = '';
        isset($_GET["action"]) ? $this->action = $_GET["action"] : $this->action = '';
    }

    //run mem api
    public function run() {
        switch ($this->action) {
            case "list_users":
                $this->list_users();
                break;
            case "create_account":
                $this->create_account();
                break;
            case "register_account":
                $this->register_account();
                break;
            case "delete_account":
                $this->delete_account();
                break;
            case "destroy_session":
                $this->destroy_session();
                break;
            case "update_user_status":
                $this->update_user_status();
                break;
            case "reset_pswd":
                $this->reset_pswd();
                break;
            case "update_account":
                $this->update_account();
                break;
            case "list_entries":
                $this->list_entries();
                break;
            case "add_entry":
                $this->add_entry();
                break;
            case "update_entry":
                $this->update_entry();
                break;
            case "update_entry_status":
                $this->update_entry_status();
                break;
            case "delete_entry":
                $this->delete_entry();
                break;
            case "get_email":
                $this->get_email();
                break;
            case "get_email_config":
                $this->get_email_config();
                break;
            case "update_email_config":
                $this->update_email_config();
                break;
            case "add_email_reg":
                $this->add_email_reg();
                break;
            case "add_email_pswd":
                $this->add_email_pswd();
                break;
            case "list_actype":
                $this->list_actype();
                break;
            case "login_user":
                $this->login_user();
                break;
            case "is_logged_in":
                $this->is_logged_in();
                break;
            case "activate_cat_entry":
                $this->activate_cat_entry();
                break;
            case "get_user_name":
                $this->get_user_name();
                break;
            case "create_auth_key":
                $this->create_auth_key();
                break;
            case "setup_player":
                $this->setup_player();
                break;
            case "w_get_thumb":
                $this->w_get_thumb();
                break;
            case "get_thumb":
                $this->get_thumb();
                break;
            case "get_cat_thumb":
                $this->get_cat_thumb();
                break;
            case "w_get_cat_thumb":
                $this->w_get_cat_thumb();
                break;
            case "delete_playlist_entry":
                $this->delete_playlist_entry();
                break;
            case "delete_platform_entry":
                $this->delete_platform_entry();
                break;
            case "get_cat_entries":
                $this->get_cat_entries();
                break;
            case "update_platform_cat":
                $this->update_platform_cat();
                break;
            case "update_drag_cat":
                $this->update_drag_cat();
                break;
            case "delete_platform_cat":
                $this->delete_platform_cat();
                break;
            case "activate_account":
                $this->activate_account();
                break;
            case "reset_request":
                $this->reset_request();
                break;
            case "reset_pass":
                $this->reset_pass();
                break;
            case "reset_email":
                $this->reset_email();
                break;
            case "is_active":
                $this->is_active();
                break;
            case "is_not_active":
                $this->is_not_active();
                break;
            case "user_concurrent_status":
                $this->user_concurrent_status();
                break;
            case "get_concurrent_status":
                $this->get_concurrent_status();
                break;
            case "user_activation_skip_status":
                $this->user_activation_skip_status();
                break;
            case "get_activation_skip_status":
                $this->get_activation_skip_status();
                break;
            case "get_owner_attrs":
                $this->get_owner_attrs();
                break;
            case "update_reg_fields":
                $this->update_reg_fields();
                break;
            case "update_user_details":
                $this->update_user_details();
                break;
            case "get_user_details":
                $this->get_user_details();
                break;
            case "update_fname":
                $this->update_fname();
                break;
            case "update_lname":
                $this->update_lname();
                break;
            case "reset_psswd_request":
                $this->reset_psswd_request();
                break;
            case "reset_email_request":
                $this->reset_email_request();
                break;
            default:
                echo "Action not found!";
        }
    }

    public function curl_request($action, $args) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://api.streamingmediahosting.com/index.php/api/" . $action . "pid=" . $this->pid . "&format=json&" . $args);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    public function list_users() {
        $ks = urlencode($_GET['ks']);
        $start = $_GET['start'];
        $length = $_GET['length'];
        $search = $_GET['search'];
        $draw = $_GET['draw'];
        $action = "mem_user/list_user?";
        $args = "start=" . $start . "&length=" . $length . "&search=" . $search['value'] . "&draw=" . $draw . "&ks=" . $ks;
        echo $this->curl_request($action, $args);
    }

    public function create_account() {
        $ks = urlencode($_GET['ks']);
        $fname = urlencode($_GET['fname']);
        $lname = urlencode($_GET['lname']);
        $email = urlencode($_GET['email']);
        $pass = urlencode($_GET['pass']);
        $status = urlencode($_GET['status']);
        $tz = urlencode($_GET['tz']);
        $action = "mem_user/add_user?";
        $args = "tz=" . $tz . "&fname=" . $fname . "&lname=" . $lname . "&email=" . $email . "&pass=" . $pass . "&status=" . $status . "&ks=" . $ks;
        echo $this->curl_request($action, $args);
    }

    public function activate_account() {
        $sm_ak = urlencode($_GET['sm_ak']);
        $akey = urlencode($_GET['akey']);
        $tz = urlencode($_GET['tz']);
        $email = urlencode($_GET['email']);
        $action = "mem_user/activate_user?";
        $args = "tz=" . $tz . "&akey=" . $akey . "&sm_ak=" . $sm_ak . "&email=" . $email;
        echo $this->curl_request($action, $args);
    }

    public function register_account() {
        $sm_ak = urlencode($_GET['sm_ak']);
        $fname = urlencode($_GET['fname']);
        $lname = urlencode($_GET['lname']);
        $email = urlencode($_GET['email']);
        $pass = urlencode($_GET['pass']);
        $tz = urlencode($_GET['tz']);
        $url = urlencode($_GET['url']);
        $attrs = urlencode($_GET['attrs']);
        $type = urlencode($_GET['type']);
        $entryId = urlencode($_GET['entryId']);
        $action = "mem_user/register_user?";
        $args = "tz=" . $tz . "&fname=" . $fname . "&lname=" . $lname . "&email=" . $email . "&pass=" . $pass . "&sm_ak=" . $sm_ak . "&url=" . $url . "&attrs=" . $attrs . "&type=" . $type . "&entryId=" . $entryId;
        echo $this->curl_request($action, $args);
    }

    public function delete_account() {
        $ks = urlencode($_GET['ks']);
        $uid = $_GET['uid'];
        $tz = $_GET['tz'];
        $action = "mem_user/delete_user?";
        $args = "tz=" . $tz . "&uid=" . $uid . "&ks=" . $ks;
        echo $this->curl_request($action, $args);
    }

    public function destroy_session() {
        $ks = urlencode($_GET['ks']);
        $uid = $_GET['uid'];
        $action = "mem_user/destroy_session?";
        $args = "uid=" . $uid . "&ks=" . $ks;
        echo $this->curl_request($action, $args);
    }

    public function update_user_status() {
        $tz = $_GET['tz'];
        $ks = urlencode($_GET['ks']);
        $email = $_GET['email'];
        $status = $_GET['status'];
        $uid = $_GET['uid'];
        $action = "mem_user/update_user_status?";
        $args = "tz=" . $tz . "&email=" . $email . "&status=" . $status . "&uid=" . $uid . "&ks=" . $ks;
        echo $this->curl_request($action, $args);
    }

    public function reset_pswd() {
        $ks = urlencode($_GET['ks']);
        $tz = $_GET['tz'];
        $email = $_GET['email'];
        $pass = urlencode($_GET['pass']);
        $action = "mem_user/update_user_pswd?";
        $args = "tz=" . $tz . "&email=" . $email . "&pass=" . $pass . "&ks=" . $ks;
        echo $this->curl_request($action, $args);
    }

    public function update_account() {
        $ks = urlencode($_GET['ks']);
        $fname = $_GET['fname'];
        $lname = $_GET['lname'];
        $email = $_GET['email'];
        $uid = $_GET['uid'];
        $tz = $_GET['tz'];
        $action = "mem_user/update_user?";
        $args = "tz=" . $tz . "&fname=" . $fname . "&lname=" . $lname . "&email=" . $email . "&uid=" . $uid . "&ks=" . $ks;
        echo $this->curl_request($action, $args);
    }

    public function list_entries() {
        $ks = urlencode($_GET['ks']);
        $start = $_GET['start'];
        $length = $_GET['length'];
        $search = $_GET['search'];
        $draw = $_GET['draw'];
        $action = "mem_entry/list_entry?";
        $args = "start=" . $start . "&length=" . $length . "&search=" . $search['value'] . "&draw=" . $draw . "&ks=" . $ks;
        echo $this->curl_request($action, $args);
    }

    public function add_entry() {
        $ks = urlencode($_GET['ks']);
        $kentry_id = urlencode($_GET['kentry_id']);
        $kentry_name = urlencode($_GET['kentry_name']);
        $media_type = urlencode($_GET['media_type']);
        $ac_id = urlencode($_GET['ac_id']);
        $ac_name = urlencode($_GET['ac_name']);
        $status = urlencode($_GET['status']);
        $action = "mem_entry/add_entry?";
        $args = "kentry_id=" . $kentry_id . "&kentry_name=" . $kentry_name . "&media_type=" . $media_type . "&media_type=" . $media_type . "&ac_id=" . $ac_id . "&ac_name=" . $ac_name . "&status=" . $status . "&ks=" . $ks;
        echo $this->curl_request($action, $args);
    }

    public function update_entry() {
        $ks = urlencode($_GET['ks']);
        $kentry_id = urlencode($_GET['kentry_id']);
        $kentry_name = urlencode($_GET['kentry_name']);
        $media_type = urlencode($_GET['media_type']);
        $ac_id = urlencode($_GET['ac_id']);
        $ac_name = urlencode($_GET['ac_name']);
        $action = "mem_entry/update_entry?";
        $args = "kentry_id=" . $kentry_id . "&kentry_name=" . $kentry_name . "&media_type=" . $media_type . "&media_type=" . $media_type . "&ac_id=" . $ac_id . "&ac_name=" . $ac_name . "&ks=" . $ks;
        echo $this->curl_request($action, $args);
    }

    public function update_entry_status() {
        $ks = urlencode($_GET['ks']);
        $tz = $_GET['tz'];
        $kentry_id = $_GET['kentry_id'];
        $status = $_GET['status'];
        $action = "mem_entry/update_entry_status?";
        $args = "tz=" . $tz . "&kentry_id=" . $kentry_id . "&status=" . $status . "&ks=" . $ks;
        echo $this->curl_request($action, $args);
    }

    public function delete_entry() {
        $ks = urlencode($_GET['ks']);
        $kentry_id = $_GET['kentry_id'];
        $media_type = $_GET['media_type'];
        $action = "mem_entry/delete_entry?";
        $args = "kentry_id=" . $kentry_id . "&media_type=" . $media_type . "&ks=" . $ks;
        echo $this->curl_request($action, $args);
    }

    public function delete_platform_entry() {
        $ks = urlencode($_GET['ks']);
        $kentry_id = $_GET['kentry_id'];
        $action = "mem_entry/delete_platform_entry?";
        $args = "kentry_id=" . $kentry_id . "&ks=" . $ks;
        echo $this->curl_request($action, $args);
    }

    public function get_email() {
        $ks = urlencode($_GET['ks']);
        $action = "mem_config/get_email?";
        $args = 'ks=' . $ks;
        echo $this->curl_request($action, $args);
    }

    public function get_email_config() {
        $ks = urlencode($_GET['ks']);
        $action = "mem_config/get_email_config?";
        $args = 'ks=' . $ks;
        echo $this->curl_request($action, $args);
    }

    public function update_email_config() {
        $ks = urlencode($_GET['ks']);
        $from_name = urlencode($_GET['from_name']);
        $from_email = urlencode($_GET['from_email']);
        $use_default = urlencode($_GET['use_default']);
        $smtp_server = urlencode($_GET['smtp_server']);
        $smtp_port = urlencode($_GET['smtp_port']);
        $smtp_auth = urlencode($_GET['smtp_auth']);
        $smtp_pass = urlencode($_GET['smtp_pass']);
        $smtp_secure = urlencode($_GET['smtp_secure']);
        $action = "mem_config/update_email_config?";
        $args = 'ks=' . $ks . '&from_name=' . $from_name . '&from_email=' . $from_email . '&use_default=' . $use_default . '&smtp_server=' . $smtp_server . '&smtp_port=' . $smtp_port . '&smtp_auth=' . $smtp_auth . '&smtp_pass=' . $smtp_pass . '&smtp_secure=' . $smtp_secure;
        echo $this->curl_request($action, $args);
    }

    public function add_email_reg() {
        $ks = urlencode($_GET['ks']);
        $reg_default = $_GET['reg_default'];
        $reg_subject = urlencode($_GET['reg_subject']);
        $reg_body = urlencode($_GET['reg_body']);
        $action = "mem_config/add_email_reg_config?";
        $args = "reg_default=" . $reg_default . "&reg_subject=" . $reg_subject . "&reg_body=" . $reg_body . '&ks=' . $ks;
        echo $this->curl_request($action, $args);
    }

    public function add_email_pswd() {
        $ks = urlencode($_GET['ks']);
        $pswd_default = $_GET['pswd_default'];
        $pswd_subject = urlencode($_GET['pswd_subject']);
        $pswd_body = urlencode($_GET['pswd_body']);
        $action = "mem_config/add_email_pswd_config?";
        $args = "pswd_default=" . $pswd_default . "&pswd_subject=" . $pswd_subject . "&pswd_body=" . $pswd_body . '&ks=' . $ks;
        echo $this->curl_request($action, $args);
    }

    public function list_actype() {
        $ks = urlencode($_GET['ks']);
        $start = $_GET['start'];
        $length = $_GET['length'];
        $search = urlencode($_GET['search']);
        $draw = $_GET['draw'];
        $action = "mem_config/list_ac_type?";
        $args = "start=" . $start . "&length=" . $length . "&draw=" . $draw . '&ks=' . $ks;
        echo $this->curl_request($action, $args);
    }

    public function get_player_details() {
        $uiconf = urlencode($_GET['uiconf']);
        $action = "mem_config/get_player_details?";
        $args = 'uiconf=' . $uiconf;
        echo $this->curl_request($action, $args);
    }

    public function login_user() {
        $sm_ak = urlencode($_GET['sm_ak']);
        $un = urlencode($_GET['un']);
        $pswd = urlencode($_GET['pswd']);
        $type = $_GET['type'];
        $entryId = $_GET['entryId'];
        $action = "mem_user/login_user?";
        $args = 'un=' . $un . '&pswd=' . $pswd . "&sm_ak=" . $sm_ak . "&type=" . $type . "&entryId=" . $entryId;
        echo $this->curl_request($action, $args);
    }

    public function is_logged_in() {
        $auth_key = urlencode($_GET['auth_key']);
        $sm_ak = urlencode($_GET['sm_ak']);
        $type = $_GET['type'];
        $entryId = $_GET['entryId'];
        $action = "mem_user/is_logged_in?";
        $args = 'auth_key=' . $auth_key . "&sm_ak=" . $sm_ak . "&type=" . $type . "&entryId=" . $entryId;
        echo $this->curl_request($action, $args);
    }

    public function get_user_name() {
        $sm_ak = urlencode($_GET['sm_ak']);
        $uid = urlencode($_GET['uid']);
        $action = "mem_user/get_user_name?";
        $args = 'uid=' . $uid . "&sm_ak=" . $sm_ak;
        echo $this->curl_request($action, $args);
    }

    public function create_auth_key() {
        $sm_ak = urlencode($_GET['sm_ak']);
        $un = urlencode($_GET['un']);
        $user_id = urlencode($_GET['user_id']);
        $action = "mem_user/create_auth_key?";
        $args = 'user_id=' . $user_id . '&un=' . $un . "&sm_ak=" . $sm_ak;
        echo $this->curl_request($action, $args);
    }

    public function setup_player() {
        $sm_ak = urlencode($_GET['sm_ak']);
        $entry_id = urlencode($_GET['entry_id']);
        $action = "mem_config/setup_player?";
        $args = 'entry_id=' . $entry_id . '&sm_ak=' . $sm_ak;
        echo $this->curl_request($action, $args);
    }

    public function w_get_thumb() {
        $sm_ak = urlencode($_GET['sm_ak']);
        $entry_id = urlencode($_GET['entry_id']);
        $action = "mem_config/w_get_thumb?";
        $args = 'entry_id=' . $entry_id . '&sm_ak=' . $sm_ak;
        echo $this->curl_request($action, $args);
    }

    public function get_thumb() {
        $ks = urlencode($_GET['ks']);
        $entry_id = urlencode($_GET['entry_id']);
        $action = "mem_config/get_thumb?";
        $args = 'entry_id=' . $entry_id . '&ks=' . $ks;
        echo $this->curl_request($action, $args);
    }

    public function w_get_cat_thumb() {
        $sm_ak = urlencode($_GET['sm_ak']);
        $cat_id = urlencode($_GET['cat_id']);
        $action = "mem_config/w_get_cat_thumb?";
        $args = 'cat_id=' . $cat_id . '&sm_ak=' . $sm_ak;
        echo $this->curl_request($action, $args);
    }

    public function get_cat_thumb() {
        $ks = urlencode($_GET['ks']);
        $cat_id = urlencode($_GET['cat_id']);
        $action = "mem_config/get_cat_thumb?";
        $args = 'cat_id=' . $cat_id . '&ks=' . $ks;
        echo $this->curl_request($action, $args);
    }

    public function check_update_ac() {
        $ks = urlencode($_GET['ks']);
        $playlist_id = urlencode($_GET['playlist_id']);
        $playlist = urlencode($_GET['playlist']);
        $action = "mem_entry/check_update_ac?";
        $args = 'playlist_id=' . $playlist_id . '&ks=' . $ks . '&playlist=' . $playlist;
        echo $this->curl_request($action, $args);
    }

    public function delete_playlist_entry() {
        $ks = urlencode($_GET['ks']);
        $playlist_id = urlencode($_GET['playlist_id']);
        $action = "mem_entry/delete_playlist_entry?";
        $args = 'playlist_id=' . $playlist_id . '&ks=' . $ks;
        echo $this->curl_request($action, $args);
    }

    public function get_cat_entries() {
        $sm_ak = urlencode($_GET['sm_ak']);
        $cat_id = urlencode($_GET['cat_id']);
        $action = "mem_entry/get_cat_entries?";
        $args = 'cat_id=' . $cat_id . '&sm_ak=' . $sm_ak;
        echo $this->curl_request($action, $args);
    }

    public function update_platform_cat() {
        $ks = urlencode($_GET['ks']);
        $cat = urlencode($_GET['cat']);
        $entry_id = urlencode($_GET['entry_id']);
        $action = "mem_entry/update_platform_cat?";
        $args = 'cat=' . $cat . '&ks=' . $ks . '&entry_id=' . $entry_id;
        echo $this->curl_request($action, $args);
    }

    public function update_drag_cat() {
        $ks = urlencode($_GET['ks']);
        $cat_id = urlencode($_GET['cat_id']);
        $entry_id = urlencode($_GET['entry_id']);
        $action = "mem_entry/update_drag_cat?";
        $args = 'cat_id=' . $cat_id . '&ks=' . $ks . '&entry_id=' . $entry_id;
        echo $this->curl_request($action, $args);
    }

    public function delete_platform_cat() {
        $ks = urlencode($_GET['ks']);
        $cat_id = urlencode($_GET['cat_id']);
        $action = "mem_entry/delete_platform_cat?";
        $args = 'cat_id=' . $cat_id . '&ks=' . $ks;
        echo $this->curl_request($action, $args);
    }

    public function reset_request() {
        $sm_ak = urlencode($_GET['sm_ak']);
        $email = urlencode($_GET['email']);
        $url = urlencode($_GET['url']);
        $action = "mem_user/reset_request?";
        $args = 'email=' . $email . '&sm_ak=' . $sm_ak . '&url=' . $url;
        echo $this->curl_request($action, $args);
    }

    public function reset_pass() {
        $sm_ak = urlencode($_GET['sm_ak']);
        $email = urlencode($_GET['email']);
        $reset_token = urlencode($_GET['reset_token']);
        $pass = urlencode($_GET['pass']);
        $action = "mem_user/reset_pass?";
        $args = 'email=' . $email . '&sm_ak=' . $sm_ak . '&reset_token=' . $reset_token . '&pass=' . $pass;
        echo $this->curl_request($action, $args);
    }

    public function reset_email() {
        $sm_ak = urlencode($_GET['sm_ak']);
        $email = urlencode($_GET['email']);
        $new_email = urlencode($_GET['new_email']);
        $reset_token = urlencode($_GET['reset_token']);
        $pass = urlencode($_GET['pass']);
        $action = "mem_user/reset_email?";
        $args = 'email=' . $email . '&sm_ak=' . $sm_ak . '&reset_token=' . $reset_token . '&pass=' . $pass . '&new_email=' . $new_email;
        echo $this->curl_request($action, $args);
    }

    public function is_active() {
        $sm_ak = urlencode($_GET['sm_ak']);
        $uid = urlencode($_GET['uid']);
        $action = "mem_user/is_active?";
        $args = "sm_ak=" . $sm_ak . "&uid=" . $uid;
        echo $this->curl_request($action, $args);
    }

    public function is_not_active() {
        $sm_ak = urlencode($_GET['sm_ak']);
        $uid = urlencode($_GET['uid']);
        $action = "mem_user/is_not_active?";
        $args = "sm_ak=" . $sm_ak . "&uid=" . $uid;
        echo $this->curl_request($action, $args);
    }

    public function activate_cat_entry() {
        $entryId = urlencode($_GET['entryId']);
        $sm_ak = urlencode($_GET['sm_ak']);
        $is_logged_in = urlencode($_GET['is_logged_in']);
        $action = "mem_user/activate_cat_entry?";
        $args = 'entryId=' . $entryId . "&sm_ak=" . $sm_ak . "&is_logged_in=" . $is_logged_in;
        echo $this->curl_request($action, $args);
    }

    public function user_concurrent_status() {
        $ks = urlencode($_GET['ks']);
        $concurrent = $_GET['concurrent'];
        $action = "mem_user/user_concurrent_status?";
        $args = "concurrent=" . $concurrent . "&ks=" . $ks;
        echo $this->curl_request($action, $args);
    }

    public function get_concurrent_status() {
        $ks = urlencode($_GET['ks']);
        $action = "mem_user/get_concurrent_status?";
        $args = "";
        echo $this->curl_request($action, $args);
    }

    public function user_activation_skip_status() {
        $ks = urlencode($_GET['ks']);
        $skip = $_GET['skip'];
        $action = "mem_user/user_activation_skip_status?";
        $args = "skip=" . $skip . "&ks=" . $ks;
        echo $this->curl_request($action, $args);
    }

    public function get_activation_skip_status() {
        $ks = urlencode($_GET['ks']);
        $action = "mem_user/get_activation_skip_status?";
        $args = "";
        echo $this->curl_request($action, $args);
    }

    public function get_owner_attrs() {
        $ks = urlencode($_GET['ks']);
        $action = "mem_user/get_owner_attrs?";
        $args = "ks=" . $ks;
        echo $this->curl_request($action, $args);
    }

    public function update_reg_fields() {
        $ks = urlencode($_GET['ks']);
        $newFields = urlencode($_GET['newFields']);
        $updateFields = urlencode($_GET['updateFields']);
        $removeFields = urlencode($_GET['removeFields']);
        $action = "mem_user/update_reg_fields?";
        $args = "ks=" . $ks . "&new_fields=" . $newFields . "&update_fields=" . $updateFields . "&remove_fields=" . $removeFields;
        echo $this->curl_request($action, $args);
    }

    public function update_user_details() {
        $ks = urlencode($_GET['ks']);
        $uid = urlencode($_GET['uid']);
        $updateFields = urlencode($_GET['updateFields']);
        $action = "mem_user/update_user_details?";
        $args = "ks=" . $ks . "&uid=" . $uid . "&update_fields=" . $updateFields;
        echo $this->curl_request($action, $args);
    }

    public function get_user_details() {
        $sm_ak = urlencode($_GET['sm_ak']);
        $uid = urlencode($_GET['uid']);
        $action = "mem_user/get_user_details?";
        $args = 'uid=' . $uid . "&sm_ak=" . $sm_ak;
        echo $this->curl_request($action, $args);
    }

    public function update_fname() {
        $sm_ak = urlencode($_GET['sm_ak']);
        $auth_key = urlencode($_GET['auth_key']);
        $fname = urlencode($_GET['fname']);
        $type = $_GET['type'];
        $entryId = $_GET['entryId'];
        $action = "mem_user/update_fname?";
        $args = "sm_ak=" . $sm_ak . "&auth_key=" . $auth_key . "&fname=" . $fname . "&type=" . $type . "&entryId=" . $entryId;
        echo $this->curl_request($action, $args);
    }

    public function update_lname() {
        $sm_ak = urlencode($_GET['sm_ak']);
        $auth_key = urlencode($_GET['auth_key']);
        $lname = urlencode($_GET['lname']);
        $type = $_GET['type'];
        $entryId = $_GET['entryId'];
        $action = "mem_user/update_lname?";
        $args = "sm_ak=" . $sm_ak . "&auth_key=" . $auth_key . "&lname=" . $lname . "&type=" . $type . "&entryId=" . $entryId;
        echo $this->curl_request($action, $args);
    }

    public function reset_psswd_request() {
        $sm_ak = urlencode($_GET['sm_ak']);
        $email = urlencode($_GET['email']);
        $url = urlencode($_GET['url']);
        $action = "mem_user/reset_psswd_request?";
        $args = 'email=' . $email . '&sm_ak=' . $sm_ak . '&url=' . $url;
        echo $this->curl_request($action, $args);
    }

    public function reset_email_request() {
        $sm_ak = urlencode($_GET['sm_ak']);
        $email = urlencode($_GET['email']);
        $url = urlencode($_GET['url']);
        $action = "mem_user/reset_email_request?";
        $args = 'email=' . $email . '&sm_ak=' . $sm_ak . '&url=' . $url;
        echo $this->curl_request($action, $args);
    }

}

$mem = new mem();
$mem->run();
?>
