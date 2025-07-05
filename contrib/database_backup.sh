#!/bin/ksh
MYSQLDUMP=/usr/local/bin/mariadb-dump
${MYSQLDUMP} -YKER --add-drop-table --hex-blob --triggers --tz-utc echoCTF | gzip -9 -f -o "/altroot/echoCTF-full-$(date +%Y%m%d).sql.gz"