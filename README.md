# Video_XH

Video_XH facilitates presenting videos on a CMSimple_XH site. It uses the
HTML5 video element wherever possible, and provides a a download link as
fallback otherwise. The supported video formats depend on the browser.

For each video additional <a href="https://schema.org/">schema.org</a>
compliant markup will be generated, so search engines can index and show the
videos in search results.

- [Requirements](#requirements)
- [Download](#download)
- [Installation](#installation)
- [Settings](#settings)
- [Usage](#usage)
  - [Examples](#examples)
  - [Subtitles](#subtitles)
- [Limitations](#limitations)
- [Troubleshooting](#troubleshooting)
- [License](#license)
- [Credits](#credits)

## Requirements

Video_XH requires CMSimple_XH ≥ 1.7.0 and PHP ≥ 7.1.0.

## Download

The [lastest release](https://github.com/cmb69/video_xh/releases/latest)
is available for download on Github.

## Installation

The installation is done as with many other CMSimple_XH plugins. See the
[CMSimple_XH Wiki](https://wiki.cmsimple-xh.org/?for-users/working-with-the-cms/plugins)
for further details.

1. Backup the data on your server.
1. Unzip the distribution on your computer.
1. Upload the whole directory `video/` to your server into
   the plugins directory of CMSimple_XH.
1. Set write permissions for the subdirectories `config/`,
   `css/` and `languages/`.
1. Navigate to `Plugins` → `Video` in the back-end to check
   if all requirements are fulfilled.

## Settings

The configuration of the plugin is done as with many other CMSimple_XH plugins
in the back-end of the Website. Go to `Plugins` → `Video`.

You can change the default settings of Video_XH under `Config`. Hints
for the options will be displayed when hovering over the help icons with
your mouse.

Localization is done under `Language`. You can translate the character
strings to your own language (if there is no appropriate language file
available), or customize them according to your needs.

The look of Video_XH can be customized under `Stylesheet`.

## Usage

As Video_XH provides no such possibility, you have to upload your video
files to the configured video folder via FTP, a file browser or
[Uploader_XH](https://github.com/cmb69/uploader_xh).
Supported video formats are MP4 (AVC Baseline@L3.0/AAC), WebM (VP8/Vorbis)
and OGG (Theora/Vorbis).
You can easily convert your videos to these formats with
[XMedia Recode](https://www.xmedia-recode.de/) (use the `HTML 5` profile).
To support all major browsers you should upload a
`*.mp4` *and* a `*.webm`/`*.ogv` version of the video.
Additionally you might want to upload a so-called
poster to the same folder, i.e. an image, that will be displayed before the
video is started, in JPEG format (`*.jpg`).

To display a video on a CMSimple_XH page insert the following plugin call in
the content:

    {{{video('%NAME%', '%OPTIONS%')}}}

The meaning of the placeholders:

- `%NAME%`:
  The name of the video file *without any extension*.
  Actually, this is a path relative to the configured video folder.

- `%OPTIONS%`:
  Options for each video as a string in the same format as a query string
  (everything after the question mark in an HTTP GET request).
  Options that are left out default to the respective values in the
  configuration of the plugin.
  You can use the `Call Builder` offered in the plugin administration,
  and optionally in a tab above the editor, to assemble the plugin call for
  you.
  After you are finished entering the desired values, just copy the
  content of the textarea at the bottom and paste it in a page.
  If you paste an existing plugin call into this textarea, the plugin call will
  be parsed and the controls updated, what allows for easy editing.

To display a video on all pages, insert the following in the template:

    <?=video('%NAME%', '%OPTIONS%')?>

### Examples

    {{{video('LotR')}}}

This will display the `LotR.webm`, `LotR.mp4` or `LotR.ogv` video in the
configured video folder with the configured width and height.
If `LotR.jpg` exists, it will be used as poster.

    {{{video('music/thriller', 'width=320&height=240')}}}

Will display the `thriller` video in the subfolder `music/`,
with a size of 320px × 240px.
Note that scaling the video for playback might not be the best option.

    {{{video('banner', 'autoplay&loop&controls=0')}}}

Will autoplay the `banner` video starting over when finished
without showing any controls.
This is particularly useful for banner or background videos,
but may be annoying for your visitors.

### Subtitles

On supporting browsers, the video players can optionally display subtitles.
The subtitles have to be in separate files (not muxed into the container),
where [WebVTT](https://developer.mozilla.org/en-US/docs/Web/API/WebVTT_API)
is the recommended file format, though SRT might also work.
Just place a respective file `*.vtt` (or `*.srt`) beside the video files,
and visitors are able to activate the subtitles if they like.

For multilingual websites you will want to have subtitles in multiple languages.
Just name them `*_LANG.vtt` (or `*_LANG.srt`), e.g. `video_en.vtt` (or
`video_de.vtt`.
The player will offer only the subtitles of the currently selected language.

## Limitations

The call builder is only available under a contempary browser; for instance,
IE 9 is not supported.

## Troubleshooting

In case the video does not play *at all*, the video might be served
with an unappropriate MIME type.
If this is the case, you probably can configure your Apache Webserver
with the following lines in `.htaccess`:

    AddType video/webm .webm
    AddType video/mp4 .mp4
    AddType video/ogg .ogv

If you use a Webserver other than Apache, or that configuration does not work,
ask your hosting provider or server admin to do the necessary configuration.

If the video cannot be played *immediately*,
it is probably due to an encoding or muxing error.
It seems many MP4 muxers put the `moov` atom at the end of the video,
what requires the player to download the complete file,
before the playback starts.
It is best to try to remux the video with
[Yamb](http://yamb.unite-video.com/) (for MP4) or
[MKVToolNix](https://mkvtoolnix.download/) (for WebM).

Report bugs and ask for support either on
[Github](https://github.com/cmb69/video_xh/issues)
or in the [CMSimple_XH Forum](https://cmsimpleforum.com/).

## License

Video_XH is free software: you can redistribute it and/or modify it
under the terms of the GNU General Public License as published
by the Free Software Foundation, either version 3 of the License,
or (at your option) any later version.

Video_XH is distributed in the hope that it will be useful,
but without any warranty; without even the implied warranty of merchantibility
or fitness for a particular purpose.
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Video_XH. If not, see https://www.gnu.org/licenses/.

© 2012-2023 Christoph M. Becker

Danish translation © 2012 Jens Maegard  
Czech translation © 2012 Josef Němec  
Slovak translation © 2012 Dr. Martin Sereday

## Credits

The plugin icon is designed by [Alessandro Rei](http://www.mentalrey.it/).
Many thanks for releasing it under GPL.

Many thanks to the community at the [CMSimple_XH-Forum](https://www.cmsimpleforum.com/)
for tips, suggestions and testing.
Particularly I want to thank *bca* for being the first beta tester,
for contributing the *tube* and *tube2* skins
(for the old video player which has now been superseded)
and informing me about Video.js 4,
and *Ulrich* and *Holger* who inspired the subtitle support.

And last but not least many thanks to [Peter Harteg](http://www.harteg.dk/),
the “father” of CMSimple, and all developers of [CMSimple_XH](https://www.cmsimple-xh.org/)
without whom this amazing CMS would not exist.
