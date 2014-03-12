:: Running demosync from command line (cmd)
:: Testet on Windows XP with WAMP installed
:: Output is put in the logs folders

:: Add the following as a scheduled task:
:: C:\path\to\jm-booking\exchangesync\jmbooking_exchangesync.bat
::
:: Also add a file called "jmbooking_exchangesync_config.bat in the same folder (see example)

@echo off

call jmbooking_exchangesync_config.bat

For /f "tokens=1-3 delims=/. " %%a in ('date /t') do (set mydate=%%c-%%b-%%a)
For /f "tokens=1-2 delims=/:" %%a in ("%TIME%") do (set mytime=%%a%%b)

%phpcli_path% "%exchangesync_path%\index.php" > "%log_path%\log-%mydate%_%mytime%.txt"
