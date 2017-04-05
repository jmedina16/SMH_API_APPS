<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>Reset Password</title>
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
        </style>
    </head>
    <body>
        <?php
        if ((isset($_GET['pid']) && !empty($_GET['pid'])) && (isset($_GET['sm_ak']) && !empty($_GET['sm_ak'])) && (isset($_GET['reset_token']) && !empty($_GET['reset_token'])) && (isset($_GET['email']) && !empty($_GET['email'])) && (isset($_GET['url']) && !empty($_GET['url']))) {
            ?>
            <div style="width: 500px; margin-left: auto; margin-right: auto; margin-top: 160px; font-size: 17px;" id="pass-wrapper">
                <div style="margin-top: 30px; width: 400px; margin-left: auto; margin-right: auto; text-align: center; height: 90px;">
                    <h3>Password Reset</h3>
                    Please enter your new password below. 
                </div>
                <form id="smh-password-form" action="">
                    <table style="width: 400px; margin-left: auto; margin-right: auto;">
                        <tr>
                            <td style="padding-bottom: 20px; text-align:right; padding-right:30px;">New Password</td><td style="padding-bottom: 20px;"><input type="password" placeholder="Enter your new password" id="smh-pass" name="pass" class="form-control" /></td>
                        </tr>
                        <tr>
                            <td style="padding-bottom: 20px; text-align:right; padding-right:30px;">Confirm Password</td><td style="padding-bottom: 20px;"><input type="password" placeholder="Re-enter your new password" id="smh-pass2" name="pass2" class="form-control" /></td>
                        </tr>  
                        <tr>
                            <td><span id="reset-loading" style="margin-right: 20px; height: 20px; float: right; display: none;"><img src="/img/loading.gif" height="20px" /></span></td><td><button style="width: 112px; display: block; font-size: 15px;" type="button" class="btn btn-primary" id="submit-btn" data-dismiss="modal" onclick="resetPassword();">Submit</button></td>
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
                $.validator.addMethod('mypassword', function(value, element) {
                    return this.optional(element) || (value.match(/[A-Z]/) && value.match(/[a-z]/) && value.match(/[0-9]/));
                },
                'Password must contain at least one uppercase letter, one lowercase letter, and one number.');  
                
                $('#smh-password-form input').tooltipster({
                    trigger: 'custom',
                    onlyOne: false,
                    position: 'right'
                });     
                validator = $("#smh-password-form").validate({
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
                        pass:{
                            required: true,
                            mypassword: true,
                            minlength: 5
                        },
                        pass2:{
                            required: true,
                            minlength: 5,
                            equalTo: '#smh-pass'
                        }
                    },
                    messages: {
                        pass:{
                            required: "Please enter a password",
                            minlength: "Your password must be at least 5 characters long"
                        },
                        pass2:{
                            required: "Please enter a password",
                            minlength: "Your password must be at least 5 characters long",
                            equalTo: 'Passwords do not match'
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
            function resetPassword(){                
                var valid = validator.form();
                if(valid){
                    var pass = $('#smh-pass').val();
                    var sessData = {
                        pid: <?php echo $_GET['pid'] ?>,
                        sm_ak: '<?php echo $_GET['sm_ak'] ?>',
                        email: '<?php echo $_GET['email'] ?>',
                        reset_token: '<?php echo $_GET['reset_token'] ?>',
                        pass: pass
                    }
                    $.ajax({
                        type: "GET",
                        url: "https://mediaplatform.streamingmediahosting.com/apps/ppv/v1.0/index.php?action=reset_pass",
                        data: sessData,
                        dataType: 'json',
                        beforeSend: function() {                    
                            $('#reset-loading').css('display','block');
                            $('#submit-btn').attr('disabled','');
                        }
                    }).done(function(data) {
                        $('#reset-loading').css('display','none');
                        $('#submit-btn').removeAttr('disabled');
                        $('.tooltipster-base').remove();
                        if(data['success']){
                            $('#pass-wrapper').html('<div style="margin-top: 30px; width: 388px; margin-left: auto; margin-right: auto; text-align: center; height: 125px;">'+
                                '<h3>Password successfully reset!</h3>'+
                                'You can now login with your new password.'+
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
                            } else if(data['message'] == 'Invalid key'){
                                $('#pass-wrapper').html('<div style="margin-top: 30px; width: 388px; margin-left: auto; margin-right: auto; text-align: center; height: 125px;">'+
                                    '<h3>Invalid Key</h3>'+
                                    'This key is invalid.'+
                                    '</div>');
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