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
 * Registers an event listener.
 *
 * @param {EventTarget}   target
 * @param {string}        eventName An event name.
 * @param {EventListener} listener  A listener function.
 */
VIDEO.register = function (target, eventName, listener) {
    if (typeof target.addEventListener != "undefined") {
        target.addEventListener(eventName, listener, false);
    } else if (typeof target.attachEvent != "undefined")  {
        target.attachEvent("on" + eventName, listener);
    }
}

/**
 * Initializes a video player.
 *
 * @param {string} id         A player's id attribute.
 * @param {string} alignment  An alignment specifier.
 * @param {string} resizeMode A resize mode specifier.
 */
VIDEO.init = function (id, alignment, resizeMode) {
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
        VIDEO.register(window, "resize", resize);
    }
}

/**
 * The relevant elements of the call builder.
 *
 * @type {Array.<HTMLInputElement>}
 */
VIDEO.callBuilderElements = {};

/**
 * Initializes the call builder.
 */
VIDEO.initCallBuilder = function () {
    var keys;
    var i;
    var key;
    var element;

    keys = [
        "name", "preload", "autoplay", "loop", "controls",
        "width", "height", "align", "resize", "call"
    ];
    for (i = 0; i < keys.length; i++) {
        key = keys[i];
        element = document.getElementById("video_" + key);
        VIDEO.callBuilderElements[key] = element;
        if (key != "call") {
            VIDEO.register(element, "change", VIDEO.buildPluginCall);
        } else {
            VIDEO.register(element, "click", function () {
                this.select();
            });
        }
    }
    VIDEO.buildPluginCall();
}

/**
 * Builds the plugin call.
 */
VIDEO.buildPluginCall = function () {
    var elements;
    var opts;
    var keys;
    var key
    var i;

    elements = VIDEO.callBuilderElements;
    opts = [];
    keys = ["width", "height"];
    for (i = 0; i < keys.length; i++) {
        key = keys[i];
        if (elements[key].value != "") {
            opts.push(key + '=' + elements[key].value);
        }
    }
    opts.push("preload=" + elements["preload"].value);
    opts.push("align=" + elements["align"].value);
    opts.push("resize=" + elements["resize"].value);
    keys = ["autoplay", "loop", "controls"];
    for (i = 0; i < keys.length; i++) {
        key = keys[i];
        opts.push(key + "=" + (elements[key].checked ? "1" : "0"));
    }
    elements.call.value = "{{{PLUGIN:video('" + elements.name.value + "', '"
        + opts.join('&') + "');}}}";
}
