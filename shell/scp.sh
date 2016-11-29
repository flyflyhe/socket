#!/bin/bash
today=$(date +%Y-%m-%d)
scp c.org:/tmp/bak_trusted_list12.php /home/fly/bak/12shouxin.php.$today >> /home/fly/trusted_cron.log           
