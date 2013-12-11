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
video.init = function(id, alignment, resizeMode) {
    var ar, mw, player = videojs(id);

    // alignment
    var div = document.getElementById(id);
    var margins = {left: "0 auto 0 0", center: "0 auto", right: "0 0 0 auto"};
    div.style.margin = margins[alignment];

    if (resizeMode != "shrink" && resizeMode != "full") {
        return;
    }
    ar = player.width() / player.height();
    mw = player.width();
    function resize() {
        var w;

        w = document.getElementById(id).parentElement.offsetWidth;
        if (resizeMode == "shrink" && w > mw) {
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
