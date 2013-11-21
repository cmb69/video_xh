/**
 * Back-end JavaScript of Video_XH.
 *
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @version  SVN: $Id$
 */

    /**
     * @namespace video
     */

if (typeof video == "undefined") {
    var video = {}
}


/**
 * The relevant elements of the call builder.
 *
 * @type {Array.<HTMLInputElement>}
 */
video.callBuilderElements = {};


/**
 * Initializes the call builder.
 */
video.initCallBuilder = function() {
    var keys, i, len, key, element;

    keys = [
        "name", "preload", "autoplay", "loop", "controls",
        "width", "height", "resize", "call"
    ];
    for (i = 0, len = keys.length; i < len; i++) {
        key = keys[i];
        element = document.getElementById("video_" + key);
        video.callBuilderElements[key] = element;
        if (key != 'call') {
            element.onchange = video.buildPluginCall;
        } else {
            element.onclick = function() {this.select()}
        }
    }
    video.buildPluginCall();
}


/**
 * Builds the plugin call.
 */
video.buildPluginCall = function() {
    var elements = video.callBuilderElements,
        opts = [],
        keys, key, i, len;

    keys = ["width", "height"];
    for (i = 0, len = keys.length; i < len; i++) {
        key = keys[i];
        if (elements[key].value != '') {
            opts.push(key + '=' + elements[key].value);
        }
    }

    opts.push("preload=" + elements["preload"].value);
    opts.push("resize=" + elements["resize"].value);

    keys = ["autoplay", "loop", "controls"];
    for (i = 0, len = keys.length; i < len; i++) {
        key = keys[i];
        opts.push(key + "=" + (elements[key].checked ? "1" : "0"));
    }

    elements.call.value = "{{{PLUGIN:video('" + elements.name.value + "', '"
        + opts.join('&') + "');}}}";
}


/*
 * Registers the initialization of the call builder.
 */
if (typeof window.addEventListener != "undefined") {
    window.addEventListener("load", video.initCallBuilder, false)
} else if (typeof window.attachEvent != "undefined") {
    window.attachEvent("onload", video.initCallBuilder);
}
