<?php

ini_set('log_errors', true);
ini_set('error_log', dirname(__FILE__) . '/authnet_relay_errors.log');

//// Get the subscription ID if it is available. 
//// Otherwise $subscription_id will be set to zero.
//$subscription_id = (int) $_POST['x_subscription_id'];
//
//// Check to see if we got a valid subscription ID.
//// If so, do something with it.
//if ($subscription_id) {
//    // Get the response code. 1 is success, 2 is decline, 3 is error
//    $response_code = (int) $_POST['x_response_code'];
//
//    // Get the reason code. 8 is expired card.
//    $reason_code = (int) $_POST['x_response_reason_code'];
//
//    if ($response_code == 1) {
//        // Approved!
//        // Some useful fields might include:
//        // $authorization_code = $_POST['x_auth_code'];
//        // $avs_verify_result  = $_POST['x_avs_code'];
//        // $transaction_id     = $_POST['x_trans_id'];
//        // $customer_id        = $_POST['x_cust_id'];
//    } else if ($response_code == 2) {
//        // Declined
//    } else if ($response_code == 3 && $reason_code == 8) {
//        // An expired card
//    } else {
//        // Other error
//    }
//}

class authnet {

    private $post_data = array();

    public function __construct() {
        if (!empty($_POST)) {
            $this->post_data = $_POST;
        }
    }

    //run ppv api
    public function run() {
//        $this->updateDb();

        $log = json_encode($this->post_data);
        $phpStringArray = str_replace(array("{", "}", ":"), array("array(", ")", "=>"), $log);
        error_log($phpStringArray);
    }

    public function updateDb() {
        $response = $this->post_data;

        $log = json_encode($this->post_data);
        $phpStringArray = str_replace(array("{", "}", ":"), array("array(", ")", "=>"), $log);
        error_log($phpStringArray);

        $custom = explode(",", $response["custom"]);
        $order_id = urlencode($custom[0]);
        $sm_ak = urlencode($custom[1]);
        $ticket_type = urlencode($custom[2]);
        $firstName = urlencode($response['x_first_name']);
        $lastName = urlencode($response['x_last_name']);
        $payerEmail = urlencode($response['x_email']);
        $city = urlencode($response['x_city']);
        $paymentStatus = urlencode($response['x_response_code']);
        $transactionId = urlencode($response['x_trans_id']);
        $itemName = urlencode($response['x_description']);

        $order = $this->recordOrder($sm_ak, $order_id, $firstName, $lastName, $payerEmail, $city, $paymentStatus, $transactionId, $itemName, $ticket_type);
    }

    public function recordOrder($sm_ak, $order_id, $firstName, $lastName, $payerEmail, $city, $paymentStatus, $transactionId, $itemName, $ticket_type) {
        $url = "http://api.streamingmediahosting.com/index.php/api_dev/ppv_orders/insert_authnet_details?sm_ak=" . $sm_ak . "&order_id=" . $order_id . "&firstName=" . $firstName . "&lastName=" . $lastName . "&payerEmail=" . $payerEmail . "&city=" . $city . "&paymentStatus=" . $paymentStatus . "&transactionId=" . $transactionId . "&itemName=" . $itemName . "&ticket_type=" . $ticket_type . "&format=json";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

}

$authnet = new authnet();
$authnet->run();
?>
