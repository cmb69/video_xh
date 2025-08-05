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
    elements.forEach(function (element) {
        if (element.id !== "video_call") {
            element.onchange = buildPluginCall;
        } else if (element instanceof HTMLTextAreaElement) {
            element.onclick = function selectContent() {
                element.select();
            };
            element.onchange = parsePluginCall;
        }
    });
    call = form.querySelector("textarea#video_call");
    buildPluginCall();

    function buildPluginCall() {
        var /** @type {string[]} */ opts,
            /** @type {HTMLSelectElement} */ name;
        opts = [];
        elements = form.querySelectorAll("input,textarea,select");
        elements.forEach(function (element) {
            if (element instanceof HTMLInputElement && element.type === "checkbox") {
                opts.push(element.id.substring(6) + "=" + (element.checked ? "1" : "0"));
            } else if (["video_name", "video_call"].indexOf(element.id) === -1) {
                opts.push(element.id.substring(6) + '=' + encodeURIComponent(element.value).replace("'", "%27"));
            }
        });
        name = form.querySelector("select#video_name");
        call.value = "{{{video('" + name.value + "','" + opts.join("&") + "')}}}";
    }

    function parsePluginCall() {
        var /** @type {string} */ text,
            /** @type {RegExpMatchArray} */ matches,
            /** @type {string} */ name,
            /** @type {HTMLSelectElement} */ select,
            /** @type {string[]} */ options,
            /** @type {string[]} */ pair,
            /** @type {HTMLInputElement} */ element;
        text = call.value;
        matches = text.match(/'([^'])*'/g);
        if (matches && matches.length === 2) {
            form.reset();
            call.value = text;
            name = matches[0].substring(1, matches[0].length - 1);
            select = document.querySelector("select#video_name");
            select.value = name;
            options = matches[1].substring(1, matches[1].length - 1).split("&");
            options.forEach(function (option) {
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
            });
            buildPluginCall();
        }
    }
}
