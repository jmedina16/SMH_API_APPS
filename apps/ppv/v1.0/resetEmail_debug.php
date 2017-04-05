<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>Reset Email</title>
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css" />
        <link href="/css/tooltipster.css?v=1" rel="stylesheet" />
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
        <script src="//code.jquery.com/ui/1.10.3/jquery-ui.js?v=1"></script>
        <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
        <script src="/js/jquery.form.js?v=1" type="text/javascript"></script>
        <script src="/js/jquery.tooltipster.min.js?v=1" type="text/javascript"></script>
        <script src="/js/jquery.validate.min.js?v=1" type="text/javascript"></script>
        <style>
            .tooltipster-base {
                width: auto !important;
            }
            .validate-error {
                border-color: #E9322D !important;
                box-shadow: 0 0 6px #F8B9B7 !important;
            }
            #error {
                margin-left: auto; 
                margin-right: auto; 
                text-align: center; 
                margin-bottom: 25px; 
                width: 400px; 
                display: none;
            }
        </style>
    </head>
    <body>
        <?php
        if ((isset($_GET['pid']) && !empty($_GET['pid'])) && (isset($_GET['sm_ak']) && !empty($_GET['sm_ak'])) && (isset($_GET['reset_token']) && !empty($_GET['reset_token'])) && (isset($_GET['email']) && !empty($_GET['email'])) && (isset($_GET['url']) && !empty($_GET['url']))) {
            ?>
            <div style="width: auto; margin-left: auto; margin-right: auto; margin-top: 160px; font-size: 17px;" id="pass-wrapper">
                <div style="margin-top: 30px; margin-left: auto; margin-right: auto; text-align: center; width: auto; height: 117px;">
                    <h3>Email Reset</h3>
                    Your current email address is <?php echo $_GET['email'] ?>.<br /> Please enter your new email address and your password. 
                </div>
                <form id="smh-email-form" action="">
                    <div id="error"></div>
                    <table style="width: 450px; margin-left: auto; margin-right: auto;">
                        <tr>
                            <td style="padding-bottom: 20px; text-align:right; padding-right:30px;">New Email Address</td><td style="padding-bottom: 20px;"><input type="text" placeholder="Enter your new email" id="smh-email" name="email" class="form-control" /></td>
                        </tr>
                        <tr>
                            <td style="padding-bottom: 20px; text-align:right; padding-right:30px;">Confirm Email Address</td><td style="padding-bottom: 20px;"><input type="text" placeholder="Re-enter your new email" id="smh-email2" name="email2" class="form-control" /></td>
                        </tr>  
                        <tr>
                            <td style="padding-bottom: 20px; text-align:right; padding-right:30px;">Password</td><td style="padding-bottom: 20px;"><input type="password" placeholder="Enter your password" id="smh-pass" name="pass" class="form-control" /></td>
                        </tr>
                        <tr>
                            <td><span id="reset-loading" style="margin-right: 20px; height: 20px; float: right; display: none;"><img src="/img/loading.gif" height="20px" /></span></td><td><button style="width: 112px; display: block; font-size: 15px;" type="button" class="btn btn-primary" id="submit-btn" data-dismiss="modal" onclick="resetEmail();">Submit</button></td>
                        </tr>
                    </table>  
                </form>
            </div>
            <?php
        } else {
            ?>
            <div style="width: 500px; margin-left: auto; margin-right: auto; margin-top: 240px; font-size: 17px;" id="loading">
                <div style="margin-top: 30px; width: 388px; margin-left: auto; margin-right: auto; text-align: center; height: 125px;">
                    <h3>Invalid URL</h3>
                    This link is invalid. Please try again. 
                </div>            
            </div>
            <?php
        }
        ?>
        <script>   
            var validator;
            $(document).ready(function(){                  
                
                $('#smh-email-form input').tooltipster({
                    trigger: 'custom',
                    onlyOne: false,
                    position: 'right'
                });     
                validator = $("#smh-email-form").validate({
                    highlight: function(element, errorClass) {
                        $(element).removeClass("valid").removeClass("error").addClass("validate-error");
                    },        
                    unhighlight: function(element, errorClass) {
                        $(element).removeClass("valid").removeClass("validate-error");
                    },
                    errorPlacement: function (error, element) {
                        $(element).tooltipster('update', $(error).text());
                        $(element).tooltipster('show');
                    },
                    success: function (label, element) {
                        $(element).tooltipster('hide');
                    },
                    rules:{
                        email:{
                            required: true
                        },
                        email2:{
                            required: true,
                            equalTo: '#smh-email'
                        },
                        pass:{
                            required: true,
                            minlength: 5
                        }
                    },
                    messages: {
                        email:{
                            required: "Please enter an email"
                        },
                        email2:{
                            required: "Please enter an email",
                            equalTo: 'Emails do not match'
                        },
                        pass:{
                            required: "Please enter a password",
                            minlength: "Your password must be at least 5 characters long"
                        }
                    }
                });
            });
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
            function resetEmail(){                
                var valid = validator.form();
                if(valid){
                    var email = $('#smh-email').val();
                    var pass = $('#smh-pass').val();
                    var sessData = {
                        pid: <?php echo $_GET['pid'] ?>,
                        sm_ak: '<?php echo $_GET['sm_ak'] ?>',
                        email: '<?php echo $_GET['email'] ?>',
                        reset_token: '<?php echo $_GET['reset_token'] ?>',
                        new_email: email,
                        pass: pass
                    }
                    $.ajax({
                        type: "GET",
                        url: "https://mediaplatform.streamingmediahosting.com/apps/ppv/v1.0/dev.php?action=reset_email",
                        data: sessData,
                        dataType: 'json',
                        beforeSend: function() {                    
                            $('#reset-loading').css('display','block');
                            $('#submit-btn').attr('disabled','');
                        }
                    }).done(function(data) {
                        $('#reset-loading').css('display','none');
                        $('.tooltipster-base').remove();
                        if(data['success']){
                            $('#pass-wrapper').html('<div style="margin-top: 30px; width: 388px; margin-left: auto; margin-right: auto; text-align: center; height: 125px;">'+
                                '<h3>Email successfully reset!</h3>'+
                                'You may now log in with your new email address.'+
                                '</div>'+
                                '<div class="modal-footer">'+
                                '<button style="margin-left: auto; margin-right: auto; width: 112px; display: block; font-size: 15px;" type="button" class="btn btn-primary" data-dismiss="modal" onclick="Redirect(<?php echo "\'" . base64_decode($_GET['url']) . "\'"; ?>);">Return to site</button>'+
                                '</div>');
                        } else {
                            if(data['message'] == 'Key deactivated'){
                                $('#pass-wrapper').html('<div style="margin-top: 30px; width: 388px; margin-left: auto; margin-right: auto; text-align: center; height: 125px;">'+
                                    '<h3>Key Deactivated</h3>'+
                                    'This key has been used and is deactivated.'+
                                    '</div>');
                            } else if(data['message'] == 'Wrong password'){
                                $('#error').css('display','block');
                                $('#reset-loading').css('display','none');
                                $('#error').html('<span class="label label-danger">Sorry, the password is invalid.</span>');
                                setTimeout(function(){   
                                    $('#error').css('display','none');
                                    $('#submit-btn').removeAttr('disabled');
                                },3000);
                            } else if(data['message'] == 'Invalid key'){
                                $('#pass-wrapper').html('<div style="margin-top: 30px; width: 388px; margin-left: auto; margin-right: auto; text-align: center; height: 125px;">'+
                                    '<h3>Invalid Key</h3>'+
                                    'This key is invalid.'+
                                    '</div>');
                            } else if(data['message'] == 'No changes made'){
                                $('#error').css('display','block');
                                $('#reset-loading').css('display','none');
                                $('#error').html('<span class="label label-danger">No changes have been made to this account.</span>');
                                setTimeout(function(){   
                                    $('#error').css('display','none');
                                    $('#submit-btn').removeAttr('disabled');
                                },3000);
                            } else if(data['message'] == 'User already exists'){
                                $('#error').css('display','block');
                                $('#reset-loading').css('display','none');
                                $('#error').html('<span class="label label-danger">This email is already in use, please try again.</span>');
                                setTimeout(function(){   
                                    $('#error').css('display','none');
                                    $('#submit-btn').removeAttr('disabled');
                                },3000);
                            } else {
                                $('#pass-wrapper').html('<div style="margin-top: 30px; width: 388px; margin-left: auto; margin-right: auto; text-align: center; height: 125px;">'+
                                    '<h3>Error</h3>'+
                                    'Sorry, an error has occurred. Please try again later.'+
                                    '</div>');
                            }                            
                        }
                    });
                }
            }
        </script>
    </body>
</html>