<?php

function activate_account($pid, $sm_ak, $akey, $email) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://mediaplatform.streamingmediahosting.com/apps/ppv/v1.0/index.php?action=activate_account&pid=" . $pid . "&sm_ak=" . $sm_ak . "&akey=" . $akey . "&tz=America/Los_Angeles&email=" . $email);
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
            function Redirect (url) {
                var ua = navigator.userAgent.toLowerCase(),
                isIE = ua.indexOf('msie') !== -1,
                version = parseInt(ua.substr(4, 2), 10);

                // Internet Explorer 8 and lower
                if (isIE && version < 9) {
                    var link = document.createElement('a');
                    link.href = url;
                    document.body.appendChild(link);
                    link.click();
                }

                // All other browsers
                else { window.location.href = url; }
            }
        </script>
    </head>
    <body>
        <?php
        if ((isset($_GET['pid']) && !empty($_GET['pid'])) && (isset($_GET['sm_ak']) && !empty($_GET['sm_ak'])) && (isset($_GET['akey']) && !empty($_GET['akey'])) && (isset($_GET['email']) && !empty($_GET['email'])) && (isset($_GET['url']) && !empty($_GET['url']))) {
            $activate = activate_account($_GET['pid'], $_GET['sm_ak'], $_GET['akey'], $_GET['email']);
            $response = json_decode($activate, true);
            if ($response['success']) {
                echo '<div style="width: 500px; margin-left: auto; margin-right: auto; margin-top: 240px; font-size: 17px;" id="loading">
                        <div style="margin-top: 30px; width: 388px; margin-left: auto; margin-right: auto; text-align: center; height: 125px;">
                            <h3>Your account has been activated!</h3>
                            You can now login through the player. 
                        </div>
                        <div class="modal-footer">
                            <button style="margin-left: auto; margin-right: auto; width: 112px; display: block; font-size: 15px;" type="button" class="btn btn-primary" data-dismiss="modal" onclick="Redirect(\'' . base64_decode($_GET['url']) . '\');">Return to site</button>
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
                            Activation key not valid. 
                        </div>          
                    </div>';
                }
            }
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