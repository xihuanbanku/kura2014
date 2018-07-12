#!bin/sh
cd /home/p-mon/pmon.jp/public_html/kura2014/crontab 
 curl "http://pmon.jp/kura2014/crontab/jxc_report_storage.php?flag=insert" >>crontab.log
 curl "http://pmon.jp/kura2014/crontab/jxc_analyze.php?flag=insert" >>crontab.log
 curl "http://pmon.jp/kura2014/crontab/jxc_salary.php?flag=insert" >>crontab.log
date >> crontab.log
