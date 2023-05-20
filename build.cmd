@echo off
md docker\server
cd src\frontend
call npm i
call ng build
cd ..\..\
xcopy /E /Y src\frontend\dist\sype\** docker\server
xcopy /E /Y src\api\** docker\server
