#!/bin/bash
#

while true
do
    sudo /opt/third-party/apple/pnProcess.php >> /var/log/pn.log
    sleep 60
done
