<?php
header("Content-Type: text/dos");
$SELF="http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']);
$mac=$_GET["mac"];
$pciid=$_GET["pciid"];
?>@TITLE Bootstrap...
:: start extra cmd for manipulations
@start cmd
>cleandisk.cmd ECHO (ECHO select disk 0 ^&^& ECHO clean) ^| diskpart
<?php
if ($pciid == "808615bb" || $pciid == "808615be") {
    echo "@echo Loading extra drivers ${pciid}...\r\n";
    echo "drvload e1d68x64.inf\r\n";
} else if (strpos($pciid, "1af4") === 0) {
    echo "@echo Loading extra drivers ${pciid}...\r\n";
    echo "drvload netkvm.inf\r\n";
    echo "drvload vioscsi.inf\r\n";
    echo "drvload viostor.inf\r\n";
} ?>
:: wpeinit loads the shell
wpeinit
:: set high performance
@powercfg /s 8c5e7fda-e8bf-4a96-9a85-a6e23a8c635c
:: make sure we have dns
start net start dnscache
:: Make sure network is up before continuing
wpeutil WaitForNetwork
@ping 127.0.0.1 -n 2 >NUL

@SET COUNT=0

:REDO
@SET /A COUNT=%COUNT%+1

@echo Mounting sources ...
@NET USE S: \\opk\floppy$ opk /USER:opk
IF %ERRORLEVEL% EQU 0 GOTO :NetOk
:: wait 5 sec and retry
@ping 127.0.0.1 -n 5 >NUL
IF %COUNT% LEQ 5 GOTO :REDO
GOTO :FAIL
:NetOk

@echo running setup.exe ...
@"s:\sources en\sources\setup.exe" /noreboot /unattend:"s:\extboot en\autounattend.xml"
@SET EL=%ERRORLEVEL%
@echo Returned with %EL%
IF %EL% NEQ 0 GOTO :FAIL

@echo run .NET 3.5 install in offline mode
@for %%a in (C D E F G H I J K L M N O P Q R S T U V W Y Z) do @if exist %%a:\$WINDOWS.~BT\ set DRIVE=%%a
@echo The Drive is: %DRIVE%
Dism /Image:%DRIVE%:\ /enable-feature /featurename:NetFx3 /All /Source:"s:\sources en\sources\sxs" /LimitAccess /NoRestart /LogLevel:4
@SET EL=%ERRORLEVEL%
@echo Returned with %EL%
IF %EL% NEQ 0 GOTO :FAIL
::pause
::wpeutil reboot
exit



GOTO :EOF

:FAIL
@echo.
@echo Failure
@echo Check the network!
@echo.
:: wait
@pause
:: rerun ourselves
%0
:: run cmd for shell
cmd

