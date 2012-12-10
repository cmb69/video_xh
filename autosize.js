/**
 * Front-End JS of Video_XH.
 *
 * @version: $Id$
 */


var video = {

    autosize: function(player, mode) {
        var ar, mw;

        if (mode == 0) {
            return;
        }
        ar = player.width() / player.height();
        mw = player.width();
        function resize() {
            var w;

            w = document.getElementById(player.id).parentElement.offsetWidth;
            if (mode == 1 && w > mw) {
                w = mw;
            }
            player.width(w).height(Math.round(w / ar));
        }
        resize();
        if (window.addEventListener) {
            window.addEventListener('resize', resize, false);
        } else if (window.attachEvent)  {
            window.attachEvent('onresize', resize);
        }

    }
}
