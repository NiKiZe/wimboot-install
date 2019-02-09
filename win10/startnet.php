<?php
header("Content-Type: text/dos");
$SELF="http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']);
$mac=$_GET["mac"];
$pciid=$_GET["pciid"];
?>TITLE Bootstrap...
:: start extra cmd for manipulations
start cmd
>cleandisk.scrpt ECHO select disk 0
>>cleandisk.scrpt ECHO clean
>cleandisk.cmd ECHO diskpart /s cleandisk.scrpt
<?php
if ($pciid == "808615bb") {
    echo "echo Loading extra drivers ${pciid}...\r\n";
    echo "drvload e1d68x64.inf\r\n";
} ?>
:: wpeinit loads the shell
wpeinit
:: set high performance
powercfg /s 8c5e7fda-e8bf-4a96-9a85-a6e23a8c635c
:: Make sure network is up before continuing
wpeutil WaitForNetwork
:: also make sure we have dns
net start dnscache
ping 127.0.0.1 -n 2 >NUL

SET COUNT=0

:REDO
SET /A COUNT=%COUNT%+1

echo Mounting sources
@NET USE S: \\opk\floppy$ opk /USER:opk
IF NOT %ERRORLEVEL% == 0 (
:: wait 5 sec and retry
  ping 127.0.0.1 -n 5 >NUL
  IF %COUNT% LEQ 5 GOTO :REDO
  GOTO :FAIL
)
echo starting setup ...
@"s:\sources en\sources\setup.exe" /unattend:"s:\extboot en\autounattend.xml"
ECHO %ERRORLEVEL%
::IF %ERRORLEVEL% EQU 0 wpeutil reboot




GOTO :EOF

:FAIL
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

