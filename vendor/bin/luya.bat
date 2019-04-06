@ECHO OFF
setlocal DISABLEDELAYEDEXPANSION
SET BIN_TARGET=%~dp0/../luyadev/luya-core/bin/luya
php "%BIN_TARGET%" %*
