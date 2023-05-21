@echo off
call ".\build.cmd"
cd src/frontend
call ng serve
