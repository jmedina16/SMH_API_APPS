<?php
require_once('../../app/clients/php5/KalturaClient.php');

function verfiy_ks($pid, $ks) {
    $success = false;
    $config = new KalturaConfiguration(0);
    $config->serviceUrl = 'http://mediaplatform.streamingmediahosting.com/';
    $client = new KalturaClient($config);
    $partnerFilter = null;
    $pager = null;
    $client->setKs($ks);
    $results = $client->partner->listpartnersforuser($partnerFilter, $pager);

    $partner_id = '';
    foreach ($results->objects as $partnerInfo) {
        $partner_id = $partnerInfo->id;
    }

    if (isset($partner_id) && $partner_id == $pid) {
        $success = array('success' => true, 'pid' => $partner_id);
    } else {
        $success = array('success' => false);
    }

    return $success;
}

function get_regular_player_details($ks, $pid) {
    $url = 'http://mediaplatform.streamingmediahosting.com/index.php/kmc/getuiconfs';
    $fields = array(
        'ks' => urlencode($ks),
        'partner_id' => urlencode($pid),
        'type' => 'player'
    );

    $fields_string = '';
    foreach ($fields as $key => $value) {
        $fields_string .= $key . '=' . $value . '&';
    }
    rtrim($fields_string, '&');

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, count($fields));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $result = curl_exec($ch);
    curl_close($ch);

    $resp = json_decode($result);

    return $resp;
}

function get_playlist_player_details($ks, $pid) {
    $url = 'http://mediaplatform.streamingmediahosting.com/index.php/kmc/getuiconfs';
    $fields = array(
        'ks' => urlencode($ks),
        'partner_id' => urlencode($pid),
        'type' => 'playlist'
    );

    $fields_string = '';
    foreach ($fields as $key => $value) {
        $fields_string .= $key . '=' . $value . '&';
    }
    rtrim($fields_string, '&');

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, count($fields));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $result = curl_exec($ch);
    curl_close($ch);

    $resp = json_decode($result);

    return $resp;
}

$pid = $_GET['pid'];
$ks = $_GET['ks'];
$mode = $_GET['mode'];
$valid = verfiy_ks($pid, $ks);

if ($mode == 's' || $mode == 'cr' || $mode == 'cl' || $mode == 'ct' || $mode == 'cb') {
    $players = get_regular_player_details($ks, $pid);

    $player_select = "<select id='players' style='width: 237px;'>";
    $i = 0;
    foreach ($players as $player) {
        if ($player->id == 6709584 || $player->id == 6709796) {
            
        } else {
            $player_select .= "<option value='" . $player->id . "," . $player->width . "," . $player->height . "'>" . $player->name . "</option>";
            $i++;
        }
    }
    $player_select .= "</select>";
} else if ($mode == 'p') {
    $players = get_playlist_player_details($ks, $pid);

    $player_select = "<select id='players' style='width: 237px;'>";
    $i = 0;
    foreach ($players as $player) {
        if ($player->id == 6709584 || $player->id == 6709796) {
            
        } else {
            $player_select .= "<option value='" . $player->id . "," . $player->width . "," . $player->height . "'>" . $player->name . "</option>";
            $i++;
        }
    }
    $player_select .= "</select>";
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
    <head>
        <title>Streaming Media Hosting Pay Per View</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <style type="text/css">
            #main-wrapper {
                background: -moz-linear-gradient(center top , #FFFCFF 5%, #EDEAED 100%) repeat scroll 0 0 #FFFCFF;
                border: 1px solid #949494;
                border-radius: 6px;
                box-shadow: 0 0 0 0 #FFFFFF inset;
                color: #404040;
                padding: 10px 12px 70px;
                text-decoration: none;
                width: 70%;
                margin-left: auto; 
                margin-right: auto;
                margin-top: 55px;
            }

            #clear {
                clear: both;
            }

            #options{
                width: 500px;
                height: 300px;
                margin-left: auto; 
                margin-right: auto;
                margin-top: 20px;
                margin-bottom: 50px;
            }

            #embed_code {
                resize: none;
            }

            textarea, input[type="text"], input[type="password"], input[type="datetime"], input[type="datetime-local"], input[type="date"], input[type="month"], input[type="time"], input[type="week"], input[type="number"], input[type="email"], input[type="url"], input[type="search"], input[type="tel"], input[type="color"], .uneditable-input {
                background-color: #FFFFFF !important;
                border: 1px solid #CCCCCC !important;
                box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset !important;
                transition: border 0.2s linear 0s, box-shadow 0.2s linear 0s !important;
            }

            textarea:focus, input[type="text"]:focus, input[type="password"]:focus, input[type="datetime"]:focus, input[type="datetime-local"]:focus, input[type="date"]:focus, input[type="month"]:focus, input[type="time"]:focus, input[type="week"]:focus, input[type="number"]:focus, input[type="email"]:focus, input[type="url"]:focus, input[type="search"]:focus, input[type="tel"]:focus, input[type="color"]:focus, .uneditable-input:focus {
                border-color: rgba(82, 168, 236, 0.8) !important;
                box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset, 0 0 8px rgba(82, 168, 236, 0.6) !important;
                outline: 0 none;
            }

            select {
                border-radius: 4px !important;
                color: #555555 !important;
                display: inline-block !important;
                font-size: 14px !important;
                height: 23px !important;
                line-height: 20px !important;
                margin-bottom: 0px !important;
                padding: 1px 6px !important;
                vertical-align: middle !important;
                background-color: #FFFFFF !important;
                border: 1px solid #CCCCCC !important;
            }

            #select-bttn {
                -moz-border-bottom-colors: none !important;
                -moz-border-left-colors: none !important;
                -moz-border-right-colors: none !important;
                -moz-border-top-colors: none !important;
                background-color: #F5F5F5 !important;
                background-image: linear-gradient(to bottom, #FFFFFF, #E6E6E6) !important;
                background-repeat: repeat-x !important;
                border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) #B3B3B3 !important;
                border-image: none !important;
                border-radius: 4px !important;
                border-style: solid !important;
                border-width: 1px !important;
                box-shadow: 0 1px 0 rgba(255, 255, 255, 0.2) inset, 0 1px 2px rgba(0, 0, 0, 0.05) !important;
                color: #333333 !important;
                cursor: pointer !important;
                display: inline-block !important;
                font-size: 12px !important;
                font-weight: bold !important;
                line-height: 20px !important;
                margin: 2px !important;
                padding: 4px 12px !important;
                text-align: center !important;
                text-shadow: 0 1px 1px rgba(255, 255, 255, 0.75) !important;
                vertical-align: middle !important;
            }

            #select-bttn:hover, #select-bttn:focus {
                background-position: 0 -15px !important;
                color: #333333 !important;
                text-decoration: none !important;
                transition: background-position 0.1s linear 0s !important;
            }

            #select-bttn:hover, #select-bttn:focus, #select-bttn:active, #select-bttn.active, #select-bttn.disabled, #select-bttn[disabled] {
                background-color: #E6E6E6 !important;
                color: #333333 !important;
            }

            #select-bttn.active, #select-bttn:active {
                background-image: none !important;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.15) inset, 0 1px 2px rgba(0, 0, 0, 0.05) !important;
                outline: 0 none !important;
            }

            .label-info, .badge-info {
                background-color: #3A87AD !important;
            }

            .label {
                border-radius: 3px !important;
            }

            .label, .badge {
                color: #FFFFFF !important;
                display: inline-block !important;
                font-size: 11.844px !important;
                font-weight: bold !important;
                line-height: 14px !important;
                padding: 2px 4px !important;
                text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25) !important;
                vertical-align: baseline !important;
                white-space: nowrap !important;
            }
        </style>
        <link type="text/css" rel="stylesheet" media="screen" href="http://mediaplatform.streamingmediahosting.com/html5/html5lib/v2.6/kWidget/onPagePlugins/ppv/resources/css/smh_ppv_style.css?1385197029"></link>
        <link type="text/css" rel="stylesheet" media="screen" href="http://mediaplatform.streamingmediahosting.com/html5/html5lib/v2.6/kWidget/onPagePlugins/ppv/resources/css/bootstrap.min.css?1385197029"></link>
        <link type="text/css" rel="stylesheet" media="screen" href="http://mediaplatform.streamingmediahosting.com/html5/html5lib/v2.6/kWidget/onPagePlugins/ppv/resources/css/categoryOnPage.css?1385197029"></link>
    </head>
    <body>
<?php
if ($valid) {
    ?>
            <div id="main-wrapper">
                <div id="ppv-wrapper" style="width: 400px; margin-left: auto; margin-right: auto; margin-top: 20px;">
                    <div style="margin-left: auto; margin-right: auto; text-align: center;"><h3>Preview &amp; Embed</h3></div>
                    <script>
                        ppv_protocol = 'http';
                                                                                                                        
                        function load_smh_ppv(){
                            mw.setConfig('Kaltura.EnableEmbedUiConfJs', true);
                            //mw.setConfig( 'KalturaSupport.LeadWithHTML5' , true );
                            ppv.init(ppv_protocol);
                            ppv.checkAccess(<?php echo $_GET['pid'] ?>,"<?php echo $_GET['sm_ak'] ?>",<?php echo $_GET['uiconf_id'] ?>,<?php echo $_GET['width'] ?>,<?php echo $_GET['height'] ?>,"<?php echo $_GET['entry_id'] ?>",'<?php echo $_GET['mode'] ?>');
                                                                                                                                                                                            
                            $smh('select#players').live('change', function(event) {
                                var p = '';
                                if($smh("#ssl-embed").is(':checked')){
                                    p = 'https';
                                } else {
                                    p = 'http';
                                }
                                            
    <?php if ($mode == 'p' || $mode == 's') { ?>
                    var mode = "<?php echo $mode ?>";        
    <?php } else { ?>
                    var mode = $smh('select#layoutmode option:selected').val();
    <?php } ?>
                var data = $smh('select#players option:selected').val();
                var temp = data.split(',');
                var uiconf_id = temp[0];
                var width = temp[1];
                var height = temp[2];
                ppv.loadVideo('',<?php echo $_GET['pid'] ?>,"<?php echo $_GET['sm_ak'] ?>",uiconf_id,width,height,"<?php echo $_GET['entry_id'] ?>",mode);
                var player = getPlayerEmbed(<?php echo $_GET['pid'] ?>,"<?php echo $_GET['sm_ak'] ?>",uiconf_id,width,height,"<?php echo $_GET['entry_id'] ?>",mode,p);
                $smh('#embed_code').val(player);
                $smh('#prev-result').css("display","none");
            });
                                                                                                                                                    
            var player = '<script>function load_smh_ppv(){mw.setConfig(\'Kaltura.EnableEmbedUiConfJs\', true);ppv.checkAccess(<?php echo $_GET['pid'] ?>,"<?php echo $_GET['sm_ak'] ?>",<?php echo $_GET['uiconf_id'] ?>,<?php echo $_GET['width'] ?>,<?php echo $_GET['height'] ?>,"<?php echo $_GET['entry_id'] ?>","<?php echo $_GET['mode'] ?>");}<\/script><script src='+ppv_protocol+'"://mediaplatform.streamingmediahosting.com/html5/html5lib/v2.6/kWidget/onPagePlugins/ppv/ppv_init.js" type="text/javascript"><\/script><div id="myVideoContainer"></div>';
            var player = getPlayerEmbed(<?php echo $_GET['pid'] ?>,"<?php echo $_GET['sm_ak'] ?>",<?php echo $_GET['uiconf_id'] ?>,<?php echo $_GET['width'] ?>,<?php echo $_GET['height'] ?>,"<?php echo $_GET['entry_id'] ?>","<?php echo $_GET['mode'] ?>", ppv_protocol);
            $smh('#embed_code').val(player);
                                                                                                                    
            $smh('#select-bttn').click(function(event) {
                $smh('#embed_code').select();
                $smh('#prev-result').css({
                    "display":"block",
                    "margin-left":"auto",
                    "margin-right":"auto",
                    "width":"326px",
                    "margin-top":"5px",
                    "margin-bottom":"10px"
                });
                $smh('#prev-result').html('<span class="label label-info">Press Ctrl+C to copy embed code (Command+C on Mac)</span>');       
            });
                                                                                                
            $smh('select#layoutmode').change(function(event) {
                var p = '';
                if($smh("#ssl-embed").is(':checked')){
                    p = 'https';
                } else {
                    p = 'http';
                }
                                
    <?php if ($mode == 'p' || $mode == 's') { ?>
                    var mode = "<?php echo $mode ?>";        
    <?php } else { ?>
                    var mode = $smh('select#layoutmode option:selected').val();
    <?php } ?>
                var data = $smh('select#players option:selected').val();
                var temp = data.split(',');
                var uiconf_id = temp[0];
                var width = temp[1];
                var height = temp[2];
                ppv.loadVideo('',<?php echo $_GET['pid'] ?>,"<?php echo $_GET['sm_ak'] ?>",uiconf_id,width,height,"<?php echo $_GET['entry_id'] ?>",mode);
                var player = getPlayerEmbed(<?php echo $_GET['pid'] ?>,"<?php echo $_GET['sm_ak'] ?>",uiconf_id,width,height,"<?php echo $_GET['entry_id'] ?>",mode,p);
                $smh('#embed_code').val(player);
                $smh('#prev-result').css("display","none");
                                                                                                    
            });
                                        
            $smh('#ssl-embed').click(function(){
                if($smh("#ssl-embed").is(':checked')){
    <?php if ($mode == 'p' || $mode == 's') { ?>
                        var mode = "<?php echo $mode ?>";        
    <?php } else { ?>
                        var mode = $smh('select#layoutmode option:selected').val();
    <?php } ?>
                    var data = $smh('select#players option:selected').val();
                    var temp = data.split(',');
                    var uiconf_id = temp[0];
                    var width = temp[1];
                    var height = temp[2];
                    var player = getPlayerEmbed(<?php echo $_GET['pid'] ?>,"<?php echo $_GET['sm_ak'] ?>",uiconf_id,width,height,"<?php echo $_GET['entry_id'] ?>",mode, 'https');
                    $smh('#embed_code').val(player);
                    $smh('#prev-result').css("display","none");
                    $smh('#ssl-notice').html('Your web server must be configured for SSL in order to support https connections');
                } else {
    <?php if ($mode == 'p' || $mode == 's') { ?>
                        var mode = "<?php echo $mode ?>";        
    <?php } else { ?>
                        var mode = $smh('select#layoutmode option:selected').val();
    <?php } ?>
                    var data = $smh('select#players option:selected').val();
                    var temp = data.split(',');
                    var uiconf_id = temp[0];
                    var width = temp[1];
                    var height = temp[2];
                    var player = getPlayerEmbed(<?php echo $_GET['pid'] ?>,"<?php echo $_GET['sm_ak'] ?>",uiconf_id,width,height,"<?php echo $_GET['entry_id'] ?>",mode, 'http');
                    $smh('#embed_code').val(player);
                    $smh('#prev-result').css("display","none");  
                    $smh('#ssl-notice').empty();                    
                }
            });
        }
                                                                                        
        function getPlayerEmbed(pid, sm_ak, uiconf_id, width, height, entry_id, mode, protocol){
            var player = '<script>ppv_protocol = \''+protocol+'\';function load_smh_ppv(){mw.setConfig(\'Kaltura.EnableEmbedUiConfJs\', true);ppv.init(\''+protocol+'\');ppv.checkAccess('+pid+',"'+sm_ak+'",'+uiconf_id+','+width+','+height+',"'+entry_id+'","'+mode+'");}<\/script><script src="'+protocol+'://mediaplatform.streamingmediahosting.com/html5/html5lib/v2.6/kWidget/onPagePlugins/ppv/ppv_init.js" type="text/javascript"><\/script><div id="myVideoContainer"></div>';
            return player;
        }
                    </script>
                    <script src="http://mediaplatform.streamingmediahosting.com/html5/html5lib/v2.6/kWidget/onPagePlugins/ppv/ppv_init_preview.js" type="text/javascript"></script>
                    <div id="myVideoContainer"></div>
                </div> 
                <div id="clear"></div>
                <div id="options" style="font-size: 14px; font-weight: bold; margin-left: auto; margin-right: auto; margin-top: 10px; width: 355px;">
                    <table>
                        <tr><td style="text-align: right; padding-right: 15px; padding-bottom: 10px;"><div style="width: 96px;">Select Player:</div></td><td style="padding-bottom: 10px;"><?php echo $player_select ?></td></tr>
    <?php if ($mode == 'cr' || $mode == 'cl' || $mode == 'ct' || $mode == 'cb') { ?>
                            <tr><td colspan="2" style="padding-top: 5px; padding-bottom: 10px; text-align: center;">Location of Category List:</td></tr>
                            <tr><td style="text-align: right; padding-right: 15px; padding-bottom: 10px;">Layout Mode:</td><td style="padding-bottom: 10px;"><select style="width: 237px;" id="layoutmode"><option value='cr'>Right of Player</option><option value='cl'>Left of Player</option><option value='ct'>Top of Player</option><option value='cb'>Bottom of Player</option></select></td></tr>
    <?php } ?>
                        <tr><td colspan="2"><span style="margin-right: 10px;">Support for HTTPS Embed Code</span><input type="checkbox" id="ssl-embed"><br><span id="ssl-notice" style="font-size: 12px; color: red;"></span></td></tr>
                        <tr><td colspan="2" style="text-align: center; padding-top: 20px;">Embed Code:<br /><textarea id="embed_code" rows="5" cols="51"></textarea></td></tr>
                        <tr><td colspan="2" style="text-align: center; padding-top: 10px;"><div id="prev-result"></div><button id="select-bttn" class="btn" style="margin: 10px 0 10px 0;">Select Code</button></td></tr>
                    </table>
                </div>
                <div id="clear"></div>
            </div>

<?php } else { ?>
            <div>Error</div>
<?php } ?>
    </body>
</html>