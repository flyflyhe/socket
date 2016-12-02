#!/bin/bash
source /etc/profile
today=$(date +%Y-%m-%d)
/usr/bin/scp -P 端口 -v root@127.0.0.1:/tmp/bak_trusted_list12.php /home/fly/bak/12shouxin.php.$today >> /home/fly/bak/scp.log


#### crontab编辑 crontab 与 scp自动备份文件
#crontab -e
#SHELL=/bin/bash
#PATH=/sbin:/bin:/usr/sbin:/usr/bin
#MAILTO=fly
#HOME=/home/fly
#00 12 * * * sh /home/fly/flywww/socket/shell/scp.sh >> /home/fly/bak/scp.log
