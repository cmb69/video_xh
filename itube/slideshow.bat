@echo off
setlocal
setlocal enableextensions
setlocal enabledelayedexpansion

set ffmpeg=%~dp0ffmpeg.exe
if not exist %ffmpeg% for /f %%i in ('where ffmpeg') do set ffmpeg=%%i
if not exist %ffmpeg% echo [31mcannot find ffmpeg[0m
set ffprobe=%~dp0ffprobe.exe
if not exist %ffprobe% for /f %%i in ('where ffprobe') do set ffprobe=%%i
if not exist %ffprobe% echo [31mcannot find ffprobe[0m

set name=%~n1

chcp 65001

title slideshow
echo [32mprocessing %~n1[0m
pushd %1

for %%i in (audio.*) do if not defined audio set audio=%%i
if not defined audio (
    echo [31mcannot find audio file[0m
    pause
    exit /b 1
)

echo [36mdetermine audio duration ...[0m
for /f "delims=. tokens=1-2" %%i in (
    '%ffprobe% -v error -select_streams a:0 -show_entries stream^=duration -of csv^=p^=0 "%audio%"'
) do (
    set ms=%%j000
    set duration=%%i!ms:~0,3!
)
echo %duration% ms

echo [36mdetermine video resolution ...[0m
set count=0
set sum=0
set min=1000
set max=1000
for %%i in (*.jpg) do (
    set /a count+=1
    for /f "delims=, tokens=1-2" %%j in (
        '%ffprobe% -v error -select_streams v:0 -show_entries stream^=width^,height -of csv^=p^=0 %%i'
    ) do (
        set /a ratio=%%j000/%%k
        set /a sum+=!ratio!
        if !ratio! lss !min! set min=!ratio!
        if !ratio! gtr !max! set max=!ratio!
        <nul set /p =.
    )
)
set /a average=%sum%/%count%
if %average% geq 1000 (
    set height=720
    set /a width=!height!*%max%/1000
    set /a mod=!width!%%2
    if !mod! neq 0 set /a width+=1
) else (
    set width=480
    set /a height=!width!*1000/%min%
    set /a mod=!height!%%2
    if !mod! neq 0 set /a height+=1
)
echo  %width%x%height%

set /a duration/=%count%
set duration=%duration:~0,-3%.%duration:~-3%
type nul > playlist.tmp.txt
for %%i in (*.jpg) do (
    (
        echo file '%%~nxi'
        echo duration %duration%
    ) >> playlist.tmp.txt
    set last=%%~nxi
)
@REM echo file '%last%'>> playlist.tmp.txt

echo [36mencode lossless video ...[0m
%ffmpeg% -y -hide_banner -loglevel error -stats -f concat -safe 0 -i playlist.tmp.txt -i %audio%^
    -vf "scale=%width%:%height%:force_original_aspect_ratio=decrease:eval=frame,pad=%width%:%height%:-1:-1:color=black,scale=out_range=tv"^
    -c:v libvpx-vp9 -lossless 1 -fps_mode cfr -r 24 -c:a copy "%name%.mkv"

del playlist.tmp.txt

popd
echo [32mfinished processing %filename%[0m
pause
