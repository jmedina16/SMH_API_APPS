<?php
require_once("init.ppv.php");
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta content="text/html; charset=utf-8" http-equiv="Content-type">
        <meta content="width=device-width, initial-scale=1" name="viewport">
        <title>Payment Terminal</title>
        <link rel="stylesheet" type="text/css" href="/html5/html5lib/v2.35/kWidget/onPagePlugins/ppv_dev/resources/css/bootstrap.min.css" />
        <link rel="stylesheet" type="text/css" href="/html5/html5lib/v2.35/kWidget/onPagePlugins/ppv_dev/resources/css/font-awesome.min.css" />
        <link rel="stylesheet" type="text/css" href="/html5/html5lib/v2.35/kWidget/onPagePlugins/ppv_dev/resources/css/payment.css" />
        <script type="text/javascript" src="/html5/html5lib/v2.35/resources/jquery/jquery.min.js"></script>
        <script type="text/javascript" src="/html5/html5lib/v2.35/kWidget/onPagePlugins/ppv_dev/resources/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="/html5/html5lib/v2.35/kWidget/onPagePlugins/ppv_dev/resources/js/jquery.validate.min.js"></script>
        <script type="text/javascript" src="/html5/html5lib/v2.35/kWidget/onPagePlugins/ppv_dev/resources/js/payment.js"></script>
        <script type="text/javascript" src="/html5/html5lib/v2.35/kWidget/onPagePlugins/ppv_dev/resources/js/jquery.payment.min.js"></script>
    </head>
    <div id="payment-wrapper">
        <div class="row row-centered">
            <div class="col-md-4 col-md-offset-4">
                <div id="checkout">
                    <h1 id="payment-header">Order Summary</h1>
                    <form novalidate autocomplete="on" id="payment-form" class="form-horizontal">
                        <fieldset>
                            <div class="col-sm-5 detail-m detail-l"><img width="100%" src="<?php echo $ppv->getThumb(); ?>" /></div>
                            <div class="col-sm-7 detail-m detail-r">
                                <div class="form-group">
                                    <label for="PaymentAmount">Title</label>
                                    <div class="title-placeholder">
                                        <span><?php echo $ppv->getTitle(); ?></span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="PaymentAmount">Payment amount</label>
                                    <div class="amount-placeholder">
                                        <span><?php echo $ppv->getCurrencySymbol() . $ppv->getPrice() . $ppv->getCurrency() . $ppv->getPeriod(); ?></span>
                                    </div>
                                </div>
                            </div>  
                            <div class="title-details">
                                <?php echo $ppv->getDesc(); ?>
                            </div>
                        </fieldset>
                        <fieldset>
                            <legend>User Details</legend>
                            <div class="form-group has-feedback">
                                <label for="textinput" class="col-sm-3">Full Name</label>
                                <div class="col-sm-6">
                                    <?php echo $ppv->getName(); ?>
                                </div>
                            </div>
                            <div class="form-group has-feedback">
                                <label for="textinput" class="col-sm-3">Email</label>
                                <div class="col-sm-6">
                                    <?php echo $ppv->getEmail(); ?>
                                </div>
                            </div>                       
                        </fieldset>
                        <fieldset>
                            <legend>Card Details</legend>
                            <div class="col-sm-12">
                                <div class="card-row">
                                    <span class="visa"></span><span class="mastercard"></span><span class="amex"></span><span class="discover"></span>
                                </div>
                                <div class="form-group">
                                    <label>Name on card</label>
                                    <input type="text" name="cardName" class="form-control" id="cc-name" placeholder="Name on card">
                                </div>
                                <div class="form-group">
                                    <label>Card number</label>
                                    <input id="cc-number" name="cardNumber" type="tel" class="card-image form-control cc-number" autocomplete="cc-number" placeholder="&bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull;" required>
                                </div>
                                <div class="expiry-date-group form-group">
                                    <label>Expiry date</label>
                                    <input id="cc-exp" name="cardExpiry" type="tel" placeholder="MM / YY" class="form-control cc-exp" autocomplete="cc-exp" required>
                                </div>
                                <div class="security-code-group form-group">
                                    <label>Security code</label>
                                    <div class="input-container">
                                        <input id="cc-cvc" name="cardCVC" type="tel" class="form-control cc-cvc" placeholder="&bull;&bull;&bull;" autocomplete="off" required><i class="fa fa-question-circle"></i>
                                    </div>
                                    <div style="display:none;right:0;top:0;" class="popover top">
                                        <div style="left:auto;right:16px;" class="arrow"></div>
                                        <div class="popover-content">
                                            <p>
                                                <span>For Visa, Mastercard, and Discover (left), the 3 digits on the </span>
                                                <em>back</em>
                                                <span> of your card.</span>
                                            </p>
                                            <p>
                                                <span>For American Express (right), the 4 digits on the </span>
                                                <em>front</em>
                                                <span> of your card.</span>
                                            </p>
                                            <div class="cvc-preview-container two-card">
                                                <div class="amex-cvc-preview"></div>
                                                <div class="visa-mc-dis-cvc-preview"></div>                                                    
                                            </div>                                                
                                        </div>                                            
                                    </div>                                        
                                </div>
                                <div class="zip-code-group form-group">
                                    <label>ZIP/Postal code</label>
                                    <div class="input-container">
                                        <input id="zipcode" name="zipcode" type="tel" class="form-control" placeholder="Zip code" required><i class="fa fa-question-circle"></i>
                                    </div>
                                    <div style="display: none; left: 11px; top: 372.7px;" class="popover top">
                                        <div style="left:auto;right:16px;" class="arrow"></div>
                                        <div class="popover-content">
                                            <div>Enter the ZIP/Postal code for your credit card's billing address.</div>                                                
                                        </div>                                            
                                    </div>                                        
                                </div>
                                <div class="row" style="display:none;">
                                    <div class="col-xs-12">
                                        <p class="payment-errors"></p>
                                    </div>
                                </div>
                                <button class="btn btn-block btn-success submit-button" id="PayButton" onclick="smhPay.submitPayment(); return false;">
                                    <span class="submit-button-lock fa fa-lock"></span><span class="align-middle"><?php $button_text = ($_GET['sid'] == -1)? 'Pay Now': 'Subscribe'; echo $button_text; ?></span>
                                </button>
                                <div id="disclaimer">Your card will be charged after clicking on the "<?php echo $button_text; ?>" button. This is a secure 128-bit SSL encrypted payment.</div>
                            </div>
                        </fieldset>
                    </form>
                </div>   
                <div id="cancel" style="margin-top: 20px; font-size: 16px ! important; text-align: center; color: rgb(44, 154, 183);"><a id="cancel-link" onclick="smhPay.cancelOrder('<?php echo base64_decode($_GET['r_url']) ?>');">Cancel and return to site</a></div>
            </div>
        </div>
    </div>
</body>
</html>