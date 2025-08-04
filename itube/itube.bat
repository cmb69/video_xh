@echo off
setlocal
setlocal enableextensions
setlocal enabledelayedexpansion

rem quality (lower = better)
set crf=23

set ffmpeg=%~dp0ffmpeg.exe
if not exist %ffmpeg% for /f %%i in ('where ffmpeg') do set ffmpeg=%%i
if not exist %ffmpeg% echo [31mcannot find ffmpeg[0m
set ffplay=%~dp0ffplay.exe
if not exist %ffplay% for /f %%i in ('where ffplay') do set ffplay=%%i
if not exist %ffplay% echo [31mcannot find ffplay[0m
set ffprobe=%~dp0ffprobe.exe
if not exist %ffprobe% for /f %%i in ('where ffprobe') do set ffprobe=%%i
if not exist %ffprobe% echo [31mcannot find ffprobe[0m

set infile=%1
set folder=%~dpn1
set basename=%~n1
set filename=%~nx1

title itube %filename%

echo [32mprocessing %filename%[0m
if not exist "%folder%" md "%folder%"
call :set_deint
call :set_size_and_scale 720 && goto :size_set
call :set_size_and_scale 480 && goto :size_set
call :set_size_and_scale 360 && goto :size_set
call :set_size_and_scale 240 || (
    echo [31mvideos smaller than 240p are not supported[0m
    pause
    exit /b 1
)
:size_set
call :set_vfilter
call :play_video
call :set_bitrate || ( pause & exit /b 1 )
call :encode_mp4 || ( pause & exit /b 1 )
call :encode_webm || ( pause & exit /b 1 )
call :set_size_and_scale 144 || ( pause & exit /b 1 )
call :set_vfilter
call :encode_3gp || ( pause & exit /b 1 )
echo [32mfinished processing %filename%[0m
pause
goto :eof

:set_deint
    setlocal
    for /f "delims=," %%i in (
        '%ffprobe% -v error -select_streams v:0 -show_entries stream^=field_order -of csv^=p^=0 %infile%'
    ) do if %%i neq progressive set deint=yadif=1
    endlocal & set deint=%deint%
goto :eof

:set_size_and_scale
    setlocal
    set size=%1
    set width[1080]=1920
    set width[720]=1280
    set width[480]=854
    set width[360]=640
    set width[240]=426
    set width[144]=256
    rem for some reason this very line is necessary to prevent a script failure
    for /f "tokens=1-3 delims=," %%i in (
        '%ffprobe% -v error -select_streams v:0 -show_entries stream^=width^,height^,sample_aspect_ratio -of csv^=p^=0 %infile%'
    ) do (
        set width=%%i
        set height=%%j
        set sar=%%k
    )
    if %sar% neq N/A for /f "tokens=1-2 delims=:" %%i in ("%sar%") do set /a width=!width! * %%i / %%j
    if %width% lss !width[%size%]! if %height% lss %size% exit /b 1
    set /a wide=%width% * 9 / 16 / %height%
    if %wide%==0 (
        set scale=-2:%size%
    ) else (
        set scale=!width[%size%]!:-2
    )
    endlocal & set "size=%size%" & set scale=%scale%
goto :eof

:set_vfilter
    setlocal
    set vfilter=%deint%
    if "%vfilter%" neq "" set vfilter=%vfilter%,
    set vfilter=%vfilter%scale=%scale%,setsar=1
    endlocal & set vfilter=%vfilter%
goto :eof

:play_video
    setlocal
    set out=%folder%\%basename%.jpg
    echo [36mplaying video ...[0m
:play_again
    %ffplay% -hide_banner -loglevel error -stats -vf %vfilter% %infile%
    set /p "pos=[33mscreenshot at: [0m"
    if "%pos%" equ "" goto :play_again
    echo [36mtaking screenshot at %pos% ...[0m
    %ffmpeg% -y -hide_banner -loglevel error -stats -ss %pos% -i %infile% -vf %vfilter% -frames:v 1 %out%
    endlocal
goto :eof

:set_bitrate
    setlocal
    set out=%folder%\%basename%.mp4
    echo [36mdetermining %size%p video bitrate ...[0m
    %ffmpeg% -y -hide_banner -loglevel error -stats -i %infile%^
        -vf %vfilter% -pix_fmt yuv420p^
        -c:v libx264 -preset slow -tune film -profile high -crf %crf%^
        -an -sn^
        -f mp4 "%out%" || exit /b 1
    for /f %%i in ('%ffprobe% -v error -show_entries format^=bit_rate -of csv^=p^=0 "%out%"') do set bitrate=%%i
    del "%out%"
    endlocal & set bitrate=%bitrate%
goto :eof

:encode_mp4
    setlocal
    set out=%folder%\%basename%.mp4
    set logfile=%folder%\%basename%
    set /a bufsize=%bitrate% * 2
    echo [36manalyzing %size%p MP4 video ...[0m
    %ffmpeg% -y -hide_banner -loglevel error -stats -i %infile%^
        -vf %vfilter% -pix_fmt yuv420p^
        -c:v libx264 -preset slow -tune film -profile high -b:v %bitrate% -maxrate %bitrate% -bufsize %bufsize%^
        -an -sn^
        -pass 1 -passlogfile "%logfile%" -f null nul || exit /b 1
    echo [36mencoding %size%p MP4 video ...[0m
    %ffmpeg% -y -hide_banner -loglevel error -stats -i %infile%^
        -vf %vfilter% -pix_fmt yuv420p^
        -c:v libx264 -preset slow -tune film -profile high -b:v %bitrate% -maxrate %bitrate% -bufsize %bufsize%^
        -c:a aac -b:a 128k -ac 2 -sn^
        -pass 2 -passlogfile "%logfile%" -movflags +faststart -f mp4 "%out%" || exit /b 1
    del "%logfile%*.log*"
    endlocal
goto :eof

:encode_webm
    setlocal
    set out=%folder%\%basename%.webm
    set logfile=%folder%\%basename%
    set /a bufsize=%bitrate% * 2
    echo [36manalyzing %size%p WebM video ...[0m
    %ffmpeg% -y -hide_banner -loglevel error -stats -i %infile%^
        -vf %vfilter% -pix_fmt yuv420p^
        -c:v libvpx-vp9 -row-mt 1 -b:v %bitrate% -maxrate %bitrate% -bufsize %bufsize%^
        -an -sn^
        -pass 1 -passlogfile "%logfile%" -f null nul || exit /b 1
    echo [36mencoding %size%p WebM video ...[0m
    %ffmpeg% -y -hide_banner -loglevel error -stats -i %infile%^
        -vf %vfilter% -pix_fmt yuv420p^
        -c:v libvpx-vp9 -row-mt 1 -b:v %bitrate% -maxrate %bitrate% -bufsize %bufsize%^
        -c:a libopus -b:a 96k -ac 2 -sn^
        -pass 2 -passlogfile "%logfile%" -f webm "%out%" || exit /b 1
    del "%logfile%*.log"
    endlocal
goto :eof

:encode_3gp
    setlocal
    set out=%folder%\%basename%.3gp
    set logfile=%folder%\%basename%
    set vfilter=%vfilter%,fps=0.5*source_fps
    echo [36mdetermining 3GP video bitrate ...[0m
    %ffmpeg% -y -hide_banner -loglevel error -stats -i %infile%^
        -vf %vfilter% -pix_fmt yuv420p^
        -c:v libx264 -preset slow -tune film -profile baseline -crf %crf%^
        -an -sn^
        "%out%" || exit /b 1
    for /f %%i in ('%ffprobe% -v error -show_entries format^=bit_rate -of csv^=p^=0 "%out%"') do set bitrate=%%i
    del "%out%"
    set /a bufsize=%bitrate% * 2
    echo [36manalyzing 3GP video ...[0m
    %ffmpeg% -y -hide_banner -loglevel error -stats -i %infile%^
        -vf %vfilter% -pix_fmt yuv420p^
        -c:v libx264 -preset slow -tune film -profile baseline -b:v %bitrate% -maxrate %bitrate% -bufsize %bufsize%^
        -an -sn^
        -pass 1 -passlogfile "%logfile%" -f null nul || exit /b 1
    echo [36mencoding 3GP video ...[0m
    %ffmpeg% -y -hide_banner -loglevel error -stats -i %infile%^
        -vf %vfilter% -pix_fmt yuv420p^
        -c:v libx264 -preset slow -tune film -profile baseline -b:v %bitrate% -maxrate %bitrate% -bufsize %bufsize%^
        -c:a aac -b:a 48k -ac 1 -ar 24000 -sn^
        -pass 2 -passlogfile "%logfile%" -movflags +faststart "%out%" || exit /b 1
    del "%logfile%*.log*"
    endlocal
goto :eof
