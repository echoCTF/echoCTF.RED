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
C=$(echo "get sysconfig:dn_countryName"|nc ${NCOPTS} ${MEMD} 11211 |egrep -v "(VALUE|END)"            | tr -d $'\r')
L=$(echo "get sysconfig:dn_localityName"|nc ${NCOPTS} ${MEMD} 11211 |egrep -v "(VALUE|END)"           | tr -d $'\r')
ST=$(echo "get sysconfig:dn_stateOrProvinceName"|nc ${NCOPTS} ${MEMD} 11211 |egrep -v "(VALUE|END)"   | tr -d $'\r')
O=$(echo "get sysconfig:dn_organizationName"|nc ${NCOPTS} ${MEMD} 11211 |egrep -v "(VALUE|END)"       | tr -d $'\r')
OU=$(echo "get sysconfig:dn_organizationalUnitName"|nc ${NCOPTS} ${MEMD} 11211 |egrep -v "(VALUE|END)"| tr -d $'\r')
CN=$(echo "get sysconfig:dn_commonName"|nc ${NCOPTS} ${MEMD} 11211 |egrep -v "(VALUE|END)"            | tr -d $'\r')

ourSubject=$(echo -e "C=${C}, ST=${ST}, L=${L}, O=${O}, OU=${OU}, CN=${CN}")
if [ "$ourSubject" == "$2" ]; then
  echo "Out CA">>/tmp/verify-cn.log
  exit 0
fi

echo -n "Checking address ${X509_0_emailAddress} with serial ${tls_serial_0}" >> /tmp/verify-cn.log
typeset -i player_id=$(mysql -h ${DBHOST} -u"${DBUSER}" -p"${DBPASS}" echoCTF -NBe "SELECT player_id FROM player_ssl WHERE serial='${tls_serial_0}'")
if [ ${player_id} -eq 0 ]; then
  echo "... not found">>/tmp/verify-cn.log
  exit 1
fi
echo " found">>/tmp/verify-cn.log
exit 0
