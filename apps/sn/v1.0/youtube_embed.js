var smh_youtube = function () {
    return this.init();
}

smh_youtube.prototype = {
    init: function (pid, eid, width, height, autoplay) {
        this.pid = pid;
        this.eid = eid;
        this.width = width;
        this.height = height;
        this.autoplay = autoplay;
    },
    loadPlayer: function () {
        var width = this.width;
        var height = this.height;
        var autoplay = this.autoplay;
        var sessData = {
            pid: this.pid,
            eid: this.eid
        }
        $smh.ajax({
            type: "GET",
            url: ('https:' == document.location.protocol ? 'https://' : 'http://') + "mediaplatform.streamingmediahosting.com/apps/sn/v1.0/index.php?action=get_youtube_broadcast_id",
            data: sessData,
            dataType: 'json'
        }).done(function (data) {
            if (data['success']) {
                var autoplay_option = '';
                if (autoplay) {
                    autoplay_option = '?autoplay=1';
                }
                $smh('#smhYoutubeContainer').html('<iframe width="' + width + '" height="' + height + '" src="https://www.youtube.com/embed/' + data['bid'] + autoplay_option + '" frameborder="0" allowfullscreen></iframe>');
            }
        });
    }
}

function jq_noconflict() {
    $smh = jQuery.noConflict();
    smh_youtube_embed();
}

var headTag = document.getElementsByTagName("head")[0];
var jqTag = document.createElement('script');
jqTag.type = 'text/javascript';
jqTag.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'mediaplatform.streamingmediahosting.com/html5/html5lib/v2.55/resources/jquery/jquery.min.js?v=1.5';
jqTag.onload = jq_noconflict;
headTag.appendChild(jqTag);
youtube = new smh_youtube();