<?php

ini_set('log_errors', true);
ini_set('error_log', dirname(__FILE__) . '/authnet_errors.log');

class authnet {

    private $post_data = array();

    public function __construct() {
        if (!empty($_POST)) {
            $this->post_data = $_POST;
        }
    }

    //run ppv api
    public function run() {
        $this->updateDb();
    }

    public function updateDb() {
        $response = $this->post_data;

        $log = json_encode($this->post_data);
        $phpStringArray = str_replace(array("{", "}", ":"), array("array(", ")", "=>"), $log);
        error_log($phpStringArray);

        if (isset($response['x_subscription_id']) && $response['x_subscription_id'] != '') {
            $subscription_id = $response['x_subscription_id'];
            $custom = explode("X", $response["x_cust_id"]);
            $pid = urlencode($custom[0]);
            $uid = urlencode($custom[1]);
            $firstName = urlencode($response['x_first_name']);
            $lastName = urlencode($response['x_last_name']);
            $payerEmail = urlencode($response['x_email']);
            $city = urlencode($response['x_city']);
            $paymentStatus = urlencode($response['x_response_code']);
            $avsCode = urlencode($response['x_avs_code']);
            $authCode = urlencode($response['x_auth_code']);
            $transactionId = urlencode($response['x_trans_id']);
            $response_text = urlencode($response['x_response_reason_text']);
            $result = $this->recordRecurrOrder($pid, $uid, $subscription_id, $firstName, $lastName, $payerEmail, $city, $paymentStatus, $avsCode, $authCode, $transactionId);
        } else {
            $type = urlencode($response['x_type']);
            if ($type == 'auth_capture') {
                $custom = explode(",", $response["custom"]);
                $order_id = urlencode($custom[0]);
                $sm_ak = urlencode($custom[1]);
                $ticket_type = urlencode($custom[2]);
                $firstName = urlencode($response['x_first_name']);
                $lastName = urlencode($response['x_last_name']);
                $payerEmail = urlencode($response['x_email']);
                $city = urlencode($response['x_city']);
                $paymentStatus = urlencode($response['x_response_code']);
                $avsCode = urlencode($response['x_avs_code']);
                $authCode = urlencode($response['x_auth_code']);
                $transactionId = urlencode($response['x_trans_id']);
                $itemName = urlencode($response['x_description']);
                sleep(5);
                $this->recordOrder($sm_ak, $order_id, $firstName, $lastName, $payerEmail, $city, $paymentStatus, $avsCode, $authCode, $transactionId, $itemName, $ticket_type);
            } else if ($type == 'void' || $type == 'credit' || $type == 'refundPendingSettlement' || $type == 'refundSettledSuccessfully') {
                $invoice_num = urlencode($response['x_invoice_num']);
                sleep(5);
                $this->refundOrder($invoice_num);
            }
        }
    }

    public function recordOrder($sm_ak, $order_id, $firstName, $lastName, $payerEmail, $city, $paymentStatus, $avsCode, $authCode, $transactionId, $itemName, $ticket_type) {
        $url = "http://api.streamingmediahosting.com/index.php/api/ppv_orders/insert_authnet_details?sm_ak=" . $sm_ak . "&order_id=" . $order_id . "&firstName=" . $firstName . "&lastName=" . $lastName . "&payerEmail=" . $payerEmail . "&city=" . $city . "&paymentStatus=" . $paymentStatus . "&avsCode=" . $avsCode . "&authCode=" . $authCode . "&transactionId=" . $transactionId . "&itemName=" . $itemName . "&ticket_type=" . $ticket_type . "&format=json";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    public function refundOrder($invoice_num) {
        $url = "http://api.streamingmediahosting.com/index.php/api/ppv_orders/refund_authnet_order?invoice_num=" . $invoice_num;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    public function recordRecurrOrder($pid, $uid, $subscription_id, $firstName, $lastName, $payerEmail, $city, $paymentStatus, $avsCode, $authCode, $transactionId) {
        $url = "http://api.streamingmediahosting.com/index.php/api/ppv_orders/insert_authnet_recurr_order?pid=" . $pid . "&uid=" . $uid . "&sub_id=" . $subscription_id . "&firstName=" . $firstName . "&lastName=" . $lastName . "&payerEmail=" . $payerEmail . "&city=" . $city . "&paymentStatus=" . $paymentStatus . "&avsCode=" . $avsCode . "&authCode=" . $authCode . "&transactionId=" . $transactionId . "&format=json";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        return json_decode($output, TRUE);
    }

}

$authnet = new authnet();
$authnet->run();
?>
