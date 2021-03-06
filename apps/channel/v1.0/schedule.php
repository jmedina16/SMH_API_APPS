<?php
$pid = $_GET['pid'];
$playerId = $_GET['playerId'];
$ks = $_GET['ks'];
if (isset($pid)) {
    if (!isset($playerId)) {
        $playerId = 6717463;
    }

    function getCDN($pid) {
        $url = 'http://hwapps.streamingmediahosting.com/apps/scripts/getCDN.php?action=get_cdn&pid=' . $pid;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    $apps_url = '';

    $cdn = json_decode(getCDN($pid), true);
    if ($cdn[0]['edgecast']) {
        //$apps_url = 'https://ecapps.streamingmediahosting.com';
        $apps_url = 'http://devplatform.streamingmediahosting.com';
    } else if ($cdn[0]['highwinds'] || $cdn[0]['custom']) {
        //$apps_url = 'https://hwapps.streamingmediahosting.com';
        $apps_url = 'http://devplatform.streamingmediahosting.com';
    }
    ?>
    <!DOCTYPE html>
    <html lang="en">
        <head>
            <meta charset="utf-8">
            <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
            <title>TV Channel Guide</title>

            <!-- CSS -->
            <link href="//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css?v=1" rel="stylesheet">
            <link href="<?php echo $apps_url ?>/p/<?php echo $pid ?>/css/bootstrap.min.css?v=1" rel="stylesheet">
            <link href="<?php echo $apps_url ?>/p/<?php echo $pid ?>/css/font-awesome.min.css?v=1" rel="stylesheet">
            <link href="<?php echo $apps_url ?>/p/<?php echo $pid ?>/css/jquery.mCustomScrollbar.css?v=1" rel="stylesheet"> 
            <link href="<?php echo $apps_url ?>/p/<?php echo $pid ?>/css/schedule_public/dhtmlxscheduler_flat.css?v=1" rel="stylesheet">
        </head>
        <body>
            <script src="http://devplatform.streamingmediahosting.com/p/<?php echo $pid ?>/sp/<?php echo $pid ?>00/embedIframeJs/uiconf_id/<?php echo $playerId ?>/partner_id/<?php echo $pid ?>"></script>
            <script type="text/javascript">
                var sessInfo = {pid: '<?php echo $pid; ?>', playerId: '<?php echo $playerId ?>', ks: '<?php echo ($ks)? $ks : null; ?>'};
            </script>
            <div class="container-fluid" id="player-wrapper">
                <div class="row">
                    <!--                <div style="border: 2px solid #000;width: 800px;height: 450px;overflow: hidden;float: left;padding: 10px;">-->
                    <!--                    <div style="max-width: 800px;width: 100%;display: inline-block;position: relative;float: left;">-->
                    <div class="col-xs-12 col-sm-7 col-md-5" style="margin-top: 10px;">
                        <div style="margin-top: 56.25%;"></div>
                        <div id="smh_player" style="position:absolute;top:0;left:0;left: 0;right: 0;bottom:0;padding-left:5px;padding-right:5px;"></div>
                    </div>
                    <!--                </div>-->
                    <!--                    <div style="border: 2px solid #000;height: 450px;margin-left: 800px;">-->
                    <div class="col-xs-12 col-sm-5 col-md-7" id="entry-details-wrapper">
                        <div id="entry-details">
                            <div id="event-title"></div>   
                            <div id="channel-time"></div>
                            <div id="event-desc"></div>
                        </div>                        
                    </div>
                    <div class="clear"></div>                
                </div>
            </div>
            <div id="scheduler" class="dhx_cal_container" style='width:100%; height:100%;'>
                <div id="dhx_sticky_navline_wrapper">
                    <div class="dhx_cal_navline">
                        <div class="scheduler_zoom_out" onclick="smhS.zoomOut();"><i class="fa fa-search-minus" aria-hidden="true"></i></div>
                        <div class="scheduler_zoom_in" onclick="smhS.zoomIn();"><i class="fa fa-search-plus" aria-hidden="true"></i></div>
                        <div class="dhx_cal_prev_button">&nbsp;</div>
                        <div class="dhx_cal_next_button">&nbsp;</div>
                        <div class="dhx_cal_date"></div>
                        <div class="dhx_minical_icon" id="dhx_minical_icon" onclick="smhS.show_minical()" style="left: 190px;">&nbsp;</div>
                    </div>                 
                </div>
                <div id="dhx_sticky_header_wrapper">
                    <div class="dhx_cal_header"></div>   
                </div>                
                <div class="dhx_cal_data"></div>		
            </div>
            <div class="modal fade" id="smh-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog smh-dialog">
                    <div class="modal-content">
                        <div class="modal-body"></div>
                    </div>
                </div>
            </div>              

            <script src="<?php echo $apps_url ?>/p/<?php echo $pid ?>/js/jQuery-2.1.4.min.js?v=1" type="text/javascript"></script>
            <script src="//code.jquery.com/ui/1.10.3/jquery-ui.js?v=1"></script>
            <script src="<?php echo $apps_url ?>/p/<?php echo $pid ?>/js/bootstrap.min.js?v=1" type="text/javascript"></script>
            <script src="<?php echo $apps_url ?>/p/<?php echo $pid ?>/js/jstz.min.js?v=1" type="text/javascript"></script>
            <script src="<?php echo $apps_url ?>/p/<?php echo $pid ?>/js/jquery.mCustomScrollbar.min.js?v=1" type="text/javascript"></script>
            <script src="<?php echo $apps_url ?>/p/<?php echo $pid ?>/js/dhtmlxscheduler.js?v=1.5" type = "text/javascript"></script>
            <script src="<?php echo $apps_url ?>/p/<?php echo $pid ?>/js/schedule_public/dhtmlxscheduler_limit.js?v=1.5" type="text/javascript"></script>
            <script src="<?php echo $apps_url ?>/p/<?php echo $pid ?>/js/dhtmlxscheduler_timeline.js?v=1.5" type="text/javascript"></script>
            <script src="<?php echo $apps_url ?>/p/<?php echo $pid ?>/js/dhtmlxscheduler_minical.js?v=1.5" type="text/javascript"></script>
            <script src="<?php echo $apps_url ?>/p/<?php echo $pid ?>/js/dhtmlxscheduler_tooltip.js?v=1.5" type="text/javascript"></script>
            <script src="<?php echo $apps_url ?>/p/<?php echo $pid ?>/js/dhtmlxscheduler_recurring.js?v=1.5" type="text/javascript"></script>
            <script src="<?php echo $apps_url ?>/p/<?php echo $pid ?>/js/jquery.dotdotdot.min.js?v=1.5" type="text/javascript"></script>
            <script src="<?php echo $apps_url ?>/p/<?php echo $pid ?>/js/schedule.js?v=1.5" type="text/javascript"></script>
        </body>
    </html>
    <?php
} else {
    echo "Cannot find schedule";
}
?>   

