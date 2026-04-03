setup-first-time.ps1
echo"If execution policy blocks it, run:"
Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser
powershell .\setup-first-time.ps1