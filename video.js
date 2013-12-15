/**
 * @file      JavaScript of Video_XH.
 * @version   $Id: autosize.js 73 2013-12-11 22:46:25Z cmb $
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2012-2013 Christoph M. Becker (http://3-magi.net/)
 * @license   GNU GPLv3 (http://www.gnu.org/licenses/gpl-3.0.en.html)
 */

/**
 * The plugin's namespace.
 *
 * @namespace
 */
VIDEO = {};

/**
 * Initializes a video player.
 *
 * The initialization of the actual player is done by Video.js. Here we are just
 * aligning the player element and set up a listener to resize the element to
 * cater for flexible templates.
 *
 * @param {string} id         A player's id attribute.
 * @param {string} alignment  An alignment specifier.
 * @param {string} resizeMode A resize mode specifier.
 */
VIDEO.initPlayer = function (id, alignment, resizeMode) {
    var playerDiv;
    var player;
    var width;
    var aspectRatio;

    /**
     * Aligns the video player.
     */
    function align() {
        switch (alignment) {
        case "center":
            playerDiv.style.margin = "0 auto";
            break;
        case "right":
            playerDiv.style.margin = "0 0 0 auto";
            break;
        }
    }

    /**
     * Resizes the video player.
     */
    function resize() {
        var newWidth;

        newWidth = playerDiv.parentNode.offsetWidth;
        if (newWidth > width && resizeMode == "shrink") {
            newWidth = width;
        }
        player.width(newWidth)
        player.height(Math.round(newWidth / aspectRatio));
    }

    playerDiv = document.getElementById(id);
    player = videojs(id);

    align();

    if (resizeMode == "shrink" || resizeMode == "full") {
        width = player.width();
        aspectRatio = width / player.height();
        resize();
        if (typeof addEventListener != "undefined") {
            addEventListener("resize", resize, false);
        } else if (typeof attachEvent != "undefined")  {
            attachEvent("onresize", resize);
        }
    }
}

/**
 * Initializes the call builder.
 *
 * Sets up event handler properties to automatically update the plugin call when
 * the input changes and to select the whole plugin call when the user clicks in
 * its textarea.
 */
VIDEO.initCallBuilder = function () {
    var elements;
    var keys;
    var i;
    var key;
    var element;

    /**
     * Builds the plugin call.
     */
    function buildPluginCall() {
        var opts;
        var keys;
        var key
        var i;

        opts = [];
        keys = ["width", "height"];
        for (i = 0; i < keys.length; i++) {
            key = keys[i];
            if (elements[key].value != "") {
                opts.push(key + '=' + elements[key].value);
            }
        }
        opts.push("preload=" + elements["preload"].value);
        opts.push("resize=" + elements["resize"].value);
        keys = ["autoplay", "loop", "controls", "centered"];
        for (i = 0; i < keys.length; i++) {
            key = keys[i];
            opts.push(key + "=" + (elements[key].checked ? "1" : "0"));
        }
        elements.call.value = "{{{PLUGIN:video('" + elements.name.value + "', '"
            + opts.join('&') + "');}}}";
    }

    /**
     * Selects the content of an element.
     */
    function selectContent() {
        this.select();
    }

    elements = {};
    keys = [
        "name", "preload", "autoplay", "loop", "controls", "centered",
        "width", "height", "resize", "call"
    ];
    for (i = 0; i < keys.length; i++) {
        key = keys[i];
        element = document.getElementById("video_" + key);
        elements[key] = element;
        if (key != "call") {
            element.onchange = buildPluginCall;
        } else {
            element.onclick = selectContent;
        }
    }
    buildPluginCall();
}
