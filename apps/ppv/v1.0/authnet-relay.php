<?php

ini_set('log_errors', true);
ini_set('error_log', dirname(__FILE__) . '/authnet_relay_errors.log');

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

        $custom = explode(",", $response["custom"]);
        $order_id = urlencode($custom[0]);
        $sm_ak = urlencode($custom[1]);
        $ticket_type = urlencode($custom[2]);
        $sub_id = urlencode($custom[3]);
        $smh_aff = urlencode($custom[4]);
        $isMobile = urlencode($custom[5]);
        $referrer_url = urlencode($response['url']);
        $firstName = urlencode($response['x_first_name']);
        $lastName = urlencode($response['x_last_name']);
        $payerEmail = urlencode($response['x_email']);
        $city = urlencode($response['x_city']);
        $paymentStatus = urlencode($response['x_response_code']);
        $avsCode = urlencode($response['x_avs_code']);
        $authCode = urlencode($response['x_auth_code']);
        $transactionId = urlencode($response['x_trans_id']);
        $itemName = urlencode($response['x_description']);
        $response_text = urlencode($response['x_response_reason_text']);
        $result = $this->recordOrder($sm_ak, $order_id, $firstName, $lastName, $payerEmail, $city, $paymentStatus, $avsCode, $authCode, $transactionId, $itemName, $ticket_type, $smh_aff);

        $url = 'https://mediaplatform.streamingmediahosting.com/apps/ppv/v1.0/checkout/receipt_page.php';
        if ($result['success']) {
            if ($paymentStatus == 1) {
                if ($ticket_type == 'sub') {
                    $sub_resp = $this->createRecurringProfile($sm_ak, $order_id, $sub_id, $payerEmail, $firstName, $lastName, $city);
                    if ($sub_resp['success']) {
                        $redirect_url = $url . '?title=' . $itemName . '&order_id=' . $order_id . '&isMobile=' . $isMobile . '&url=' . base64_encode($referrer_url) . '&response_code=1';
                    }
                } else {
                    $redirect_url = $url . '?title=' . $itemName . '&order_id=' . $order_id . '&isMobile=' . $isMobile . '&url=' . base64_encode($referrer_url) . '&response_code=1';
                }
            } else {
                $redirect_url = $url . '?title=' . $itemName . '&order_id=' . $order_id . '&isMobile=' . $isMobile . '&url=' . base64_encode($referrer_url) . '&response_code=' . $paymentStatus . '&response_reason_text=' . $response_text;
            }
        }

        echo $this->getRelayResponseSnippet($redirect_url);
    }

    public function recordOrder($sm_ak, $order_id, $firstName, $lastName, $payerEmail, $city, $paymentStatus, $avsCode, $authCode, $transactionId, $itemName, $ticket_type, $smh_aff) {
        $url = "http://api.streamingmediahosting.com/index.php/api_dev/ppv_orders/insert_authnet_details?sm_ak=" . $sm_ak . "&order_id=" . $order_id . "&firstName=" . $firstName . "&lastName=" . $lastName . "&payerEmail=" . $payerEmail . "&city=" . $city . "&paymentStatus=" . $paymentStatus . "&avsCode=" . $avsCode . "&authCode=" . $authCode . "&transactionId=" . $transactionId . "&itemName=" . $itemName . "&ticket_type=" . $ticket_type . "&smh_aff=" . $smh_aff . "&format=json";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        return json_decode($output, TRUE);
    }

    public function createRecurringProfile($sm_ak, $order_id, $sub_id, $payerEmail, $firstName, $lastName, $city) {
        $url = "http://api.streamingmediahosting.com/index.php/api_dev/ppv_orders/createAuthnetSub?sm_ak=" . $sm_ak . "&order_id=" . $order_id . "&sub_id=" . $sub_id . "&firstName=" . $firstName . "&lastName=" . $lastName . "&payerEmail=" . $payerEmail . "&city=" . $city . "&format=json";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        return json_decode($output, TRUE);
    }

    public function getRelayResponseSnippet($redirect_url) {
        return "<html><head><script language=\"javascript\">
                <!--
                window.location=\"{$redirect_url}\";
                //-->
                </script>
                </head><body><noscript><meta http-equiv=\"refresh\" content=\"1;url={$redirect_url}\"></noscript></body></html>";
    }

}

$authnet = new authnet();
$authnet->run();
?>
