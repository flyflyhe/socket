#!/bin/bash
source /etc/profile
today=$(date +%Y-%m-%d)
/usr/bin/scp -P 2208 -v root@:/tmp/bak_trusted_list12.php /home/fly/bak/12shouxin.php.$today >> /home/fly/bak/scp.log


#### crontab编辑 crontab 与 scp自动备份文件
#crontab -e
#SHELL=/bin/bash
#PATH=/sbin:/bin:/usr/sbin:/usr/bin
#MAILTO=fly
#HOME=/home/fly
#00 12 * * * sh /home/fly/flywww/socket/shell/scp.sh >> /home/fly/bak/scp.log
