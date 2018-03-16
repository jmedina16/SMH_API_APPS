<?php

function store_weibo_authorization($pid, $ks, $projection, $code) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://devplatform.streamingmediahosting.com/apps/sn/v1.0/dev.php?action=store_weibo_authorization&pid=" . $pid . "&ks=" . $ks . "&projection=" . $projection . "&code=" . $code);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>Account Activation</title>
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css" />
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
        <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
        <script>
            function closeWindow() {
                window.close();
            }
        </script>
    </head>
    <body>
        <?php
        if ((isset($_GET['state']) && !empty($_GET['state'])) && (isset($_GET['code']) && !empty($_GET['code']))) {
            $state = explode("|", $_GET['state']);
            $pid = $state[0];
            $ks = $state[1];
            $projection = $state[2];
            $auth = store_weibo_authorization($pid, $ks, $projection, $_GET['code']);
            $response = json_decode($auth, true);
            if ($response['success']) {
                echo '<div style="width: 675px; margin-left: auto; margin-right: auto; margin-top: 150px; font-size: 17px;" id="loading">
                        <div style="margin: 30px auto 50px; width: 675px; margin-left: auto; margin-right: auto; text-align: center; height: 125px;">
                            <h3><img src="/img/weibo_logo.png" width="150px"></h3>
                            <h3>You have successfully connected your Weibo account!</h3>
                            You may now close this window. 
                        </div>
                        <div class="modal-footer">
                            <button style="margin-left: auto; margin-right: auto; width: 112px; display: block; font-size: 15px;" type="button" class="btn btn-primary" data-dismiss="modal" onclick="closeWindow();">close</button>
                        </div>            
                    </div>';
            } else {
                if ($response['message']) {
                    echo '<div style="width: 500px; margin-left: auto; margin-right: auto; margin-top: 240px; font-size: 17px;" id="loading">
                        <div style="margin-top: 30px; width: 388px; margin-left: auto; margin-right: auto; text-align: center; height: 125px;">
                            <h3>Error</h3>
                            ' . $response['message'] . ' 
                        </div>          
                    </div>';
                } else {
                    echo '<div style="width: 500px; margin-left: auto; margin-right: auto; margin-top: 240px; font-size: 17px;" id="loading">
                        <div style="margin-top: 30px; width: 388px; margin-left: auto; margin-right: auto; text-align: center; height: 125px;">
                            <h3>Error</h3>
                            Something went wrong. Please try again. 
                        </div>          
                    </div>';
                }
            }
        } else if ((isset($_GET['state']) && !empty($_GET['state'])) && (isset($_GET['error']) && !empty($_GET['error']))) {
            echo '<div style="width: 500px; margin-left: auto; margin-right: auto; margin-top: 200px; font-size: 17px;" id="loading">
            <div style="margin-top: 30px; width: 450px; margin-left: auto; margin-right: auto; text-align: center; height: 125px;">
                <h3><img src="/img/weibo_logo.png" width="150px"></h3>
                Access has been denied to your Weibo account. 
            </div>            
        </div>';
        } else {
            echo '<div style="width: 500px; margin-left: auto; margin-right: auto; margin-top: 240px; font-size: 17px;" id="loading">
            <div style="margin-top: 30px; width: 388px; margin-left: auto; margin-right: auto; text-align: center; height: 125px;">
                <h3>Invalid URL</h3>
                This link is invalid. Please try again. 
            </div>            
        </div>';
        }
        ?>
    </body>
</html>