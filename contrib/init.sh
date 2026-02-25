#!/bin/bash
#
# Simple script to initiallize a development installation
#

: ${DATABASE:=echoCTF}

# Wait in seconds after each step
: ${WAIT_AFTER_STEP:=0}

echo "Using database: ${DATABASE}"


function settings() {
  composer install -d backend
  composer install -d frontend
  cp backend/config/cache-local.php backend/config/cache.php
  sed -e "s/echoCTF/${DATABASE}/g" backend/config/db-sample.php > backend/config/db.php
  cp backend/config/validationKey-local.php backend/config/validationKey.php
  sed -e "s/echoCTF/${DATABASE}/g" frontend/config/db-local.php > frontend/config/db.php
  cp frontend/config/memcached-local.php frontend/config/cache.php
  cp frontend/config/validationKey-local.php frontend/config/validationKey.php
  mkdir -p frontend/web/images/avatars/ frontend/web/images/avatars/team/
}


function services() {
  sudo rm -f /tmp/event_finished
  sudo service memcached restart
  sudo service mysql restart
  (sed -e "s/echoCTF/${DATABASE}/" contrib/mysql-init.sql | mysql ${DATABASE}  > /dev/null) || echo "No function exists"
  mysql ${DATABASE} -e "SET GLOBAL EVENT_SCHEDULER=ON"
}

function sql() {
  mysqladmin drop -f ${DATABASE}
  mysql -e "CREATE DATABASE ${DATABASE} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
  if compgen -G "${DATABASE}-full-*.sql" > /dev/null; then
    list=( ${DATABASE}-full-*.sql )
    echo "Using local file ${list[-1]}"
    mysql ${DATABASE} < "${list[-1]}"
  else
    for _sql in echoCTF.sql echoCTF-routines.sql echoCTF-triggers.sql echoCTF-events.sql faq.sql instruction.sql rule.sql sysconfig.sql disabled_route.sql; do
      if [ -f "schemas/${_sql}" ]; then
        sed -e "s/echoCTF/${DATABASE}/g" schemas/${_sql} | mysql ${DATABASE}
      fi
    done
  fi
  sed -e "s/echoCTF/${DATABASE}/g" contrib/mysql-init.sql | mysql ${DATABASE}
  mysql ${DATABASE} -e "SET GLOBAL EVENT_SCHEDULER=ON"
}

function migrate() {
  ./backend/yii migrate --interactive=0
  ./backend/yii init_data --interactive=0
  ./backend/yii migrate-sales --interactive=0
  ./backend/yii migrate-red --migrationPath=@app/../migrations --interactive=0
}

function init() {
  ./backend/yii ssl/create-ca
  ./backend/yii ssl/create-cert
  ./backend/yii user/create admin admin@example.com admin
}

function sysconfig() {
  declare -A SYSCONFIG
#  SYSCONFIG[mail_useFileTransport]="1"
  SYSCONFIG[offense_domain]="ctf.example.local:8082"
  SYSCONFIG[moderator_domain]="mui.example.local:8080"
#  SYSCONFIG[treasure_secret_key]="secret"
#  SYSCONFIG[registrations_start]="2025-12-01 00:00:00"
#  SYSCONFIG[event_start]="2025-12-01 00:00:00"
  SYSCONFIG[force_https_urls]="0"

  for K in "${!SYSCONFIG[@]}"; do
    ./backend/yii sysconfig/set $K "${SYSCONFIG[$K]}"
  done
}

function tmuxs() {
  tmux -L ${DATABASE} kill-server || echo "tmux not running"
  sleep 1
  tmux -L ${DATABASE} new -d 'cd ./backend; php --define session.save_handler=memcached --define session.save_path=127.0.0.1:11211 --define session.name=mUISESSID yii serve 127.0.1.4:8080'
  tmux -L ${DATABASE} split-window 'cd ./frontend/web; php --define session.save_handler=memcached --define session.save_path=127.0.0.1:11211 --define session.name=pUI2SESSID -S 127.0.1.4:8082'
}

function eventOrganizers()
{
  ./backend/yii player/register "organizer" "organizer@example.local" "organizer" "organizer" offense 1 10 0 "CTF ORGANIZERS" 1
}

function sampleData()
{
  academic=0
  for no in $(seq 1 90);do
    academic=$(expr $academic % 3)
    ourno=$(expr $no / 3)
    ourno=$((ourno+1))
    if [ $academic -eq 0 ]; then
      base="gov"
    elif [ $academic -eq 1 ]; then
      base="edu"
    else
      base="pro"
    fi
    ./backend/yii player/register "${base}owner${ourno}" "${base}owner${ourno}@example.local" "team owner ${base} ${no}" "${base}owner${ourno}" offense 1 "${academic}" "${base}team${ourno}"
    academic=$((academic+1))
  done
  academic=0
  for no in $(seq 1 90);do
    academic=$(expr $academic % 3)
    ourno=$(expr $no / 3)
    ourno=$((ourno+1))
    if [ $academic -eq 0 ]; then
      base="gov"
    elif [ $academic -eq 1 ]; then
      base="edu"
    else
      base="pro"
    fi
    ./backend/yii player/register "${base}user${ourno}" "${base}user${no}@example.local" "${base} user ${no}" "${base}user${no}" offense 0 "${academic}"
    academic=$((academic+1))
  done
}

function extras() {
  #mysql -e "UPDATE server SET connstr='tcp://127.0.0.1:2375',network='isolated-bridge'" ${DATABASE}
  #mysql -e "UPDATE player_last SET vpn_local_address=inet_aton(concat('10.10.0.',id))" ${DATABASE}
  #(
  #  cd ansible
  #  ./playbooks/feed-targets.yml -i inventories/targets
  #  ./playbooks/feed-challenges.yml -i inventories/challenges
  #)
  #mysql -e 'insert into target_instance (player_id,target_id,server_id,ip) select id as player_id, (id % 13)+1 as target_id,(id%5)+1 as server_id,null from player' ${DATABASE}
  tmux -L ${DATABASE} split-window  '../ws-server/ws-server -db mysql -dsn "root@/echoCTF" -addr :8888'
  sleep 1
  tmux -L ${DATABASE} split-window  'python3 contrib/watchdoger.py --file_path /tmp/event_finished --url http://127.0.0.1:8888/broadcast --token server123token'
  tmux -L ${DATABASE} select-layout tiled
}

function usage() {
  echo "1. services: Start mysql and memcached"
  echo "2. settings: Prepare sample settings"
  echo "3. sql: Do SQL imports"
  echo "4. sysconfig: Sysconfig values"
  echo "5. init: Perform yii inits"
  echo "6. tmuxs: Start tmux sessions"
  echo "6. eventOrganizers: Add event organizers"
  echo "7. sampleData: Populate sample data"
  echo "8. migrate: Perform Yii2 migrations"
}

if [ $# -eq 0 ]; then
  usage
  exit 1
fi

count=$#
for arg in "$@"; do
  echo "Doing $arg";

  case "$arg" in
    "settings") "$arg" ;;
    "services") "$arg" ;;
    "sql") "$arg" ;;
    "migrate") "$arg" ;;
    "init") "$arg" ;;
    "tmuxs") "$arg" ;;
    "sysconfig") "$arg" ;;
    "sampleData") "$arg" ;;
    "eventOrganizers") "$arg" ;;
    "extras") "$arg" ;;
    *) echo "Option: $arg does not exist"; usage ;;
  esac
  ((count--))

  if [ "${WAIT_AFTER_STEP}" != "0" ] && ((count > 0)); then
    read -t $WAIT_AFTER_STEP -p "Hit ENTER or wait ${WAIT_AFTER_STEP} seconds" || echo;
  fi

done
