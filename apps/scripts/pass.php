<?php
if (!isset($_GET['setpasshashkey']) || $_GET['setpasshashkey'] == '') {
    header('Location: http://mediaplatform.streamingmediahosting.com');
}
?>
<?php
function ae_detect_ie()
{
    if (isset($_SERVER['HTTP_USER_AGENT']) && 
    (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false))
        return true;
    else
        return false;
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Streaming Media Hosting - Media Platform</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <!--[if IE]>
            <style type="text/css">
                #form-main{width: 319px; margin-top: -15px !important; margin-left: auto; margin-right: auto;}
                #error{position: relative; top: 15px !important;}
            </style>
        <![endif]-->
        <style type="text/css">
            .error{font-size: 10px;}
            .hightlight{border-color:red; border-style:solid; border-width:1px;}
            #ok-ne {
                -moz-box-shadow:inset 0px 0px 0px 0px #ffffff;
                -webkit-box-shadow:inset 0px 0px 0px 0px #ffffff;
                box-shadow:inset 0px 0px 0px 0px #ffffff;
                background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #fffcff), color-stop(1, #edeaed) );
                background:-moz-linear-gradient( center top, #fffcff 5%, #edeaed 100% );
                filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#fffcff', endColorstr='#edeaed');
                background-color:#fffcff;
                -moz-border-radius:6px;
                -webkit-border-radius:6px;
                border-radius:6px;
                border:1px solid #949494;
                display:inline-block;
                color:#404040;
                font-family:Arial;
                font-size:13px;
                font-weight:bold;
                padding:3px 12px;
                text-decoration:none;
                text-shadow:1px 1px 0px #ffffff;
                margin-right: 5px;
                width: 76px;
            }
            #ok-ne:hover {
                background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #edeaed), color-stop(1, #fffcff) );
                background:-moz-linear-gradient( center top, #edeaed 5%, #fffcff 100% );
                filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#edeaed', endColorstr='#fffcff');
                background-color:#edeaed;
            }
            #ok-ne:active {
                position:relative;
                top:1px;
            }

            #ok-nm {
                -moz-box-shadow:inset 0px 0px 0px 0px #ffffff;
                -webkit-box-shadow:inset 0px 0px 0px 0px #ffffff;
                box-shadow:inset 0px 0px 0px 0px #ffffff;
                background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #fffcff), color-stop(1, #edeaed) );
                background:-moz-linear-gradient( center top, #fffcff 5%, #edeaed 100% );
                filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#fffcff', endColorstr='#edeaed');
                background-color:#fffcff;
                -moz-border-radius:6px;
                -webkit-border-radius:6px;
                border-radius:6px;
                border:1px solid #949494;
                display:inline-block;
                color:#404040;
                font-family:Arial;
                font-size:13px;
                font-weight:bold;
                padding:3px 12px;
                text-decoration:none;
                text-shadow:1px 1px 0px #ffffff;
                margin-right: 5px;
                width: 76px;
            }
            #ok-nm:hover {
                background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #edeaed), color-stop(1, #fffcff) );
                background:-moz-linear-gradient( center top, #edeaed 5%, #fffcff 100% );
                filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#edeaed', endColorstr='#fffcff');
                background-color:#edeaed;
            }
            #ok-nm:active {
                position:relative;
                top:1px;
            }

            #button {
                -moz-box-shadow:inset 0px 0px 0px 0px #ffffff;
                -webkit-box-shadow:inset 0px 0px 0px 0px #ffffff;
                box-shadow:inset 0px 0px 0px 0px #ffffff;
                background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #fffcff), color-stop(1, #edeaed) );
                background:-moz-linear-gradient( center top, #fffcff 5%, #edeaed 100% );
                filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#fffcff', endColorstr='#edeaed');
                background-color:#fffcff;
                -moz-border-radius:6px;
                -webkit-border-radius:6px;
                border-radius:6px;
                border:1px solid #949494;
                display:inline-block;
                color:#404040;
                font-family:Arial;
                font-size:13px;
                font-weight:bold;
                padding:3px 12px;
                text-decoration:none;
                text-shadow:1px 1px 0px #ffffff;
                margin-right: 5px;
                width: 76px;
            }
            #button:hover {
                background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #edeaed), color-stop(1, #fffcff) );
                background:-moz-linear-gradient( center top, #edeaed 5%, #fffcff 100% );
                filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#edeaed', endColorstr='#fffcff');
                background-color:#edeaed;
            }
            #button:active {
                position:relative;
                top:1px;
            }

            #ok-ps {
                -moz-box-shadow:inset 0px 0px 0px 0px #ffffff;
                -webkit-box-shadow:inset 0px 0px 0px 0px #ffffff;
                box-shadow:inset 0px 0px 0px 0px #ffffff;
                background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #fffcff), color-stop(1, #edeaed) );
                background:-moz-linear-gradient( center top, #fffcff 5%, #edeaed 100% );
                filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#fffcff', endColorstr='#edeaed');
                background-color:#fffcff;
                -moz-border-radius:6px;
                -webkit-border-radius:6px;
                border-radius:6px;
                border:1px solid #949494;
                display:inline-block;
                color:#404040;
                font-family:Arial;
                font-size:13px;
                font-weight:bold;
                padding:3px 12px;
                text-decoration:none;
                text-shadow:1px 1px 0px #ffffff;
                margin-right: 5px;
                width: 76px;
            }
            #ok-ps:hover {
                background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #edeaed), color-stop(1, #fffcff) );
                background:-moz-linear-gradient( center top, #edeaed 5%, #fffcff 100% );
                filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#edeaed', endColorstr='#fffcff');
                background-color:#edeaed;
            }
            #ok-ps:active {
                position:relative;
                top:1px;
            }

            body{background-color: #272929; font: 82.5% "Lucida Grande",Helvetica,Verdana,Arial;}

            .ui-dialog-title{font-size: 14px !important;}

            #kmcHeader {
                background: url("/lib/images/kmc/kmc_sprite.png") repeat-x scroll 0 -29px transparent;
                height: 29px;
                padding: 4px 7px 0;
            }
            #form-main{width: 319px; margin-top: 20px; margin-left: auto; margin-right: auto;}
        </style>
        <link rel="stylesheet" type="text/css" href="/css/ui-darkness/jquery-ui-1.9.2.custom.css" />
        <script src="/js/jquery.min.js" type="text/javascript"></script>
        <script src="/js/jquery.validate.min.js" type="text/javascript"></script> 
        <script src="/js/jquery-ui.min.js" type="text/javascript"></script>
        <script type="text/javascript">

            function submitFormWithAjax(options){
                var pass1 = $('input[name=pass1]').val();
                var pass2 = $('input[name=pass2]').val();
                  
                if (pass1=='') {
                    $('#pass1').addClass('hightlight');
                    $('#error').html('This field cannot be empty!');
                    return false;
                } else {
                    $('#pass1').removeClass('hightlight');
                    $('#error').empty();
                } 

                if (pass2=='') {
                    $('#pass2').addClass('hightlight');
                    $('#error').html('This field cannot be empty!');
                    return false;
                } else {
                    $('#pass2').removeClass('hightlight');
                    $('#error').empty();                    
                }
                
                if(pass1 != pass2){
                    error_nm();
                    return false;
                }
                
                var length = /^.{8,14}$/;
                var lowercase = /^(?=.*[a-z]).+$/;
                var digit = /^(?=.*\d).+$/;
                var special = /^(?=.*[%~!@#$^*=+?[\]{}])+/;
                var arrows = /^[^<>]+$/;
                
                if(!length.test(pass2) || !lowercase.test(pass2) || !arrows.test(pass2) || !digit.test(pass2) || !special.test(pass2)){                    
                    error_ne();
                    return false;                   
                }    
                                
                var defaults = {
                    animatePadding: 60,
                    ApiUrl:	"/api_v3/index.php?",
                    sessSrv: 	"user",
                    sessAct:	"setInitialPassword",
                    format:	"1"
                };
        
                var options = $.extend(defaults, options);
                var o = options;
                 
                var sessData = "hashKey=<?php echo $_GET['setpasshashkey'] ?>&newPassword="+pass1;
                    
                var reqObj = {
                    service: o.sessSrv, 
                    action: o.sessAct, 
                    format: o.format        
                };

                var reqUrl = o.ApiUrl + jQuery.param(reqObj);
   
                $.ajax({
                    cache: 			false,
                    url:			reqUrl,
                    type:			'POST',
                    data:			sessData,
                    dataType:		'json',
                    success:function(data) {
                        if(data == null){
                            pass_succ(); 
                        } else {
                            if(data['code'] == "NEW_PASSWORD_HASH_KEY_INVALID" || data['code'] == "NEW_PASSWORD_HASH_KEY_EXPIRED"){
                                alert("This link is invalid or has expired.");
                            }
                        }                        
                    }
                });
                return false;
               event.preventDefault();

            }
            
            function error_ne(){
                var content = '<div style="font-size: 12px; width: 340px; margin-left: auto; margin-right: auto;">The password you entered has an invalid structure.<br />\
Passwords must obey the following rules :<br />\
- Must be of length between 8 and 14.<br />\
- Must not contain your name<br />\
- Must contain at least one lowercase letter (a-z).<br />\
- Must contain at least one digit (0-9).<br />\
- Must contain at least one of the following symbols: %~!@#$^*=+?[]{}.<br />\
- Must not contain the following characters: < or >.<div style="width: 60px; margin-left: auto; margin-right: auto;"><button style="width: 60px; margin-top: 15px;" id="ok-ne">OK</button></div>';

                $('.form_error_ne').html(content);
                $('.form_error_ne').dialog({
                    open: function(event, ui) {
                        $(".ui-dialog-titlebar-close").hide();
                    },
                    title: "Error Occurred",
                    resizable: false,    
                    width: 415,
                    height: 250,
                    modal: true,
                    draggable: true,
                    close: function(){
                        $(this).dialog('destroy');
                        $(".ui-dialog-titlebar-close").show();
                    }
                });

                $('.form_error_ne').dialog('open');
            }
            
            function error_nm(){
                var content = '<div style="font-size: 12px; width: 163px; margin-left: auto; margin-right: auto;">Passwords do not match.<br />\
<div style="width: 60px; margin-left: auto; margin-right: auto;"><button style="width: 60px; margin-top: 15px;" id="ok-nm">OK</button></div>';

                $('.form_error_nm').html(content);
                $('.form_error_nm').dialog({
                    open: function(event, ui) {
                        $(".ui-dialog-titlebar-close").hide();
                    },
                    title: "Error Occurred",
                    resizable: false,    
                    width: 268,
                    height: 130,
                    modal: true,
                    draggable: true,
                    close: function(){
                        $(this).dialog('destroy');
                        $(".ui-dialog-titlebar-close").show();
                    }
                });

                $('.form_error_nm').dialog('open');
            }
            
            function pass_succ(){
                var content = '<div style="font-size: 11px; width: 264px; margin-left: auto; margin-right: auto; text-align: center;"><strong>Your new password has been set,<br /> You can now login to the Media Portal.</strong>\
<div style="width: 60px; margin-left: auto; margin-right: auto;"><button style="width: 60px; margin-top: 15px;" id="ok-ps">OK</button></div>';

                $('.pass_dialog').html(content);
                $('.pass_dialog').dialog({
                    open: function() {
                        $(this).closest(".ui-dialog").find(".ui-dialog-titlebar:first").hide();
                    },
                    resizable: false,    
                    width: 295,
                    height: 135,
                    modal: true,
                    draggable: true,
                    close: function(){
                        $(this).dialog('destroy');
                        window.location = "http://mediaplatform.streamingmediahosting.com";
                    }
                });

                $('.pass_dialog').dialog('open');
            }            

            $(document).ready(function() {
                <?php  if (ae_detect_ie()) {
                    echo '$("#button").click(function(){
                        submitFormWithAjax();
                          });';
                    
                } else {
                    echo '$("#form").submit(function(e){
                        e.preventDefault();
                        submitFormWithAjax();
                        return false;
                          });';                    
                }?>
     
                $('<div>').addClass('form_error_nm').dialog({
                    autoOpen:false
                });
                
                $('<div>').addClass('form_error_ne').dialog({
                    autoOpen:false
                });
                
                $('<div>').addClass('pass_dialog').dialog({
                    autoOpen:false
                });                
            }); 
            
            $('#ok-nm').live('click', function(event) {
                $('.form_error_nm').dialog('close');
            });       
            
            $('#ok-ne').live('click', function(event) {
                $('.form_error_ne').dialog('close');
            });
            
            $('#ok-ps').live('click', function(event) {
                $('.pass_dialog').dialog('close');
            });

        </script>
    </head>
    <body>
        <div id="wrap">
            <div id="kmcHeader">
                <img height="22px" src="/img/SMH_Logo_v3.png" alt="Media Portal" />
            </div>
        </div>
        <div style="width: 399px; height: 250px; margin-left: auto; margin-right: auto; background-color: #FAFAFA; margin-top: 100px;" id="pass-form">
            <div id="title" class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix" style="padding: 10px; font-size: 15px; font-weight: bold; border-bottom: solid 1px;">Set Password</div>
            <div id="field-head" style="text-align: center; margin-top: 15px; font-size: 14px;">
                Welcome to Streaming Media Hosting's Media Platform<br />Please enter your password.
            </div>
            <div id="form-main">
                <div id="error" style="width: 200px; margin-left: auto; margin-right: auto; text-align: center; color: red; font-weight: bold;"></div>
                <form id="form">
                    <table width="327px">
                        <tr>
                            <td>New Password:</td><td><input type="password" id="pass1" name="pass1" value="" size="25" /></td>
                        </tr>
                        <tr>
                            <td>Confirm Password:</td><td><input type="password" id="pass2" name="pass2" value="" size="25" /></td>
                        </tr>
                        <tr>
                            <td colspan="2"><div style="float: left; margin-top: 30px;"><a href="http://mediaplatform.streamingmediahosting.com">Login</a></div><div style="width: 20px; float: right; margin-right: 65px; margin-top: 27px;"><button id="button">Send</button></div></td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
    </body>
</html>