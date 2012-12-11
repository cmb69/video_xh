// $Id$

/**
 * Back-end JS of Video_XH.
 *
 * Copyright (c) 2012 Christoph M. Becker (see license.txt)
 */


var Video = {

    fields: {},


    init: function() {
        var keys, i, key;

        keys = ['name', 'preload', 'autoplay', 'loop', 'controls',
            'width', 'height', "resize", 'call'];
        for (i = 0; i < keys.length; i++) {
            key = keys[i];
            this.fields[key] = document.getElementById('video_' + key);
            if (key != 'call') {
                this.fields[key].onchange = this.buildPluginCall;
            }
        }
        this.buildPluginCall();
    },


    buildPluginCall: function() {
        var flds = Video.fields, opts = [], keys, key, i;

        keys = ['width', 'height'];
        for (i = 0; i < keys.length; i++) {
            key = keys[i];
            if (flds[key].value != '') {
                opts.push(key + '=' + flds[key].value);
            }
        }

        opts.push('preload=' + flds['preload'].value);
        opts.push('resize=' + flds["resize"].value);

        keys = ['autoplay', 'loop', 'controls'];
        for (i = 0; i < keys.length; i++) {
            key = keys[i];
            opts.push(key + '=' + (flds[key].checked ? '1' : '0'));
        }

        flds.call.value = '{{{PLUGIN:video(\'' + flds.name.value + '\', \''
            + opts.join('&') + '\');}}}';
    }

}

Video.init();
