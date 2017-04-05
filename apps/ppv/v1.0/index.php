<?php

//header('Access-Control-Allow-Origin: *');
class ppv {

    protected $pid;
    protected $action;

    public function __construct() {
        isset($_GET["pid"]) ? $this->pid = $_GET["pid"] : $this->pid = '';
        isset($_GET["action"]) ? $this->action = $_GET["action"] : $this->action = '';
    }

    //run ppv api
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
            case "get_user_orders":
                $this->get_user_orders();
                break;
            case "w_get_user_orders":
                $this->w_get_user_orders();
                break;
            case "list_orders":
                $this->list_orders();
                break;
            case "update_user_restriction":
                $this->update_user_restriction();
                break;
            case "delete_order":
                $this->delete_order();
                break;
            case "w_delete_order":
                $this->w_delete_order();
                break;
            case "list_tickets":
                $this->list_tickets();
                break;
            case "get_ticket_names":
                $this->get_ticket_name();
                break;
            case "add_ticket":
                $this->add_ticket();
                break;
            case "update_ticket":
                $this->update_ticket();
                break;
            case "delete_ticket":
                $this->delete_ticket();
                break;
            case "update_ticket_status":
                $this->update_ticket_status();
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
            case "list_ac":
                $this->list_ac();
                break;
            case "delete_ac":
                $this->delete_ac();
                break;
            case "update_ac":
                $this->update_ac();
                break;
            case "add_ac":
                $this->add_ac();
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
            case "add_email_ty":
                $this->add_email_ty();
                break;
            case "add_email_pswd":
                $this->add_email_pswd();
                break;
            case "add_gateway":
                $this->add_gateway();
                break;
            case "add_paypal_config":
                $this->add_paypal_config();
                break;
            case "add_authnet_config":
                $this->add_authnet_config();
                break;
            case "get_gateways":
                $this->get_gateways();
                break;
            case "list_entryTickets":
                $this->list_entryTickets();
                break;
            case "list_actype":
                $this->list_actype();
                break;
            case "update_setup":
                $this->update_setup();
                break;
            case "get_tickets":
                $this->get_tickets();
                break;
            case "get_player_details":
                $this->get_player_details();
                break;
            case "login_user":
                $this->login_user();
                break;
            case "is_logged_in":
                $this->is_logged_in();
                break;
            case "check_inventory":
                $this->check_inventory();
                break;
            case "check_cat_inventory":
                $this->check_cat_inventory();
                break;
            case "update_user_views":
                $this->update_user_views();
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
            case "get_user_name":
                $this->get_user_name();
                break;
            case "get_confirm":
                $this->get_confirm();
                break;
            case "add_order":
                $this->add_order();
                break;
            case "create_auth_key":
                $this->create_auth_key();
                break;
            case "update_order_status":
                $this->update_order_status();
                break;
            case "complete_order":
                $this->complete_order();
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
            case "update_playlist_entry":
                $this->update_playlist_entry();
                break;
            case "check_update_ac":
                $this->check_update_ac();
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
            case "refund_order":
                $this->refund_order();
                break;
            case "finish_order":
                $this->finish_order();
                break;
            case "activate_account":
                $this->activate_account();
                break;
            case "reset_psswd_request":
                $this->reset_psswd_request();
                break;
            case "reset_email_request":
                $this->reset_email_request();
                break;
            case "reset_pass":
                $this->reset_pass();
                break;
            case "reset_email":
                $this->reset_email();
                break;
            case "list_affiliates":
                $this->list_affiliates();
                break;
            case "create_affiliate":
                $this->create_affiliate();
                break;
            case "update_affiliate":
                $this->update_affiliate();
                break;
            case "delete_affiliate":
                $this->delete_affiliate();
                break;
            case "update_affiliate_status":
                $this->update_affiliate_status();
                break;
            case "list_campaigns":
                $this->list_campaigns();
                break;
            case "create_campaign":
                $this->create_campaign();
                break;
            case "update_campaign":
                $this->update_campaign();
                break;
            case "update_campaign_status":
                $this->update_campaign_status();
                break;
            case "delete_campaign":
                $this->delete_campaign();
                break;
            case "list_marketing":
                $this->list_marketing();
                break;
            case "get_marketing_data":
                $this->get_marketing_data();
                break;
            case "create_link":
                $this->create_link();
                break;
            case "update_link":
                $this->update_link();
                break;
            case "update_link_status":
                $this->update_link_status();
                break;
            case "delete_link":
                $this->delete_link();
                break;
            case "get_user_comms":
                $this->get_user_comms();
                break;
            case "update_user_comms_status":
                $this->update_user_comms_status();
                break;
            case "list_commissions":
                $this->list_commissions();
                break;
            case "delete_commission":
                $this->delete_commission();
                break;
            case "w_get_user_subs":
                $this->w_get_user_subs();
                break;
            case "list_subs":
                $this->list_subs();
                break;
            case "cancel_sub":
                $this->cancel_sub();
                break;
            case "w_cancel_sub":
                $this->w_cancel_sub();
                break;
            case "delete_sub":
                $this->delete_sub();
                break;
            case "w_delete_sub":
                $this->w_delete_sub();
                break;
            case "update_sub_status":
                $this->update_sub_status();
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
            case "get_checkout_details":
                $this->get_checkout_details();
                break;
            case "cancel_order":
                $this->cancel_order();
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
        $action = "ppv_user/list_user?";
        $args = "start=" . $start . "&length=" . $length . "&search=" . $search['value'] . "&draw=" . $draw . "&ks=" . $ks;
        echo $this->curl_request($action, $args);
    }

    public function create_account() {
        $ks = urlencode($_GET['ks']);
        $fname = urlencode($_GET['fname']);
        $lname = urlencode($_GET['lname']);
        $email = urlencode($_GET['email']);
        $pass = urlencode($_GET['pass']);
        $restriction = urlencode($_GET['restriction']);
        $status = urlencode($_GET['status']);
        $tz = urlencode($_GET['tz']);
        $action = "ppv_user/add_user?";
        $args = "tz=" . $tz . "&fname=" . $fname . "&lname=" . $lname . "&email=" . $email . "&pass=" . $pass . "&restriction=" . $restriction . "&status=" . $status . "&ks=" . $ks;
        echo $this->curl_request($action, $args);
    }

    public function create_affiliate() {
        $ks = urlencode($_GET['ks']);
        $fname = urlencode($_GET['fname']);
        $lname = urlencode($_GET['lname']);
        $email = urlencode($_GET['email']);
        $phone = urlencode($_GET['phone']);
        $fax = urlencode($_GET['fax']);
        $address1 = urlencode($_GET['address1']);
        $address2 = urlencode($_GET['address2']);
        $city = urlencode($_GET['city']);
        $state = urlencode($_GET['state']);
        $zip = urlencode($_GET['zip']);
        $country = urlencode($_GET['country']);
        $company = urlencode($_GET['company']);
        $website = urlencode($_GET['website']);
        $ppemail = urlencode($_GET['ppemail']);
        $status = urlencode($_GET['status']);
        $tz = urlencode($_GET['tz']);
        $action = "ppv_affiliate/add_affiliate?";
        $args = "tz=" . $tz . "&fname=" . $fname . "&lname=" . $lname . "&email=" . $email . "&phone=" . $phone . "&fax=" . $fax . "&address1=" . $address1 . "&address2=" . $address2 . "&city=" . $city . "&state=" . $state . "&zip=" . $zip . "&country=" . $country . "&company=" . $company . "&website=" . $website . "&ppemail=" . $ppemail . "&status=" . $status . "&ks=" . $ks;
        echo $this->curl_request($action, $args);
    }

    public function create_campaign() {
        $ks = urlencode($_GET['ks']);
        $name = urlencode($_GET['name']);
        $desc = urlencode($_GET['desc']);
        $cookie = urlencode($_GET['cookie']);
        $comm = urlencode($_GET['comm']);
        $comm_type = urlencode($_GET['comm_type']);
        $status = urlencode($_GET['status']);
        $tz = urlencode($_GET['tz']);
        $action = "ppv_affiliate/add_campaign?";
        $args = "tz=" . $tz . "&name=" . $name . "&desc=" . $desc . "&cookie=" . $cookie . "&comm=" . $comm . "&comm_type=" . $comm_type . "&status=" . $status . "&ks=" . $ks;
        echo $this->curl_request($action, $args);
    }

    public function update_campaign() {
        $ks = urlencode($_GET['ks']);
        $cid = urlencode($_GET['cid']);
        $name = urlencode($_GET['name']);
        $desc = urlencode($_GET['desc']);
        $cookie = urlencode($_GET['cookie']);
        $comm = urlencode($_GET['comm']);
        $comm_type = urlencode($_GET['comm_type']);
        $tz = urlencode($_GET['tz']);
        $action = "ppv_affiliate/update_campaign?";
        $args = "tz=" . $tz . "&name=" . $name . "&desc=" . $desc . "&cookie=" . $cookie . "&comm=" . $comm . "&comm_type=" . $comm_type . "&ks=" . $ks . "&cid=" . $cid;
        echo $this->curl_request($action, $args);
    }

    public function update_affiliate() {
        $aid = urlencode($_GET['aid']);
        $ks = urlencode($_GET['ks']);
        $fname = urlencode($_GET['fname']);
        $lname = urlencode($_GET['lname']);
        $email = urlencode($_GET['email']);
        $phone = urlencode($_GET['phone']);
        $fax = urlencode($_GET['fax']);
        $address1 = urlencode($_GET['address1']);
        $address2 = urlencode($_GET['address2']);
        $city = urlencode($_GET['city']);
        $state = urlencode($_GET['state']);
        $zip = urlencode($_GET['zip']);
        $country = urlencode($_GET['country']);
        $company = urlencode($_GET['company']);
        $website = urlencode($_GET['website']);
        $ppemail = urlencode($_GET['ppemail']);
        $tz = urlencode($_GET['tz']);
        $action = "ppv_affiliate/update_affiliate?";
        $args = "tz=" . $tz . "&fname=" . $fname . "&lname=" . $lname . "&email=" . $email . "&phone=" . $phone . "&fax=" . $fax . "&address1=" . $address1 . "&address2=" . $address2 . "&city=" . $city . "&state=" . $state . "&zip=" . $zip . "&country=" . $country . "&company=" . $company . "&website=" . $website . "&ppemail=" . $ppemail . "&ks=" . $ks . "&aid=" . $aid;
        echo $this->curl_request($action, $args);
    }

    public function activate_account() {
        $sm_ak = urlencode($_GET['sm_ak']);
        $akey = urlencode($_GET['akey']);
        $tz = urlencode($_GET['tz']);
        $email = urlencode($_GET['email']);
        $action = "ppv_user/activate_user?";
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
        $action = "ppv_user/register_user?";
        $args = "tz=" . $tz . "&fname=" . $fname . "&lname=" . $lname . "&email=" . $email . "&pass=" . $pass . "&sm_ak=" . $sm_ak . "&url=" . $url . "&attrs=" . $attrs;
        echo $this->curl_request($action, $args);
    }

    public function delete_account() {
        $ks = urlencode($_GET['ks']);
        $uid = $_GET['uid'];
        $tz = $_GET['tz'];
        $action = "ppv_user/delete_user?";
        $args = "tz=" . $tz . "&uid=" . $uid . "&ks=" . $ks;
        echo $this->curl_request($action, $args);
    }

    public function destroy_session() {
        $ks = urlencode($_GET['ks']);
        $uid = $_GET['uid'];
        $action = "ppv_user/destroy_session?";
        $args = "uid=" . $uid . "&ks=" . $ks;
        echo $this->curl_request($action, $args);
    }

    public function delete_affiliate() {
        $ks = urlencode($_GET['ks']);
        $email = $_GET['email'];
        $tz = $_GET['tz'];
        $action = "ppv_affiliate/delete_affiliate?";
        $args = "tz=" . $tz . "&email=" . $email . "&ks=" . $ks;
        echo $this->curl_request($action, $args);
    }

    public function delete_campaign() {
        $ks = urlencode($_GET['ks']);
        $cid = $_GET['cid'];
        $tz = $_GET['tz'];
        $action = "ppv_affiliate/delete_campaign?";
        $args = "tz=" . $tz . "&cid=" . $cid . "&ks=" . $ks;
        echo $this->curl_request($action, $args);
    }

    public function update_user_status() {
        $tz = $_GET['tz'];
        $ks = urlencode($_GET['ks']);
        $email = $_GET['email'];
        $status = $_GET['status'];
        $uid = $_GET['uid'];
        $action = "ppv_user/update_user_status?";
        $args = "tz=" . $tz . "&email=" . $email . "&status=" . $status . "&uid=" . $uid . "&ks=" . $ks;
        echo $this->curl_request($action, $args);
    }

    public function update_affiliate_status() {
        $tz = $_GET['tz'];
        $ks = urlencode($_GET['ks']);
        $email = $_GET['email'];
        $status = $_GET['status'];
        $action = "ppv_affiliate/update_affiliate_status?";
        $args = "tz=" . $tz . "&email=" . $email . "&status=" . $status . "&ks=" . $ks;
        echo $this->curl_request($action, $args);
    }

    public function update_campaign_status() {
        $tz = $_GET['tz'];
        $ks = urlencode($_GET['ks']);
        $cid = $_GET['cid'];
        $status = $_GET['status'];
        $action = "ppv_affiliate/update_campaign_status?";
        $args = "tz=" . $tz . "&email=" . $email . "&status=" . $status . "&ks=" . $ks . "&cid=" . $cid;
        echo $this->curl_request($action, $args);
    }

    public function update_user_restriction() {
        $ks = urlencode($_GET['ks']);
        $tz = $_GET['tz'];
        $email = $_GET['email'];
        $restriction = $_GET['restriction'];
        $action = "ppv_user/update_user_restriction?";
        $args = "tz=" . $tz . "&email=" . $email . "&restriction=" . $restriction . "&ks=" . $ks;
        echo $this->curl_request($action, $args);
    }

    public function reset_pswd() {
        $ks = urlencode($_GET['ks']);
        $tz = $_GET['tz'];
        $email = $_GET['email'];
        $pass = urlencode($_GET['pass']);
        $action = "ppv_user/update_user_pswd?";
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
        $action = "ppv_user/update_user?";
        $args = "tz=" . $tz . "&fname=" . $fname . "&lname=" . $lname . "&email=" . $email . "&uid=" . $uid . "&ks=" . $ks;
        echo $this->curl_request($action, $args);
    }

    public function get_user_orders() {
        $ks = urlencode($_GET['ks']);
        $start = $_GET['start'];
        $length = $_GET['length'];
        $search = urlencode($_GET['search']);
        $draw = $_GET['draw'];
        $uid = $_GET['uid'];
        $tz = $_GET['tz'];
        $action = "ppv_orders/list_user_orders?";
        $args = "tz=" . $tz . "&uid=" . $uid . "&start=" . $start . "&length=" . $length . "&search=" . $search['value'] . "&draw=" . $draw . "&ks=" . $ks;
        echo $this->curl_request($action, $args);
    }

    public function w_get_user_orders() {
        $sm_ak = urlencode($_GET['sm_ak']);
        $auth_key = urlencode($_GET['auth_key']);
        $start = $_GET['start'];
        $length = $_GET['length'];
        $draw = $_GET['draw'];
        $uid = $_GET['uid'];
        $action = "ppv_orders/w_list_user_orders?";
        $args = "uid=" . $uid . "&start=" . $start . "&length=" . $length . "&draw=" . $draw . "&sm_ak=" . $sm_ak . "&auth_key=" . $auth_key;
        echo $this->curl_request($action, $args);
    }

    public function w_get_user_subs() {
        $sm_ak = urlencode($_GET['sm_ak']);
        $auth_key = urlencode($_GET['auth_key']);
        $start = $_GET['start'];
        $length = $_GET['length'];
        $draw = $_GET['draw'];
        $uid = $_GET['uid'];
        $action = "ppv_orders/w_list_user_subs?";
        $args = "uid=" . $uid . "&start=" . $start . "&length=" . $length . "&draw=" . $draw . "&sm_ak=" . $sm_ak . "&auth_key=" . $auth_key;
        echo $this->curl_request($action, $args);
    }

    public function list_orders() {
        $ks = urlencode($_GET['ks']);
        $start = $_GET['start'];
        $length = $_GET['length'];
        $search = $_GET['search'];
        $draw = $_GET['draw'];
        $tz = $_GET['tz'];
        $action = "ppv_orders/list_order?";
        $args = "tz=" . $tz . "&start=" . $start . "&length=" . $length . "&search=" . $search['value'] . "&draw=" . $draw . "&ks=" . $ks;
        echo $this->curl_request($action, $args);
    }

    public function delete_order() {
        $ks = urlencode($_GET['ks']);
        $order_id = $_GET['order_id'];
        $action = "ppv_orders/delete_order?";
        $args = "order_id=" . $order_id . "&ks=" . $ks;
        echo $this->curl_request($action, $args);
    }

    public function w_delete_order() {
        $sm_ak = urlencode($_GET['sm_ak']);
        $order_id = $_GET['order_id'];
        $action = "ppv_orders/w_delete_order?";
        $args = "order_id=" . $order_id . "&sm_ak=" . $sm_ak;
        echo $this->curl_request($action, $args);
    }

    public function refund_order() {
        $ks = urlencode($_GET['ks']);
        $order_id = $_GET['order_id'];
        $ticket_type = $_GET['ticket_type'];
        $action = "ppv_orders/refund_order?";
        $args = "order_id=" . $order_id . "&ks=" . $ks . "&ticket_type=" . $ticket_type;
        echo $this->curl_request($action, $args);
    }

    public function list_tickets() {
        $ks = urlencode($_GET['ks']);
        $start = $_GET['start'];
        $length = $_GET['length'];
        $search = $_GET['search'];
        $draw = $_GET['draw'];
        $currency = $_GET['currency'];
        $action = "ppv_ticket/list_tickets?";
        $args = "start=" . $start . "&length=" . $length . "&search=" . $search['value'] . "&draw=" . $draw . "&currency=" . $currency . "&ks=" . $ks;
        echo $this->curl_request($action, $args);
    }

    public function get_ticket_name() {
        $ks = urlencode($_GET['ks']);
        $ticket_ids = $_GET['ticket_ids'];
        $action = "ppv_ticket/get_ticket_name?";
        $args = "ids=" . $ticket_ids . "&ks=" . $ks;
        echo $this->curl_request($action, $args);
    }

    public function add_ticket() {
        $ks = urlencode($_GET['ks']);
        $ticket_name = urlencode($_GET['ticket_name']);
        $ticket_desc = urlencode($_GET['ticket_desc']);
        $ticket_price = urlencode($_GET['ticket_price']);
        $ticket_type = urlencode($_GET['ticket_type']);
        $expires = $_GET['expires'];
        $expiry_config = $_GET['expiry_config'];
        $max_views = $_GET['max_views'];
        $status = $_GET['status'];
        $tz = $_GET['tz'];
        $currency = $_GET['currency'];
        $action = "ppv_ticket/add_ticket?";
        $args = "tz=" . $tz . "&ticket_name=" . $ticket_name . "&ticket_desc=" . $ticket_desc . "&ticket_price=" . $ticket_price . "&ticket_type=" . $ticket_type . "&expires=" . $expires . "&expiry_config=" . $expiry_config . "&max_views=" . $max_views . "&status=" . $status . "&currency=" . $currency . "&ks=" . $ks;
        echo $this->curl_request($action, $args);
    }

    public function update_ticket() {
        $ks = urlencode($_GET['ks']);
        $ticket_id = $_GET['ticket_id'];
        $ticket_name = urlencode($_GET['ticket_name']);
        $ticket_desc = urlencode($_GET['ticket_desc']);
        $ticket_price = urlencode($_GET['ticket_price']);
        $ticket_type = urlencode($_GET['ticket_type']);
        $expires = $_GET['expires'];
        $expiry_config = $_GET['expiry_config'];
        $max_views = $_GET['max_views'];
        $tz = $_GET['tz'];
        $action = "ppv_ticket/update_ticket?";
        $args = "tz=" . $tz . "&ticket_id=" . $ticket_id . "&ticket_name=" . $ticket_name . "&ticket_desc=" . $ticket_desc . "&ticket_price=" . $ticket_price . "&ticket_type=" . $ticket_type . "&expires=" . $expires . "&expiry_config=" . $expiry_config . "&max_views=" . $max_views . "&ks=" . $ks;
        echo $this->curl_request($action, $args);
    }

    public function delete_ticket() {
        $ks = urlencode($_GET['ks']);
        $ticket_id = $_GET['ticket_id'];
        $action = "ppv_ticket/delete_ticket?";
        $args = "ticket_id=" . $ticket_id . "&ks=" . $ks;
        echo $this->curl_request($action, $args);
    }

    public function update_ticket_status() {
        $ks = urlencode($_GET['ks']);
        $tz = $_GET['tz'];
        $ticket_id = $_GET['ticket_id'];
        $status = $_GET['status'];
        $action = "ppv_ticket/update_ticket_status?";
        $args = "tz=" . $tz . "&ticket_id=" . $ticket_id . "&status=" . $status . "&ks=" . $ks;
        echo $this->curl_request($action, $args);
    }

    public function list_entries() {
        $ks = urlencode($_GET['ks']);
        $start = $_GET['start'];
        $length = $_GET['length'];
        $search = $_GET['search'];
        $draw = $_GET['draw'];
        $action = "ppv_entry/list_entry?";
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
        $ticket_ids = urlencode($_GET['ticket_ids']);
        $status = urlencode($_GET['status']);
        $action = "ppv_entry/add_entry?";
        $args = "kentry_id=" . $kentry_id . "&kentry_name=" . $kentry_name . "&media_type=" . $media_type . "&media_type=" . $media_type . "&ac_id=" . $ac_id . "&ac_name=" . $ac_name . "&ticket_ids=" . $ticket_ids . "&status=" . $status . "&ks=" . $ks;
        echo $this->curl_request($action, $args);
    }

    public function update_entry() {
        $ks = urlencode($_GET['ks']);
        $kentry_id = urlencode($_GET['kentry_id']);
        $kentry_name = urlencode($_GET['kentry_name']);
        $media_type = urlencode($_GET['media_type']);
        $ac_id = urlencode($_GET['ac_id']);
        $ac_name = urlencode($_GET['ac_name']);
        $ticket_ids = urlencode($_GET['ticket_ids']);
        $action = "ppv_entry/update_entry?";
        $args = "kentry_id=" . $kentry_id . "&kentry_name=" . $kentry_name . "&media_type=" . $media_type . "&media_type=" . $media_type . "&ac_id=" . $ac_id . "&ac_name=" . $ac_name . "&ticket_ids=" . $ticket_ids . "&ks=" . $ks;
        echo $this->curl_request($action, $args);
    }

    public function update_entry_status() {
        $ks = urlencode($_GET['ks']);
        $tz = $_GET['tz'];
        $kentry_id = $_GET['kentry_id'];
        $status = $_GET['status'];
        $action = "ppv_entry/update_entry_status?";
        $args = "tz=" . $tz . "&kentry_id=" . $kentry_id . "&status=" . $status . "&ks=" . $ks;
        echo $this->curl_request($action, $args);
    }

    public function delete_entry() {
        $ks = urlencode($_GET['ks']);
        $kentry_id = $_GET['kentry_id'];
        $media_type = $_GET['media_type'];
        $action = "ppv_entry/delete_entry?";
        $args = "kentry_id=" . $kentry_id . "&media_type=" . $media_type . "&ks=" . $ks;
        echo $this->curl_request($action, $args);
    }

    public function delete_platform_entry() {
        $ks = urlencode($_GET['ks']);
        $kentry_id = $_GET['kentry_id'];
        $action = "ppv_entry/delete_platform_entry?";
        $args = "kentry_id=" . $kentry_id . "&ks=" . $ks;
        echo $this->curl_request($action, $args);
    }

    public function list_ac() {
        $ks = urlencode($_GET['ks']);
        $iDisplayStart = $_GET['iDisplayStart'];
        $iDisplayLength = $_GET['iDisplayLength'];
        $iSortCol_0 = $_GET['iSortCol_0'];
        $sSortDir_0 = $_GET['sSortDir_0'];
        $sSearch = urlencode($_GET['sSearch']);
        $sEcho = $_GET['sEcho'];
        $action = "ppv_config/list_ac?";
        $args = "iDisplayStart=" . $iDisplayStart . "&iDisplayLength=" . $iDisplayLength . "&iSortCol_0=" . $iSortCol_0 . "&sSortDir_0=" . $sSortDir_0 . "&sSearch=" . $sSearch . "&sEcho=" . $sEcho . '&ks=' . $ks;
        echo $this->curl_request($action, $args);
    }

    public function delete_ac() {
        $id = $_GET['id'];
        $ks = urlencode($_GET['ks']);
        $action = "ppv_config/delete_ac?";
        $args = "id=" . $id . '&ks=' . $ks;
        echo $this->curl_request($action, $args);
    }

    public function update_ac() {
        $id = urlencode($_GET['id']);
        $ks = urlencode($_GET['ks']);
        $name = urlencode($_GET['name']);
        $desc = urlencode($_GET['desc']);
        $preview = urlencode($_GET['preview']);
        $preview_time = urlencode($_GET['preview_time']);
        $action = "ppv_config/update_ac?";
        $args = "id=" . $id . "&name=" . $name . "&desc=" . $desc . "&preview=" . $preview . "&preview_time=" . $preview_time . '&ks=' . $ks;
        echo $this->curl_request($action, $args);
    }

    public function add_ac() {
        $ks = urlencode($_GET['ks']);
        $name = urlencode($_GET['name']);
        $desc = urlencode($_GET['desc']);
        $preview = urlencode($_GET['preview']);
        $preview_time = urlencode($_GET['preview_time']);
        $action = "ppv_config/add_ac?";
        $args = "name=" . $name . "&desc=" . $desc . "&preview=" . $preview . "&preview_time=" . $preview_time . '&ks=' . $ks;
        echo $this->curl_request($action, $args);
    }

    public function get_email() {
        $ks = urlencode($_GET['ks']);
        $action = "ppv_config/get_email?";
        $args = 'ks=' . $ks;
        echo $this->curl_request($action, $args);
    }

    public function get_email_config() {
        $ks = urlencode($_GET['ks']);
        $action = "ppv_config/get_email_config?";
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
        $action = "ppv_config/update_email_config?";
        $args = 'ks=' . $ks . '&from_name=' . $from_name . '&from_email=' . $from_email . '&use_default=' . $use_default . '&smtp_server=' . $smtp_server . '&smtp_port=' . $smtp_port . '&smtp_auth=' . $smtp_auth . '&smtp_pass=' . $smtp_pass . '&smtp_secure=' . $smtp_secure;
        echo $this->curl_request($action, $args);
    }

    public function add_email_reg() {
        $ks = urlencode($_GET['ks']);
        $reg_default = $_GET['reg_default'];
        $reg_subject = urlencode($_GET['reg_subject']);
        $reg_body = urlencode($_GET['reg_body']);
        $action = "ppv_config/add_email_reg_config?";
        $args = "reg_default=" . $reg_default . "&reg_subject=" . $reg_subject . "&reg_body=" . $reg_body . '&ks=' . $ks;
        echo $this->curl_request($action, $args);
    }

    public function add_email_ty() {
        $ks = urlencode($_GET['ks']);
        $ty_default = $_GET['ty_default'];
        $ty_subject = urlencode($_GET['ty_subject']);
        $ty_body = urlencode($_GET['ty_body']);
        $action = "ppv_config/add_email_ty_config?";
        $args = "ty_default=" . $ty_default . "&ty_subject=" . $ty_subject . "&ty_body=" . $ty_body . '&ks=' . $ks;
        echo $this->curl_request($action, $args);
    }

    public function add_email_pswd() {
        $ks = urlencode($_GET['ks']);
        $pswd_default = $_GET['pswd_default'];
        $pswd_subject = urlencode($_GET['pswd_subject']);
        $pswd_body = urlencode($_GET['pswd_body']);
        $action = "ppv_config/add_email_pswd_config?";
        $args = "pswd_default=" . $pswd_default . "&pswd_subject=" . $pswd_subject . "&pswd_body=" . $pswd_body . '&ks=' . $ks;
        echo $this->curl_request($action, $args);
    }

    public function add_gateway() {
        $ks = urlencode($_GET['ks']);
        $gate_name = urlencode($_GET['gate_name']);
        $gate_status = urlencode($_GET['gate_status']);
        $action = "ppv_config/add_gateway?";
        $args = "gate_name=" . $gate_name . "&gate_status=" . $gate_status . '&ks=' . $ks;
        echo $this->curl_request($action, $args);
    }

    public function add_paypal_config() {
        $ks = urlencode($_GET['ks']);
        $api_user_id = urlencode($_GET['api_user_id']);
        $api_pswd = urlencode($_GET['api_pswd']);
        $api_sig = urlencode($_GET['api_sig']);
        $currency = urlencode($_GET['currency']);
        $setup = urlencode($_GET['setup']);
        $action = "ppv_config/add_paypal_config?";
        $args = "api_user_id=" . $api_user_id . "&api_pswd=" . $api_pswd . "&api_sig=" . $api_sig . "&currency=" . $currency . "&setup=" . $setup . '&ks=' . $ks;
        echo $this->curl_request($action, $args);
    }

    public function add_authnet_config() {
        $ks = urlencode($_GET['ks']);
        $api_login_id = urlencode($_GET['api_login_id']);
        $transaction_key = urlencode($_GET['transaction_key']);
        $currency = urlencode($_GET['currency']);
        $setup = urlencode($_GET['setup']);
        $action = "ppv_config/add_authnet_config?";
        $args = "api_login_id=" . $api_login_id . "&transaction_key=" . $transaction_key . "&currency=" . $currency . "&setup=" . $setup . '&ks=' . $ks;
        echo $this->curl_request($action, $args);
    }

    public function get_gateways() {
        $ks = urlencode($_GET['ks']);
        $action = "ppv_config/get_gateways?";
        $args = 'ks=' . $ks;
        echo $this->curl_request($action, $args);
    }

    public function list_entryTickets() {
        $ks = urlencode($_GET['ks']);
        $start = $_GET['start'];
        $length = $_GET['length'];
        $search = $_GET['search'];
        $draw = $_GET['draw'];
        $currency = $_GET['currency'];
        $action = "ppv_ticket/list_entry_tickets?";
        $args = "start=" . $start . "&length=" . $length . "&search=" . $search['value'] . "&draw=" . $draw . "&currency=" . $currency . '&ks=' . $ks;
        echo $this->curl_request($action, $args);
    }

    public function list_actype() {
        $ks = urlencode($_GET['ks']);
        $start = $_GET['start'];
        $length = $_GET['length'];
        $search = urlencode($_GET['search']);
        $draw = $_GET['draw'];
        $action = "ppv_config/list_ac_type?";
        $args = "start=" . $start . "&length=" . $length . "&draw=" . $draw . '&ks=' . $ks;
        echo $this->curl_request($action, $args);
    }

    public function update_setup() {
        $ks = urlencode($_GET['ks']);
        $setup = $_GET['setup'];
        $action = "ppv_config/update_setup?";
        $args = 'setup=' . $setup . '&ks=' . $ks;
        echo $this->curl_request($action, $args);
    }

    public function get_tickets() {
        $sm_ak = urlencode($_GET['sm_ak']);
        $kentry = urlencode($_GET['entryId']);
        $type = urlencode($_GET['type']);
        $protocol = urlencode($_GET['protocol']);
        $logged_in = urlencode($_GET['logged_in']);
        $has_start = urlencode($_GET['has_start']);
        $action = "ppv_ticket/get_tickets?";
        $args = 'kentry=' . $kentry . "&sm_ak=" . $sm_ak . "&type=" . $type . "&protocol=" . $protocol . "&logged_in=" . $logged_in . "&has_start=" . $has_start;
        echo $this->curl_request($action, $args);
    }

    public function get_player_details() {
        $uiconf = urlencode($_GET['uiconf']);
        $action = "ppv_config/get_player_details?";
        $args = 'uiconf=' . $uiconf;
        echo $this->curl_request($action, $args);
    }

    public function login_user() {
        $sm_ak = urlencode($_GET['sm_ak']);
        $un = urlencode($_GET['un']);
        $pswd = urlencode($_GET['pswd']);
        $entryId = $_GET['entryId'];
        $action = "ppv_user/login_user?";
        $args = 'un=' . $un . '&pswd=' . $pswd . "&sm_ak=" . $sm_ak . "&entryId=" . $entryId;
        echo $this->curl_request($action, $args);
    }

    public function is_logged_in() {
        $auth_key = urlencode($_GET['auth_key']);
        $sm_ak = urlencode($_GET['sm_ak']);
        $action = "ppv_user/is_logged_in?";
        $args = 'auth_key=' . $auth_key . "&sm_ak=" . $sm_ak;
        echo $this->curl_request($action, $args);
    }

    public function check_inventory() {
        $entryId = urlencode($_GET['entryId']);
        $uid = urlencode($_GET['uid']);
        $sm_ak = urlencode($_GET['sm_ak']);
        $type = urlencode($_GET['type']);
        $tz = urlencode($_GET['tz']);
        $action = "ppv_orders/check_inventory?";
        $args = 'entryId=' . $entryId . '&uid=' . $uid . "&sm_ak=" . $sm_ak . "&type=" . $type . "&tz=" . $tz;
        echo $this->curl_request($action, $args);
    }

    public function check_cat_inventory() {
        $entryId = urlencode($_GET['entryId']);
        $sm_ak = urlencode($_GET['sm_ak']);
        $access = urlencode($_GET['access']);
        $action = "ppv_orders/check_cat_inventory?";
        $args = 'entryId=' . $entryId . "&sm_ak=" . $sm_ak . "&access=" . $access;
        echo $this->curl_request($action, $args);
    }

    public function update_user_views() {
        $sm_ak = urlencode($_GET['sm_ak']);
        $entryId = urlencode($_GET['entryId']);
        $uid = urlencode($_GET['uid']);
        $action = "ppv_orders/update_views?";
        $args = 'entryId=' . $entryId . '&uid=' . $uid . "&sm_ak=" . $sm_ak;
        echo $this->curl_request($action, $args);
    }

    public function get_user_details() {
        $sm_ak = urlencode($_GET['sm_ak']);
        $uid = urlencode($_GET['uid']);
        $action = "ppv_user/get_user_details?";
        $args = 'uid=' . $uid . "&sm_ak=" . $sm_ak;
        echo $this->curl_request($action, $args);
    }

    public function update_fname() {
        $sm_ak = urlencode($_GET['sm_ak']);
        $auth_key = urlencode($_GET['auth_key']);
        $fname = urlencode($_GET['fname']);
        $action = "ppv_user/update_fname?";
        $args = "sm_ak=" . $sm_ak . "&auth_key=" . $auth_key . "&fname=" . $fname;
        echo $this->curl_request($action, $args);
    }

    public function update_lname() {
        $sm_ak = urlencode($_GET['sm_ak']);
        $auth_key = urlencode($_GET['auth_key']);
        $lname = urlencode($_GET['lname']);
        $action = "ppv_user/update_lname?";
        $args = "sm_ak=" . $sm_ak . "&auth_key=" . $auth_key . "&lname=" . $lname;
        echo $this->curl_request($action, $args);
    }

    public function w_cancel_sub() {
        $sm_ak = urlencode($_GET['sm_ak']);
        $auth_key = urlencode($_GET['auth_key']);
        $sub_id = urlencode($_GET['sub_id']);
        $action = "ppv_orders/w_cancel_sub?";
        $args = "sm_ak=" . $sm_ak . "&auth_key=" . $auth_key . "&sub_id=" . $sub_id;
        echo $this->curl_request($action, $args);
    }

    public function get_user_name() {
        $sm_ak = urlencode($_GET['sm_ak']);
        $uid = urlencode($_GET['uid']);
        $action = "ppv_user/get_user_name?";
        $args = 'uid=' . $uid . "&sm_ak=" . $sm_ak;
        echo $this->curl_request($action, $args);
    }

    public function get_confirm() {
        $sm_ak = urlencode($_GET['sm_ak']);
        $entryId = urlencode($_GET['entryId']);
        $ticket_id = urlencode($_GET['ticket_id']);
        $type = urlencode($_GET['type']);
        $protocol = urlencode($_GET['protocol']);
        $has_start = urlencode($_GET['has_start']);
        $action = "ppv_orders/get_confirm?";
        $args = 'entryId=' . $entryId . '&ticket_id=' . $ticket_id . "&sm_ak=" . $sm_ak . '&type=' . $type . '&protocol=' . $protocol . '&has_start=' . $has_start;
        echo $this->curl_request($action, $args);
    }

    public function get_checkout_details() {
        $sm_ak = urlencode($_GET['sm_ak']);
        $entryId = urlencode($_GET['entryId']);
        $kentry = urlencode($_GET['kentry']);
        $ticket_id = urlencode($_GET['ticket_id']);
        $type = urlencode($_GET['type']);
        $uid = urlencode($_GET['uid']);
        $action = "ppv_orders/get_checkout_details?";
        $args = 'entryId=' . $entryId . '&kentry=' . $kentry . '&ticket_id=' . $ticket_id . "&sm_ak=" . $sm_ak . '&type=' . $type . "&uid=" . $uid;
        echo $this->curl_request($action, $args);
    }

    public function add_order() {
        $sm_ak = urlencode($_GET['sm_ak']);
        $entry_id = urlencode($_GET['entry_id']);
        $user_id = urlencode($_GET['user_id']);
        $ticket_id = urlencode($_GET['ticket_id']);
        $tz = urlencode($_GET['tz']);
        $gw_type = urlencode($_GET['gw_type']);
        $action = "ppv_orders/add_order?";
        $args = 'entry_id=' . $entry_id . '&user_id=' . $user_id . '&ticket_id=' . $ticket_id . '&tz=' . $tz . "&sm_ak=" . $sm_ak . "&gw_type=" . $gw_type;
        echo $this->curl_request($action, $args);
    }

    public function create_auth_key() {
        $sm_ak = urlencode($_GET['sm_ak']);
        $un = urlencode($_GET['un']);
        $user_id = urlencode($_GET['user_id']);
        $action = "ppv_user/create_auth_key?";
        $args = 'user_id=' . $user_id . '&un=' . $un . "&sm_ak=" . $sm_ak;
        echo $this->curl_request($action, $args);
    }

    public function update_order_status() {
        $entry_id = urlencode($_GET['entry_id']);
        $user_id = urlencode($_GET['user_id']);
        $ticket_id = urlencode($_GET['ticket_id']);
        $status = urlencode($_GET['status']);
        $tz = urlencode($_GET['tz']);
        $action = "ppv_orders/update_order_payment_status?";
        $args = 'entry_id=' . $entry_id . '&user_id=' . $user_id . '&ticket_id=' . $ticket_id . '&tz=' . $tz . '&status=' . $status;
        echo $this->curl_request($action, $args);
    }

    public function complete_order() {
        $sm_ak = urlencode($_GET['sm_ak']);
        $entry_id = urlencode($_GET['entry_id']);
        $user_id = urlencode($_GET['user_id']);
        $ticket_id = urlencode($_GET['ticket_id']);
        $ticket_type = urlencode($_GET['ticket_type']);
        $status = urlencode($_GET['status']);
        $order_id = urlencode($_GET['order_id']);
        $payment_status = urlencode($_GET['payment_status']);
        $tz = urlencode($_GET['tz']);
        $type = urlencode($_GET['type']);
        $media_type = urlencode($_GET['media_type']);
        $smh_aff = urlencode($_GET['smh_aff']);
        $action = "ppv_orders/complete_order?";
        $args = 'entry_id=' . $entry_id .
                '&user_id=' . $user_id .
                '&ticket_id=' . $ticket_id .
                '&ticket_type=' . $ticket_type .
                '&tz=' . $tz .
                '&status=' . $status .
                '&order_id=' . $order_id .
                '&sm_ak=' . $sm_ak .
                '&payment_status=' . $payment_status .
                '&type=' . $type .
                '&media_type=' . $media_type .
                '&smh_aff=' . $smh_aff;
        echo $this->curl_request($action, $args);
    }

    public function finish_order() {
        $sm_ak = urlencode($_GET['sm_ak']);
        $entry_id = urlencode($_GET['entry_id']);
        $user_id = urlencode($_GET['user_id']);
        $ticket_id = urlencode($_GET['ticket_id']);
        $ticket_type = urlencode($_GET['ticket_type']);
        $status = urlencode($_GET['status']);
        $order_id = urlencode($_GET['order_id']);
        $payment_status = urlencode($_GET['payment_status']);
        $tz = urlencode($_GET['tz']);
        $type = urlencode($_GET['type']);
        $media_type = urlencode($_GET['media_type']);
        $action = "ppv_orders/finish_order?";
        $args = 'entry_id=' . $entry_id .
                '&user_id=' . $user_id .
                '&ticket_id=' . $ticket_id .
                '&ticket_type=' . $ticket_type .
                '&tz=' . $tz .
                '&status=' . $status .
                '&order_id=' . $order_id .
                '&sm_ak=' . $sm_ak .
                '&payment_status=' . $payment_status .
                '&type=' . $type .
                '&media_type=' . $media_type;
        echo $this->curl_request($action, $args);
    }

    public function setup_player() {
        $sm_ak = urlencode($_GET['sm_ak']);
        $entry_id = urlencode($_GET['entry_id']);
        $action = "ppv_config/setup_player?";
        $args = 'entry_id=' . $entry_id . '&sm_ak=' . $sm_ak;
        echo $this->curl_request($action, $args);
    }

    public function w_get_thumb() {
        $sm_ak = urlencode($_GET['sm_ak']);
        $entry_id = urlencode($_GET['entry_id']);
        $action = "ppv_config/w_get_thumb?";
        $args = 'entry_id=' . $entry_id . '&sm_ak=' . $sm_ak;
        echo $this->curl_request($action, $args);
    }

    public function get_thumb() {
        $ks = urlencode($_GET['ks']);
        $entry_id = urlencode($_GET['entry_id']);
        $action = "ppv_config/get_thumb?";
        $args = 'entry_id=' . $entry_id . '&ks=' . $ks;
        echo $this->curl_request($action, $args);
    }

    public function w_get_cat_thumb() {
        $sm_ak = urlencode($_GET['sm_ak']);
        $cat_id = urlencode($_GET['cat_id']);
        $action = "ppv_config/w_get_cat_thumb?";
        $args = 'cat_id=' . $cat_id . '&sm_ak=' . $sm_ak;
        echo $this->curl_request($action, $args);
    }

    public function get_cat_thumb() {
        $ks = urlencode($_GET['ks']);
        $cat_id = urlencode($_GET['cat_id']);
        $action = "ppv_config/get_cat_thumb?";
        $args = 'cat_id=' . $cat_id . '&ks=' . $ks;
        echo $this->curl_request($action, $args);
    }

    public function update_playlist_entry() {
        $ks = urlencode($_GET['ks']);
        $playlist_id = urlencode($_GET['playlist_id']);
        $ac_id = urlencode($_GET['ac_id']);
        $action = "ppv_entry/update_playlist_entry?";
        $args = 'playlist_id=' . $playlist_id . '&ks=' . $ks . '&ac_id=' . $ac_id;
        echo $this->curl_request($action, $args);
    }

    public function check_update_ac() {
        $ks = urlencode($_GET['ks']);
        $playlist_id = urlencode($_GET['playlist_id']);
        $playlist = urlencode($_GET['playlist']);
        $action = "ppv_entry/check_update_ac?";
        $args = 'playlist_id=' . $playlist_id . '&ks=' . $ks . '&playlist=' . $playlist;
        echo $this->curl_request($action, $args);
    }

    public function delete_playlist_entry() {
        $ks = urlencode($_GET['ks']);
        $playlist_id = urlencode($_GET['playlist_id']);
        $action = "ppv_entry/delete_playlist_entry?";
        $args = 'playlist_id=' . $playlist_id . '&ks=' . $ks;
        echo $this->curl_request($action, $args);
    }

    public function get_cat_entries() {
        $sm_ak = urlencode($_GET['sm_ak']);
        $cat_id = urlencode($_GET['cat_id']);
        $action = "ppv_entry/get_cat_entries?";
        $args = 'cat_id=' . $cat_id . '&sm_ak=' . $sm_ak;
        echo $this->curl_request($action, $args);
    }

    public function update_platform_cat() {
        $ks = urlencode($_GET['ks']);
        $cat = urlencode($_GET['cat']);
        $entry_id = urlencode($_GET['entry_id']);
        $action = "ppv_entry/update_platform_cat?";
        $args = 'cat=' . $cat . '&ks=' . $ks . '&entry_id=' . $entry_id;
        echo $this->curl_request($action, $args);
    }

    public function update_drag_cat() {
        $ks = urlencode($_GET['ks']);
        $cat_id = urlencode($_GET['cat_id']);
        $entry_id = urlencode($_GET['entry_id']);
        $action = "ppv_entry/update_drag_cat?";
        $args = 'cat_id=' . $cat_id . '&ks=' . $ks . '&entry_id=' . $entry_id;
        echo $this->curl_request($action, $args);
    }

    public function delete_platform_cat() {
        $ks = urlencode($_GET['ks']);
        $cat_id = urlencode($_GET['cat_id']);
        $action = "ppv_entry/delete_platform_cat?";
        $args = 'cat_id=' . $cat_id . '&ks=' . $ks;
        echo $this->curl_request($action, $args);
    }

    public function reset_psswd_request() {
        $sm_ak = urlencode($_GET['sm_ak']);
        $email = urlencode($_GET['email']);
        $url = urlencode($_GET['url']);
        $action = "ppv_user/reset_psswd_request?";
        $args = 'email=' . $email . '&sm_ak=' . $sm_ak . '&url=' . $url;
        echo $this->curl_request($action, $args);
    }

    public function reset_email_request() {
        $sm_ak = urlencode($_GET['sm_ak']);
        $email = urlencode($_GET['email']);
        $url = urlencode($_GET['url']);
        $action = "ppv_user/reset_email_request?";
        $args = 'email=' . $email . '&sm_ak=' . $sm_ak . '&url=' . $url;
        echo $this->curl_request($action, $args);
    }

    public function reset_pass() {
        $sm_ak = urlencode($_GET['sm_ak']);
        $email = urlencode($_GET['email']);
        $reset_token = urlencode($_GET['reset_token']);
        $pass = urlencode($_GET['pass']);
        $action = "ppv_user/reset_pass?";
        $args = 'email=' . $email . '&sm_ak=' . $sm_ak . '&reset_token=' . $reset_token . '&pass=' . $pass;
        echo $this->curl_request($action, $args);
    }

    public function reset_email() {
        $sm_ak = urlencode($_GET['sm_ak']);
        $email = urlencode($_GET['email']);
        $new_email = urlencode($_GET['new_email']);
        $reset_token = urlencode($_GET['reset_token']);
        $pass = urlencode($_GET['pass']);
        $action = "ppv_user/reset_email?";
        $args = 'email=' . $email . '&sm_ak=' . $sm_ak . '&reset_token=' . $reset_token . '&pass=' . $pass . '&new_email=' . $new_email;
        echo $this->curl_request($action, $args);
    }

    public function list_affiliates() {
        $ks = urlencode($_GET['ks']);
        $start = $_GET['start'];
        $length = $_GET['length'];
        $search = $_GET['search'];
        $draw = $_GET['draw'];
        $action = "ppv_affiliate/list_affiliate?";
        $args = "start=" . $start . "&length=" . $length . "&search=" . $search['value'] . "&draw=" . $draw . "&ks=" . $ks;
        echo $this->curl_request($action, $args);
    }

    public function list_campaigns() {
        $ks = urlencode($_GET['ks']);
        $start = $_GET['start'];
        $length = $_GET['length'];
        $search = $_GET['search'];
        $draw = $_GET['draw'];
        $currency = $_GET['currency'];
        $action = "ppv_affiliate/list_campaign?";
        $args = "start=" . $start . "&length=" . $length . "&search=" . $search['value'] . "&draw=" . $draw . "&ks=" . $ks . "&currency=" . $currency;
        echo $this->curl_request($action, $args);
    }

    public function list_marketing() {
        $ks = urlencode($_GET['ks']);
        $start = $_GET['start'];
        $length = $_GET['length'];
        $search = $_GET['search'];
        $draw = $_GET['draw'];
        $action = "ppv_affiliate/list_marketing?";
        $args = "start=" . $start . "&length=" . $length . "&search=" . $search['value'] . "&draw=" . $draw . "&ks=" . $ks;
        echo $this->curl_request($action, $args);
    }

    public function list_commissions() {
        $ks = urlencode($_GET['ks']);
        $start = $_GET['start'];
        $length = $_GET['length'];
        $search = $_GET['search'];
        $draw = $_GET['draw'];
        $action = "ppv_affiliate/list_commissions?";
        $args = "start=" . $start . "&length=" . $length . "&search=" . $search['value'] . "&draw=" . $draw . "&ks=" . $ks;
        echo $this->curl_request($action, $args);
    }

    public function get_marketing_data() {
        $ks = urlencode($_GET['ks']);
        $action = "ppv_affiliate/get_marketing_data?";
        $args = '&ks=' . $ks;
        echo $this->curl_request($action, $args);
    }

    public function create_link() {
        $ks = urlencode($_GET['ks']);
        $name = urlencode($_GET['name']);
        $desc = urlencode($_GET['desc']);
        $url = urlencode($_GET['url']);
        $aid = urlencode($_GET['aid']);
        $a_name = urlencode($_GET['a_name']);
        $cid = urlencode($_GET['cid']);
        $c_name = urlencode($_GET['c_name']);
        $status = urlencode($_GET['status']);
        $tz = urlencode($_GET['tz']);
        $action = "ppv_affiliate/add_link?";
        $args = "tz=" . $tz . "&name=" . $name . "&desc=" . $desc . "&url=" . $url . "&aid=" . $aid . "&cid=" . $cid . "&status=" . $status . "&ks=" . $ks . "&a_name=" . $a_name . "&c_name=" . $c_name;
        echo $this->curl_request($action, $args);
    }

    public function update_link() {
        $ks = urlencode($_GET['ks']);
        $mid = urlencode($_GET['mid']);
        $name = urlencode($_GET['name']);
        $desc = urlencode($_GET['desc']);
        $url = urlencode($_GET['url']);
        $aid = urlencode($_GET['aid']);
        $a_name = urlencode($_GET['a_name']);
        $cid = urlencode($_GET['cid']);
        $c_name = urlencode($_GET['c_name']);
        $tz = urlencode($_GET['tz']);
        $action = "ppv_affiliate/update_link?";
        $args = "tz=" . $tz . "&name=" . $name . "&desc=" . $desc . "&url=" . $url . "&aid=" . $aid . "&cid=" . $cid . "&mid=" . $mid . "&ks=" . $ks . "&a_name=" . $a_name . "&c_name=" . $c_name;
        echo $this->curl_request($action, $args);
    }

    public function update_link_status() {
        $tz = $_GET['tz'];
        $ks = urlencode($_GET['ks']);
        $mid = $_GET['mid'];
        $status = $_GET['status'];
        $action = "ppv_affiliate/update_link_status?";
        $args = "tz=" . $tz . "&status=" . $status . "&ks=" . $ks . "&mid=" . $mid;
        echo $this->curl_request($action, $args);
    }

    public function delete_link() {
        $ks = urlencode($_GET['ks']);
        $mid = $_GET['mid'];
        $tz = $_GET['tz'];
        $action = "ppv_affiliate/delete_link?";
        $args = "tz=" . $tz . "&mid=" . $mid . "&ks=" . $ks;
        echo $this->curl_request($action, $args);
    }

    public function get_user_comms() {
        $ks = urlencode($_GET['ks']);
        $start = $_GET['start'];
        $length = $_GET['length'];
        $search = urlencode($_GET['search']);
        $draw = $_GET['draw'];
        $aid = $_GET['aid'];
        $tz = $_GET['tz'];
        $action = "ppv_affiliate/get_user_comms?";
        $args = "tz=" . $tz . "&aid=" . $aid . "&start=" . $start . "&length=" . $length . "&search=" . $search['value'] . "&draw=" . $draw . "&ks=" . $ks;
        echo $this->curl_request($action, $args);
    }

    public function update_user_comms_status() {
        $ks = urlencode($_GET['ks']);
        $sale_id = $_GET['sale_id'];
        $status = $_GET['status'];
        $tz = $_GET['tz'];
        $action = "ppv_affiliate/update_user_comms_status?";
        $args = "tz=" . $tz . "&sale_id=" . $sale_id . "&ks=" . $ks . "&status=" . $status;
        echo $this->curl_request($action, $args);
    }

    public function delete_commission() {
        $ks = urlencode($_GET['ks']);
        $sale_id = $_GET['sid'];
        $action = "ppv_affiliate/delete_commission?";
        $args = "sale_id=" . $sale_id . "&ks=" . $ks;
        echo $this->curl_request($action, $args);
    }

    public function list_subs() {
        $ks = urlencode($_GET['ks']);
        $start = $_GET['start'];
        $length = $_GET['length'];
        $search = $_GET['search'];
        $draw = $_GET['draw'];
        $tz = $_GET['tz'];
        $action = "ppv_orders/list_subs?";
        $args = "tz=" . $tz . "&start=" . $start . "&length=" . $length . "&search=" . $search['value'] . "&draw=" . $draw . "&ks=" . $ks;
        echo $this->curl_request($action, $args);
    }

    public function cancel_sub() {
        $ks = urlencode($_GET['ks']);
        $sub_id = $_GET['sub_id'];
        $action = "ppv_orders/cancel_sub?";
        $args = "sub_id=" . $sub_id . "&ks=" . $ks;
        echo $this->curl_request($action, $args);
    }

    public function delete_sub() {
        $ks = urlencode($_GET['ks']);
        $sub_id = $_GET['sub_id'];
        $action = "ppv_orders/delete_sub?";
        $args = "sub_id=" . $sub_id . "&ks=" . $ks;
        echo $this->curl_request($action, $args);
    }

    public function w_delete_sub() {
        $sm_ak = urlencode($_GET['sm_ak']);
        $sub_id = $_GET['sub_id'];
        $action = "ppv_orders/w_delete_sub?";
        $args = "sub_id=" . $sub_id . "&sm_ak=" . $sm_ak;
        echo $this->curl_request($action, $args);
    }

    public function update_sub_status() {
        $ks = urlencode($_GET['ks']);
        $sub_id = $_GET['sub_id'];
        $status = $_GET['status'];
        $action = "ppv_orders/update_sub_status?";
        $args = "sub_id=" . $sub_id . "&ks=" . $ks . "&status=" . $status;
        echo $this->curl_request($action, $args);
    }

    public function is_active() {
        $sm_ak = urlencode($_GET['sm_ak']);
        $uid = urlencode($_GET['uid']);
        $action = "ppv_user/is_active?";
        $args = "sm_ak=" . $sm_ak . "&uid=" . $uid;
        echo $this->curl_request($action, $args);
    }

    public function is_not_active() {
        $sm_ak = urlencode($_GET['sm_ak']);
        $uid = urlencode($_GET['uid']);
        $action = "ppv_user/is_not_active?";
        $args = "sm_ak=" . $sm_ak . "&uid=" . $uid;
        echo $this->curl_request($action, $args);
    }

    public function user_concurrent_status() {
        $ks = urlencode($_GET['ks']);
        $concurrent = $_GET['concurrent'];
        $action = "ppv_user/user_concurrent_status?";
        $args = "concurrent=" . $concurrent . "&ks=" . $ks;
        echo $this->curl_request($action, $args);
    }

    public function get_concurrent_status() {
        $ks = urlencode($_GET['ks']);
        $action = "ppv_user/get_concurrent_status?";
        $args = "";
        echo $this->curl_request($action, $args);
    }

    public function user_activation_skip_status() {
        $ks = urlencode($_GET['ks']);
        $skip = $_GET['skip'];
        $action = "ppv_user/user_activation_skip_status?";
        $args = "skip=" . $skip . "&ks=" . $ks;
        echo $this->curl_request($action, $args);
    }

    public function get_activation_skip_status() {
        $ks = urlencode($_GET['ks']);
        $action = "ppv_user/get_activation_skip_status?";
        $args = "";
        echo $this->curl_request($action, $args);
    }

    public function get_owner_attrs() {
        $ks = urlencode($_GET['ks']);
        $action = "ppv_user/get_owner_attrs?";
        $args = "ks=" . $ks;
        echo $this->curl_request($action, $args);
    }

    public function update_reg_fields() {
        $ks = urlencode($_GET['ks']);
        $newFields = urlencode($_GET['newFields']);
        $updateFields = urlencode($_GET['updateFields']);
        $removeFields = urlencode($_GET['removeFields']);
        $action = "ppv_user/update_reg_fields?";
        $args = "ks=" . $ks . "&new_fields=" . $newFields . "&update_fields=" . $updateFields . "&remove_fields=" . $removeFields;
        echo $this->curl_request($action, $args);
    }

    public function update_user_details() {
        $ks = urlencode($_GET['ks']);
        $uid = urlencode($_GET['uid']);
        $updateFields = urlencode($_GET['updateFields']);
        $action = "ppv_user/update_user_details?";
        $args = "ks=" . $ks . "&uid=" . $uid . "&update_fields=" . $updateFields;
        echo $this->curl_request($action, $args);
    }

    public function cancel_order() {
        $sm_ak = urlencode($_GET['sm_ak']);
        $uid = urlencode($_GET['uid']);
        $order_id = $_GET['order_id'];
        $sub_id = $_GET['sub_id'];
        $action = "ppv_orders/cancel_order?";
        $args = "uid=" . $uid . "&order_id=" . $order_id . "&sub_id=" . $sub_id . "&sm_ak=" . $sm_ak;
        echo $this->curl_request($action, $args);
    }

}

$ppv = new ppv();
$ppv->run();
?>
