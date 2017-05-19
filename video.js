/**
 * @file      JavaScript of Video_XH.
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2012-2017 Christoph M. Becker (http://3-magi.net/)
 * @license   GNU GPLv3 (http://www.gnu.org/licenses/gpl-3.0.en.html)
 */

/*global  videojs */

/**
 * The plugin's namespace.
 *
 * @namespace
 */
var VIDEO = {};

/**
 * Initializes a video player.
 *
 * @param {string} id         A player's id attribute.
 * @param {Number} width      A width in pixels.
 * @param {Number} height     A height in pixels.
 * @param {string} resizeMode A resize mode specifier.
 */
VIDEO.initPlayer = function (id, width, height, resizeMode) {
    "use strict";

    var video, align;

    /**
     * Performs post-initialization of a video player, i.e. aligning the player
     * element and setting up a listener to resize the element to cater for
     * flexible templates.
     *
     * @param {string} alignment  An alignment specifier.
     */
    function postinit(alignment) {
        var playerDiv, player, width, aspectRatio;

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
            if (newWidth > width && resizeMode === "shrink") {
                newWidth = width;
            }
            player.width(newWidth);
            player.height(Math.round(newWidth / aspectRatio));
        }

        playerDiv = document.getElementById(id);
        player = videojs(id);

        align();

        if (resizeMode === "shrink" || resizeMode === "full") {
            width = player.width();
            aspectRatio = width / player.height();
            resize();
            if (typeof window.addEventListener !== "undefined") {
                window.addEventListener("resize", resize, false);
            } else if (typeof window.attachEvent !== "undefined") {
                window.attachEvent("onresize", resize);
            }
        }
    }

    video = document.getElementById(id);
    video.width = width;
    video.height = height;
    align = video.parentNode.style.textAlign;
    videojs(id, {}, function () {
        postinit(align);
    });
};

/**
 * Initializes the call builder.
 *
 * Sets up event handler properties to automatically update the plugin call when
 * the input changes and to select the whole plugin call when the user clicks in
 * its textarea.
 */
VIDEO.initCallBuilder = function () {
    "use strict";

    var elements, keys, i, key, element;

    /**
     * Builds the plugin call.
     */
    function buildPluginCall() {
        var opts, keys, key, i;

        opts = [];
        keys = ["width", "height"];
        for (i = 0; i < keys.length; i += 1) {
            key = keys[i];
            if (elements[key].value !== "") {
                opts.push(key + '=' + elements[key].value);
            }
        }
        opts.push("preload=" + elements.preload.value);
        opts.push("resize=" + elements.resize.value);
        keys = ["autoplay", "loop", "controls", "centered"];
        for (i = 0; i < keys.length; i += 1) {
            key = keys[i];
            opts.push(key + "=" + (elements[key].checked ? "1" : "0"));
        }
        elements.call.value = "{{{PLUGIN:video('" + elements.name.value + "', '"
            + opts.join('&') + "');}}}";
    }

    /**
     * Selects the content of an element.
     */
    function selectContent(event) {
        event = event || window.event;
        (event.target || event.srcElement).select();
    }

    elements = {};
    keys = [
        "name", "preload", "autoplay", "loop", "controls", "centered",
        "width", "height", "resize", "call"
    ];
    for (i = 0; i < keys.length; i += 1) {
        key = keys[i];
        element = document.getElementById("video_" + key);
        elements[key] = element;
        if (key !== "call") {
            element.onchange = buildPluginCall;
        } else {
            element.onclick = selectContent;
        }
    }
    buildPluginCall();
};
