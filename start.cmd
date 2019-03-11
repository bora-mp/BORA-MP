@echo off
TITLE Minecraft: Bedrock Edition icin BORA-MP Sunucu Yazilimi
cd /d %~dp0

if exist bin\php\php.exe (
	set PHPRC=""
	set PHP_BINARY=bin\php\php.exe
) else (
	set PHP_BINARY=php
)

if exist src/bora/Bora.php (
	set POCKETMINE_FILE=src/bora/Bora.php
) else (
	echo BORA-MP.phar bulunamadi
	echo Guncel yazilimi buradan indirebilirsiniz: https://github.com/bora-mp/BORA-MP/releases
	pause
	exit 1
)

if exist bin\mintty.exe (
	start "" bin\mintty.exe -o Columns=88 -o Rows=32 -o AllowBlinking=0 -o FontQuality=3 -o Font="Consolas" -o FontHeight=10 -o CursorType=0 -o CursorBlinks=1 -h error -t "PocketMine-MP" -i bin/pocketmine.ico -w max %PHP_BINARY% %POCKETMINE_FILE% --enable-ansi %*
) else (
	REM pause on exitcode != 0 so the user can see what went wrong
	%PHP_BINARY% -c bin\php %POCKETMINE_FILE% %* || pause
)
