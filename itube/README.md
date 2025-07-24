# ITube

ITube is a script for processing video files so they can be easily used by
[Video_XH](https://github.com/cmb69/video_xh).
The script is meant for offline processing, since direct integration of its
features into Video_XH is practically impossible.

Of course, there is no *need* to use ITube for processing your videos; it is
merely meant as a convenient way to prepare suitable videos for the Web,
similar to what is done by YouTube, Vimeo etc. when you upload videos to those
platforms.  The main problem solved by ITube is to create a suitable set of
video files which can be played on different browsers and in different
environments.  E.g. some years ago, it was mandatory to have Ogg video files
for some browsers, but nowadays browsers are in the process of completely
dropping support for that container format.  Looking at the details of what
codecs are actually supported, shows a considerable complexity, and you may not
want to learn all that.  So just keep your original (possibly post-processed)
videos, and re-encode them from time to time with newer versions of ITube to
cater to an ever evolving Web.

## Requirements

The script runs only under Windows operating systems (or emulation layers, such
as [WineHQ](https://www.winehq.org/)), and requires [ffmpeg](https://ffmpeg.org/).

## Download

The [lastest release](https://github.com/cmb69/video_xh/releases/latest)
is available for download on Github.

## Installation

* Extract the ZIP archive somewhere on your computer.
* If you have [ffmpeg](https://ffmpeg.org/) already installed, make sure it is
  in the `PATH`.  Otherwise [download ffmpeg](https://www.gyan.dev/ffmpeg/builds/);
  the essential build is usually sufficient unless you have some uncommon videos
  to process.  Then put `ffmpeg.exe` and `ffprobe.exe` in the `PATH`, or the
  folder where you extracted the ITube ZIP archive (i.e. right besides `itube.bat`).
* If you prefer working with drag&drop, consider to create a link to `itube.bat`
  on your desktop.

## Settings

At the top of `itube.bat` is a configuration section which allows you to tune some
of the parameters, but the defaults are supposed to be good, so usually no
configuration is necessary.

## Usage

`itube.bat` is a command line script, so you can use it from the command line,
if you are comfortable working this way, e.g.

    itube C:\my_video.mp4

Otherwise you can just drag & drop the video file to be converted on `itube.bat`
(or a link to that file), which will then start the conversion.

Either way, the script creates a subfolder with the basename of the video file
(e.g. for the example above, `C:\my_video\`), and all created files (including
some temporary files) are placed right inside this folder.  The script is
showing its progress in a command window; you can watch it, or just take a break
since the video transcoding process takes a long time.  If you close the window,
transcoding will quit.

After the conversion is finished, you may want to manually inspect some of the
created files (i.e. play or view them); if you're contempt with their quality,
upload the whole folder to your webspace into the `userfiles/media` folder of
CMSimple_XH.  Then use Video_XH to show the video on your website.

## Troubleshooting

Report bugs and ask for support either on
[Github](https://github.com/cmb69/video_xh/issues)
or in the [CMSimple_XH Forum](https://cmsimpleforum.com/).

## License

ITube is free software: you can redistribute it and/or modify it
under the terms of the GNU General Public License as published
by the Free Software Foundation, either version 3 of the License,
or (at your option) any later version.

ITube is distributed in the hope that it will be useful,
but without any warranty; without even the implied warranty of merchantibility
or fitness for a particular purpose.
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with ITube. If not, see https://www.gnu.org/licenses/.

Copyright © Christoph M. Becker

## Credits

ITube is powered by [ffmpeg](https://ffmpeg.org/).
Many thanks for releasing this powerful multimedia framework as OpenSource software!
