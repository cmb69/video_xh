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

/* jshint browser:true,strict:implied */

if (document.getElementById("video_call_builder")) {
    initCallBuilder();
}

function initCallBuilder() {
    var /** @type {HTMLScriptElement} */ template,
        /** @type {HTMLFormElement} */ form,
        /** @type {NodeListOf<HTMLInputElement|HTMLTextAreaElement|HTMLSelectElement>} */ elements,
        /** @type {HTMLTextAreaElement} */ call;

    template = document.querySelector("script#video_call_builder");
    template.insertAdjacentHTML("beforebegin", template.text);
    form = document.querySelector("form#video_call_builder");
    elements = form.querySelectorAll("input,textarea,select");
    elements.forEach(initFormElement);
    call = form.querySelector("textarea#video_call");
    buildPluginCall();

    /** @param {HTMLInputElement|HTMLTextAreaElement|HTMLSelectElement} element */
    function initFormElement(element) {
        if (element instanceof HTMLTextAreaElement && element.id === "video_call") {
            element.onclick = element.select.bind(element);
            element.onchange = parsePluginCall;
        } else {
            element.onchange = buildPluginCall;
        }
    }

    function buildPluginCall() {
        var /** @type {string[]} */ opts, /** @type {HTMLSelectElement} */ name;

        opts = [];
        elements.forEach(buildOption);
        name = form.querySelector("select#video_name");
        call.value = "{{{video('" + name.value + "','" + opts.join("&") + "')}}}";

        /** @param {HTMLInputElement|HTMLTextAreaElement|HTMLSelectElement} element */
        function buildOption(element) {
            if (element instanceof HTMLInputElement && element.type === "checkbox") {
                opts.push(
                    element.id.substring("video_".length) + "=" + (element.checked ? "1" : "0")
                );
            } else if (["video_name", "video_call"].indexOf(element.id) === -1) {
                opts.push(
                    element.id.substring("video_".length) +
                        "=" +
                        encodeURIComponent(element.value).replace("'", "%27")
                );
            }
        }
    }

    function parsePluginCall() {
        var /** @type {string} */ text,
            /** @type {RegExpMatchArray} */ matches,
            /** @type {string} */ name,
            /** @type {HTMLSelectElement} */ select,
            /** @type {string[]} */ options;

        text = call.value;
        matches = text.match(/'([^'])*'/g);
        if (matches && matches.length === 2) {
            form.reset();
            call.value = text;
            name = matches[0].substring(1, matches[0].length - 1);
            select = document.querySelector("select#video_name");
            select.value = name;
            options = matches[1].substring(1, matches[1].length - 1).split("&");
            options.forEach(parseOption);
            buildPluginCall();
        }

        /** @param {string} option */
        function parseOption(option) {
            var /** @type {string[]} */ pair, /** @type {HTMLInputElement} */ element;

            pair = option.split("=");
            if (pair.length === 2) {
                element = document.querySelector("#video_" + pair[0]);
                if (element) {
                    if (element.type === "checkbox") {
                        element.checked = pair[1] === "0" ? false : !!pair[1];
                    } else {
                        element.value = decodeURIComponent(pair[1]);
                    }
                }
            }
        }
    }
}
