#!/bin/ksh
PATH=/sbin:/usr/sbin:/bin:/usr/bin:/usr/X11R6/bin:/usr/local/sbin:/usr/local/bin
rcctl stop openvpn findingsd heartbeatd inetd cron
supervisorctl stop all
backend target/destroy-instances
ifconfig tun0 down
