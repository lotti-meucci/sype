@echo off
call ".\build.bat"
cd docker
call docker compose up
