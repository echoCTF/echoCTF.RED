#!/bin/ksh
MEMD="{{db.host}}"
DBHOST="127.0.0.1"
DBUSER="{{db.user}}"
DBPASS="{{db.pass}}"
NCOPTS="-N"
exec 1>>/tmp/verify-cn-std.log
exec 2>&1

echo "------------" >>/tmp/verify-cn.log
date >> /tmp/verify-cn.log
echo "Arguments $*">>/tmp/verify-cn.log
echo "Checking address ${X509_0_emailAddress} with serial ${tls_serial_0}" >> /tmp/verify-cn.log
player_id=$(mysql -h ${DBHOST} -u"${DBUSER}" -p"${DBPASS}" echoCTF -NBe "SELECT player_id FROM player_ssl WHERE serial=${tls_serial_0}")
if [ "${player_id}x" == "x" ]; then
  exit 1
fi
exit 0
