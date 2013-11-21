/**
 * Front-End JS of Video_XH.
 *
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @version: SVN: $Id$
 */

    /**
     * @namespace video
     */

if (typeof video == "undefined") {
    video = {}
}


/**
 * @param {Object}
 * @param {String}
 */
video.autosize = function(player, mode) {
    var ar, mw;

    if (mode != "shrink" && mode != "full") {
        return;
    }
    ar = player.width() / player.height();
    mw = player.width();
    function resize() {
        var w;

        w = document.getElementById(player.id).parentElement.offsetWidth;
        if (mode == "shrink" && w > mw) {
            w = mw;
        }
        player.width(w).height(Math.round(w / ar));
    }
    resize();
    if (typeof window.addEventListener != "undefined") {
        window.addEventListener("resize", resize, false);
    } else if (typeof window.attachEvent != "undefined")  {
        window.attachEvent("onresize", resize);
    }
}
