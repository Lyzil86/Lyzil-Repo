@echo off
title SIWASANG Online Tunnel (ngrok)
color 0B
echo ===================================================
echo     SIWASANG KOTA BOGOR - ONLINE PERMANENT TUNNEL
echo ===================================================
echo.
echo Link Publik Permanen:
echo https://bounding-credit-trustee.ngrok-free.dev
echo.
echo Sedang menjalankan tunnel... Jangan tutup jendela ini!
echo ===================================================
echo.

"C:\Users\user\.config\herd\bin\ngrok.exe" http 443 --url=bounding-credit-trustee.ngrok-free.dev --host-header="siwasang-web.test"
pause
