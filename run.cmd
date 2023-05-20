@echo off
call ".\build.cmd"
cd docker
call docker compose up
