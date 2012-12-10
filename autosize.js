/**
 * Front-End JS of Video_XH.
 *
 * @version: $Id$
 */


var video = {

    autosize: function(player, mode) {
        var ar;

        if (mode == 0) {
            return;
        }
        ar = player.width() / player.height();
        function resizeVideoJS() {
            var w;

            w = document.getElementById(player.id).parentElement.offsetWidth;
            player.width(w).height(Math.round(w / ar));
        }
        resizeVideoJS();
        if (window.addEventListener) {
            window.addEventListener('resize', resizeVideoJS, false);
        } else if (window.attachEvent)  {
            window.attachEvent('onresize', resizeVideoJS);
        }

    }
}
