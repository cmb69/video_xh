/**
 * Copyright (c) Christoph M. Becker
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

// jshint browser:true,esversion:5,latedef:nofunc,strict:implied
// @ts-check

/**
 * @typedef {HTMLInputElement|HTMLTextAreaElement|HTMLSelectElement} FormControl
 */

if (document.getElementById("video_call_builder")) {
    initCallBuilder();
}

function initCallBuilder() {
    var template = /** @type {HTMLScriptElement} */ (
        document.querySelector("script#video_call_builder")
    );
    template.insertAdjacentHTML("beforebegin", template.text);
    var form = /** @type {HTMLFormElement} */ (document.querySelector("form#video_call_builder"));
    var elements = /** @type {NodeListOf<FormControl>} */ (
        form.querySelectorAll("input,textarea,select")
    );
    elements.forEach(function (element) {
        if (element instanceof HTMLTextAreaElement && element.id === "video_call") {
            element.onclick = element.select.bind(element);
            element.onchange = parsePluginCall;
        } else {
            element.onchange = buildPluginCall;
        }
    });
    var call = /** @type {HTMLTextAreaElement} */ (form.querySelector("textarea#video_call"));
    buildPluginCall();

    function buildPluginCall() {
        var /** @type {string[]} */ opts = [];
        elements.forEach(function (element) {
            var /** @type string */ value;
            if (element instanceof HTMLInputElement && element.type === "checkbox") {
                value = element.checked ? "1" : "0";
                opts.push(element.id.substring("video_".length) + "=" + value);
            } else if (["video_name", "video_call"].indexOf(element.id) === -1) {
                value = encodeURIComponent(element.value).replace("'", "%27");
                opts.push(element.id.substring("video_".length) + "=" + value);
            }
        });
        var name = /** @type {HTMLSelectElement} */ (form.querySelector("select#video_name"));
        call.value = "{{{video('" + name.value + "','" + opts.join("&") + "')}}}";
    }

    function parsePluginCall() {
        var text = call.value;
        var matches = /** @type {RegExpMatchArray} */ (text.match(/'([^'])*'/g));
        if (matches && matches.length === 2) {
            form.reset();
            call.value = text;
            var name = matches[0].substring(1, matches[0].length - 1);
            var select = /** @type {HTMLSelectElement} */ (
                document.querySelector("select#video_name")
            );
            select.value = name;
            var options = matches[1].substring(1, matches[1].length - 1).split("&");
            options.forEach(function (option) {
                var pair = option.split("=");
                if (pair.length === 2) {
                    var element = /** @type {FormControl} */ (
                        document.querySelector("#video_" + pair[0])
                    );
                    if (element) {
                        if (element instanceof HTMLInputElement && element.type === "checkbox") {
                            element.checked = pair[1] === "0" ? false : !!pair[1];
                        } else {
                            element.value = decodeURIComponent(pair[1]);
                        }
                    }
                }
            });
            buildPluginCall();
        }
    }
}
