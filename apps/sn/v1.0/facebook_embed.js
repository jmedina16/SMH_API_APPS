var smh_facebook = function () {
    return this.init();
}

smh_facebook.prototype = {
    init: function (pid, width, height) {
        this.pid = pid;
        this.width = width;
        this.height = height;
    },
    loadPlayer: function () {
        var width = this.width;
        var height = this.height;
        var sessData = {
            pid: this.pid
        }
        $smh.ajax({
            type: "GET",
            url: ('https:' == document.location.protocol ? 'https://' : 'http://') + "mediaplatform.streamingmediahosting.com/apps/sn/v1.0/dev.php?action=get_facebook_embed",
            data: sessData,
            dataType: 'json'
        }).done(function (data) {
            if (data['success']) {
                var src = data['src'] + '&width=' + width + '&height='+height;
                $smh('#smhFacebookContainer').html('<iframe width="' + width + '" height="' + height + '" src="' + src + '" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowTransparency="true" allowFullScreen="true"></iframe>');
            }
        });
    }
}

function jq_noconflict() {
    $smh = jQuery.noConflict();
    smh_facebook_embed();
}

var headTag = document.getElementsByTagName("head")[0];
var jqTag = document.createElement('script');
jqTag.type = 'text/javascript';
jqTag.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'mediaplatform.streamingmediahosting.com/html5/html5lib/v2.52.3/resources/jquery/jquery.min.js?v=1.5';
jqTag.onload = jq_noconflict;
headTag.appendChild(jqTag);
facebook = new smh_facebook();