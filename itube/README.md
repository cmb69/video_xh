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

Note that creating image slideshows with background music is also supported.

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
  to process.  Then put `ffmpeg.exe`, `ffplay.exe` and `ffprobe.exe` in the `PATH`, or the
  folder where you extracted the ITube ZIP archive (i.e. right besides `itube.bat`).
* If you prefer working with drag&drop, consider to create links to `itube.bat`
  and `slideshow.bat` on your desktop.

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
some temporary files) are placed right inside this folder.  After the script
has determined some basic parameters, the video is played, so you can check
whether everything is okay.  Use the following keys to control playback:

| key               | function                         |
|-------------------|----------------------------------|
| q/ESC             | quit                             |
| p/SPACE           | pause                            |
| left/right        | seek backward/forward 10 seconds |
| down/up           | seek backward/forward 1 minute   |
| page down/page up | seek backward/forward 10 minutes |

After quitting, the script asks for the time where the screenshot (used as video
poster) should be taken.  You can find the time where you quit the video at the
left of the line above.  Enter this value and press `ENTER`, and the screenshot
is taken.  If you want to play the video again, because you didn't quit at the
desired time, just press `ENTER`.
Afterwards the script continues with the actual video encoding.
The script is showing its progress in a command window; you can watch it, or
just take a break since the video encoding process takes a long time.
If you close the window, processing will quit.

After the conversion is finished, you may want to manually inspect some of the
created files (i.e. play or view them); if you're contempt with their quality,
upload the whole folder (including the subfolders) to your webspace into the
`userfiles/media` folder of CMSimple_XH.  Then use Video_XH to show the video
on your website.

### Input Videos

While ITube will convert almost any video that you pass it, a couple of notes:

* Use meaningful (but not overly long) video filenames; that might be good for
  SEO, and also for users downloading the videos.

* Avoid special characters (like spaces and punctuation) in the video filenames;
  while that might work fine for ITube, it can cause portability issues, and is
  also somewhat confusing.

* Provide properly post-processed input videos to ITube.  The script does no
  post-processing on itself (except for quick and dirty deinterlacing, if needed,
  and the necessary downscaling), so depending on your input video, you should
  do this by other means (there are plenty of solutions available elsewhere,
  commercial and free).  It is strongly suggested that you keep the (post-processed)
  input videos, so you can re-encode them with newer versions of ITube later.

* Provide videos with a contempary resolution to ITube.  While the script accepts
  even 240p videos, you do not want to present such small videos to all of your
  visitors – some may not be able to watch bigger videos due to limited bandwidth
  or device power, but most usually are.  So use HD videos as input, or at least
  full PAL/NTSC SD content.  If you still have some smaller videos you consider
  worthwhile to show on your Website, consider to apply some sophisticated
  upscaling algorithms in the post-processing step (commercial and free solutions
  are available elsewhere).

* Usually you should not alter the framerate of the videos; either of 24fps,
  25fps, 30fps, 50fps and 60fps are fine; even smaller fps values may be okay.
  While there are a couple of ways to change the framerate during post-processing,
  the results are not unlikely to yield bad results when re-encoded with ITube.

* ITube only takes into account the main video and audio streams of the input
  videos and ignores other streams, because 1 video and 1 audio stream is the
  least common denominator regarding browser support.  If ITube picks up undesired
  streams, you need to re-mux the videos upfront.

### Image Slideshows

If you have still images, you can create slideshows with background music as video.

To do so, you need a folder with the images in JPEG format (`*.jpg`) and an audio
file (`audio.mp3`, `audio.wav`, or some other `audio.*`).  The duration of the
audio file determines the duration of the video file, and every image will be
shown for the respective fraction of that duration.  For instance, if there are
12 images and the audio duration is 1 minute, each image will be shown for 5
seconds.

Then you need to invoke `slideshow.bat`, either from the command line,
if you are comfortable working this way, e.g.

    slideshow C:\my_slideshow

Or you can just drag & drop the folder which contains the images and the audio
file on `slideshow.bat` (or a link to that file), which will then start the
creation of the slideshow video.

The script determines the most suitable video dimensions, so that the images need
minimal padding (some padding is needed if landscape and portrait images are mixed).
Afterwards the video containing the slideshow is created, and stored as lossless
MKV (e.g. for the example above it would be `C:\my_slideshow\my_slideshow.mkv`).
You can watch the created video (note that not all players may be able to play
the file; if in doubt, try [VLC](https://www.videolan.org/)).  If you are
satisfied with the video, you can [convert it with `itube.bat`](#usage).

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

Many thanks to the community at the [CMSimple_XH-Forum](https://www.cmsimpleforum.com/)
for tips, suggestions and testing.
Particularly I want to thank *manu* for presenting a slideshow video which triggered
the development of `slideshow.bat`.
