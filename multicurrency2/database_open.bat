for /f "tokens=2 delims==" %%a in ('wmic OS Get localdatetime /value') do set "dt=%%a"
set "YY=%dt:~2,2%" & set "YYYY=%dt:~0,4%" & set "MM=%dt:~4,2%" & set "DD=%dt:~6,2%"
set "HH=%dt:~8,2%" & set "Min=%dt:~10,2%" & set "Sec=%dt:~12,2%"
set "fullstamp=%YYYY%-%MM%-%DD%_%HH%-%Min%-%Sec%"
mysqldump --opt -hlocalhost -uadmin_icai14marketcap -pReset930$$ admin_icai14marketcap > C:/xampp/htdocs/marketcap/files/db-backup/opening_backup_admin_icai14_%fullstamp%.sql