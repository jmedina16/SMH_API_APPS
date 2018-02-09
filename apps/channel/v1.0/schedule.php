<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <title>TV Channel Guide</title>

        <!-- CSS -->
        <link href="//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css?v=1" rel="stylesheet">
        <link href="/css/bootstrap.min.css?v=1" rel="stylesheet">
        <link href="/css/font-awesome.min.css?v=1" rel="stylesheet">
        <link href="/css/schedule_public/dhtmlxscheduler_flat.css?v=1" rel="stylesheet">
    </head>
    <body>
        <?php
        $pid = $_GET['pid'];
        $playerId = $_GET['playerId'];
        if (isset($pid)) {
            ?>
            <script src="https://mediaplatform.streamingmediahosting.com/p/<?php echo $pid ?>/sp/<?php echo $pid ?>00/embedIframeJs/uiconf_id/<?php echo $playerId ?>/partner_id/<?php echo $pid ?>"></script>
            <script type="text/javascript">
                var sessInfo = {pid: '<?php echo $pid; ?>', playerId: '<?php echo $playerId ?>'};
            </script>
            <div style="border: 2px solid #000; width: 100%; height: 431px;">
                <div style="border: 2px solid #000;width: 782px;height: 431px;overflow: hidden;float: left;">
                    <div id="smh_player" style="width: 782px; height: 431px;"></div>
                </div>
                <div style="border: 2px solid #000;height: 431px;margin-left: 782px;">
                    Entry Details
                </div>
                <div class="clear"></div>
            </div>
            <div id="scheduler" class="dhx_cal_container" style='width:100%; height:100%;'>
                <div class="dhx_cal_navline">
                    <div class="scheduler_zoom_out" onclick="smhS.zoomOut();"><i class="fa fa-search-minus" aria-hidden="true"></i></div>
                    <div class="scheduler_zoom_in" onclick="smhS.zoomIn();"><i class="fa fa-search-plus" aria-hidden="true"></i></div>
                    <div class="dhx_cal_prev_button">&nbsp;</div>
                    <div class="dhx_cal_next_button">&nbsp;</div>
                    <div class="dhx_cal_date"></div>
                    <div class="dhx_minical_icon" id="dhx_minical_icon" onclick="smhS.show_minical()" style="left: 190px;">&nbsp;</div>
                </div>
                <div class="dhx_cal_header">
                </div>
                <div class="dhx_cal_data">
                </div>		
            </div>
            <div class="modal fade" id="smh-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog smh-dialog">
                    <div class="modal-content">
                        <div class="modal-body"></div>
                    </div>
                </div>
            </div>
            <script src="/js/jQuery-2.1.4.min.js?v=1" type="text/javascript"></script>
            <script src="//code.jquery.com/ui/1.10.3/jquery-ui.js?v=1"></script>
            <script src="/js/bootstrap.min.js?v=1" type="text/javascript"></script>
            <script src="/js/dhtmlxscheduler.js?v=1.5" type = "text/javascript"></script>
            <script src="/js/dhtmlxscheduler_limit.js?v=1.5" type="text/javascript"></script>
            <script src="/js/dhtmlxscheduler_timeline.js?v=1.5" type="text/javascript"></script>
            <script src="/js/dhtmlxscheduler_minical.js?v=1.5" type="text/javascript"></script>
            <script src="/js/dhtmlxscheduler_tooltip.js?v=1.5" type="text/javascript"></script>
            <script src="/js/dhtmlxscheduler_container_autoresize.js?v=1.5" type="text/javascript"></script>
            <script src="/js/dhtmlxscheduler_recurring.js?v=1.5" type="text/javascript"></script>
            <script src="/js/jquery.dotdotdot.js?v=1.5" type="text/javascript"></script>
            <script src="/js/schedule.js?v=1.5" type="text/javascript"></script>

            <?php
        } else {
            echo "Cannot find schedule";
        }
        ?>   
    </body>

</html>
