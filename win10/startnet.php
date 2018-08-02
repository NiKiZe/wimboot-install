<?php
header("Content-Type: text/dos");
$SELF="http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']);
?>TITLE Bootstrap...
:: wpeinit loads the shell
wpeinit
:: Make sure network is up before continuing
wpeutil WaitForNetwork
:: also make sure we have dns
net start dnscache
ping 127.0.0.1 -n 2 >NUL

SET COUNT=0

:REDO
SET /A COUNT=%COUNT%+1

NET USE S: \\opk\floppy$ opk /USER opk
IF NOT %ERRORLEVEL% == 0 (
:: wait 5 sec and retry
  ping 127.0.0.1 -n 5 >NUL
  IF %COUNT% LEQ 5 GOTO :REDO
)

echo.
echo Could not fetch bootstrap script
echo Check the network!
echo.
:: wait
pause
:: rerun ourselves
%0
:: run cmd for shell
cmd

