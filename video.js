/**
 * Copyright 2012-2017 Christoph M. Becker
 *
 * This file is part of Video_XH.
 *
 * Video_XH is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Video_XH is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Video_XH.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * The plugin's namespace.
 *
 * @namespace
 */
var VIDEO = {};

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
        keys = ["autoplay", "loop", "controls"];
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
        "name", "preload", "autoplay", "loop", "controls",
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
