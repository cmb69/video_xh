@echo off
setlocal
setlocal enableextensions
setlocal enabledelayedexpansion

rem quality (lower = better)
set crf=23

set ffmpeg=%~dp0ffmpeg.exe
if not exist %ffmpeg% for /f %%i in ('where ffmpeg') do set ffmpeg=%%i
if not exist %ffmpeg% echo [31mcannot find ffmpeg[0m
set ffprobe=%~dp0ffprobe.exe
if not exist %ffprobe% for /f %%i in ('where ffprobe') do set ffprobe=%%i
if not exist %ffprobe% echo [31mcannot find ffprobe[0m

set infile=%1
set folder=%~dpn1
set basename=%~n1
set inifile=%folder%\%basename%.ini
set filename=%~nx1

title itube %filename%

echo [32mprocessing %filename%[0m
if not exist "%folder%" md "%folder%"
type nul > "%inifile%"
call :set_size_and_scale 720 || ( pause & exit /b 1 )
call :set_bitrate || ( pause & exit /b 1 )
call :encode_mp4 || ( pause & exit /b 1 )
call :encode_webm || ( pause & exit /b 1 )
echo [32mfinished processing %filename%[0m
pause
goto :eof

:set_size_and_scale
    setlocal
    set size=%1
    set width[720]=1280
    set width[240]=426
    for /f "tokens=1-3 delims=," %%i in (
        '%ffprobe% -v error -select_streams v:0 -show_entries stream^=width^,height^,sample_aspect_ratio -of csv^=p^=0 %infile%'
    ) do (
        set width=%%i
        set height=%%j
        set sar=%%k
    )
    if %sar% neq N/A for /f "tokens=1-2 delims=:" %%i in ("%sar%") do set /a width=!width! * %%i / %%j
    set /a wide=%width% * 9 / 16 / %height%
    if %wide%==0 (
        set scale=-2:%size%
    ) else (
        set scale=!width[%size%]!:-2
    )
    endlocal & set "size=%size%" & set scale=%scale%
goto :eof

:set_bitrate
    setlocal
    set out=%folder%\%basename%.mp4
    echo [36mdetermining %size%p video bitrate ...[0m
    %ffmpeg% -y -hide_banner -loglevel error -stats -i %infile%^
        -vf scale=%scale%,setsar=1 -pix_fmt yuv420p^
        -c:v libx264 -preset slow -tune film -profile main -crf %crf%^
        -an -sn^
        -f mp4 "%out%" || exit /b 1
    for /f %%i in ('%ffprobe% -v error -show_entries format^=bit_rate -of csv^=p^=0 "%out%"') do set bitrate=%%i
    del "%out%"
    endlocal & set bitrate=%bitrate%
goto :eof

:encode_mp4
    setlocal
    set out=%folder%\%basename%_%size%p.mp4
    set logfile=%folder%\%basename%
    set /a bufsize=%bitrate% * 2
    echo [36manalyzing %size%p MP4 video ...[0m
    %ffmpeg% -y -hide_banner -loglevel error -stats -i %infile%^
        -vf scale=%scale%,setsar=1 -pix_fmt yuv420p^
        -c:v libx264 -preset slow -tune film -profile main -b:v %bitrate% -maxrate %bitrate% -bufsize %bufsize%^
        -an -sn^
        -pass 1 -passlogfile "%logfile%" -f null nul || exit /b 1
    echo [36mencoding %size%p MP4 video ...[0m
    %ffmpeg% -y -hide_banner -loglevel error -stats -i %infile%^
        -vf scale=%scale%,setsar=1 -pix_fmt yuv420p^
        -c:v libx264 -preset slow -tune film -profile main -b:v %bitrate% -maxrate %bitrate% -bufsize %bufsize%^
        -c:a aac -b:a 128k -ac 2 -sn^
        -pass 2 -passlogfile "%logfile%" -movflags +faststart -f mp4 "%out%" || exit /b 1
    del "%logfile%*.log*"
    call :set_level "%out%"
    (
        echo [%basename%_%size%p.mp4]
        echo codecs=avc1.4d00%level%,mp4a.40.2
        echo:
    ) >> "%inifile%"
    endlocal
goto :eof

:encode_webm
    setlocal
    set out=%folder%\%basename%_%size%p.webm
    set logfile=%folder%\%basename%
    set /a bufsize=%bitrate% * 2
    echo [36manalyzing %size%p WebM video ...[0m
    %ffmpeg% -y -hide_banner -loglevel error -stats -i %infile%^
        -vf scale=%scale%,setsar=1 -pix_fmt yuv420p^
        -c:v libvpx -row-mt 1 -b:v %bitrate% -maxrate %bitrate% -bufsize %bufsize%^
        -an -sn^
        -pass 1 -passlogfile "%logfile%" -f null nul || exit /b 1
    echo [36mencoding %size%p WebM video ...[0m
    %ffmpeg% -y -hide_banner -loglevel error -stats -i %infile%^
        -vf scale=%scale%,setsar=1 -pix_fmt yuv420p^
        -c:v libvpx -row-mt 1 -b:v %bitrate% -maxrate %bitrate% -bufsize %bufsize%^
        -c:a libopus -b:a 96k -ac 2 -sn^
        -pass 2 -passlogfile "%logfile%" -f webm "%out%" || exit /b 1
    del "%logfile%*.log"
    (
        echo [%basename%_%size%p.webm]
        echo codecs=vp8,opus
        echo:
    ) >> "%inifile%"
    endlocal
goto :eof

:set_level
    setlocal
    set file=%1
    for /f "delims=," %%i in (
        '%ffprobe% -v error -select_streams v:0 -show_entries stream^=level -of csv^=p^=0 %file%'
    ) do set level=%%i
    set digits=0123456789abcdef
    set /a high=%level% / 16
    set /a low=%level% %% 16
    set hex=!digits:~%high%,1!!digits:~%low%,1!
    endlocal & set level=%hex%
goto :eof
