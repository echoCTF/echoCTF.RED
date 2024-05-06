#!/bin/bash
#
# Simple script to initiallize a development installation
#

: ${DATABASE:=echoCTF}

echo "Using database: ${DATABASE}"


function settings() {
  composer.phar install -d backend
  composer.phar install -d frontend
  cp backend/config/cache-local.php backend/config/cache.php
  sed -e "s/echoCTF/${DATABASE}/g" backend/config/db-sample.php > backend/config/db.php
  cp backend/config/validationKey-local.php backend/config/validationKey.php
  sed -e "s/echoCTF/${DATABASE}/g" frontend/config/db-local.php > frontend/config/db.php
  cp frontend/config/memcached-local.php frontend/config/cache.php
  cp frontend/config/validationKey-local.php frontend/config/validationKey.php
  mkdir -p frontend/web/images/avatars/ frontend/web/images/avatars/team/
}


function services() {
  sudo service memcached restart
  sudo service mysql restart
  mysql ${DATABASE} -e "SET GLOBAL EVENT_SCHEDULER=ON"
  mysql ${DATABASE} < contrib/mysql-init.sql > /dev/null
}

function sql() {
  mysqladmin drop -f ${DATABASE}
  mysql -e "CREATE DATABASE ${DATABASE} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
  if compgen -G "${DATABASE}-full-*.sql" > /dev/null; then
    mysql ${DATABASE} < ${DATABASE}-full-*.sql
  else
    for _sql in echoCTF.sql echoCTF-routines.sql echoCTF-triggers.sql echoCTF-events.sql faq.sql instruction.sql rule.sql sysconfig.sql disabled_route.sql; do
      if [ -f "schemas/${_sql}" ]; then
        mysql ${DATABASE} < schemas/${_sql}
      fi
    done
  fi
  mysql ${DATABASE} < contrib/mysql-init.sql
  mysql ${DATABASE} -e "SET GLOBAL EVENT_SCHEDULER=ON"
}

function init() {
  ./backend/yii migrate --interactive=0
  ./backend/yii init_data --interactive=0
  ./backend/yii migrate-sales --interactive=0
  sysconfig
  ./backend/yii migrate-red --migrationPath=@app/../migrations --interactive=0
  ./backend/yii ssl/create-ca
  ./backend/yii ssl/create-cert
  ./backend/yii user/create admin admin@example.com admin
}

function sysconfig() {
  declare -A SYSCONFIG
  SYSCONFIG[event_start]='1709913600'
  SYSCONFIG[event_end]='1710000000'
  SYSCONFIG[profile_visibility]="private"
  SYSCONFIG[default_homepage]="/dashboard"
  SYSCONFIG[approved_avatar]="1"
  SYSCONFIG[mail_useFileTransport]="1"
  SYSCONFIG[team_manage_members]="1"
  SYSCONFIG[team_required]="1"
  SYSCONFIG[vpngw]="vpn.XXCHANGEMEXX"
  SYSCONFIG[event_active]="1"
  SYSCONFIG[twitter_hashtags]="XXCHANGEMEXX,CTF"
  SYSCONFIG[twitter_account]="XXCHANGEMEXX"
  SYSCONFIG[members_per_team]="3"
  SYSCONFIG[challenge_home]="uploads/"
  SYSCONFIG[dashboard_is_home]="1"
  SYSCONFIG[defense_registered_tag]="DEFENSE_REGISTERED"
  SYSCONFIG[disable_registration]="0"
  SYSCONFIG[offense_registered_tag]="OFFENSE_REGISTERED"
  SYSCONFIG[online_timeout]="900"
  SYSCONFIG[player_profile]="1"
  SYSCONFIG[require_activation]="1"
  SYSCONFIG[spins_per_day]="70"
  SYSCONFIG[teams]="1"
  SYSCONFIG[members_per_team]="3"
  SYSCONFIG[mail_from]="register@XXCHANGEMEXX"
  SYSCONFIG[mail_fromName]="XXCHANGEMEXX CTF"
  SYSCONFIG[mail_host]="smtp.gmail.com"
  SYSCONFIG[mail_port]="25"
  SYSCONFIG[mail_username]="register@XXCHANGEMEXX"
  SYSCONFIG[mail_password]=""
  SYSCONFIG[offense_domain]="ctf.XXCHANGEMEXX"
  SYSCONFIG[frontpage_scenario]="XXCHANGEMEXX CTF"
  SYSCONFIG[event_name]="XXCHANGEMEXX CTF"
  SYSCONFIG[footer_logos]=''
  SYSCONFIG[dn_countryName]='GR'
  SYSCONFIG[dn_localityName]='Athens'
  SYSCONFIG[dn_organizationalUnitName]='XXCHANGEMEXX'
  SYSCONFIG[dn_organizationName]='echoCTF'
  SYSCONFIG[dn_stateOrProvinceName]='Greece'
  for K in "${!SYSCONFIG[@]}"; do
    ./backend/yii sysconfig/set $K "${SYSCONFIG[$K]}"
  done
}

function tmuxs() {
  tmux -L ${DATABASE} new -d  'cd ./backend; php --define session.save_handler=memcached --define session.save_path=127.0.0.1:11211 --define session.name=mUISESSID yii serve 127.0.0.1:8080'
  tmux -L ${DATABASE} split-window 'cd ./frontend/web; php --define session.save_handler=memcached --define session.save_path=127.0.0.1:11211 --define session.name=pUI2SESSID -S 127.0.0.1:8082'
}

function eventOrganizers()
{
  #./backend/yii player/register "organizer" "organizer@example.com" "organizer" "organizer" offense 0 "" "CTF ORGANIZERS"
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
    ./backend/yii player/register "${base}owner${ourno}" "${base}owner${ourno}@example.com" "team owner ${base} ${no}" "${base}owner${ourno}" offense 1 "${academic}" "${base}team${ourno}"
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
    ./backend/yii player/register "${base}user${ourno}" "${base}user${no}@example.com" "${base} user ${no}" "${base}user${no}" offense 0 "${academic}"
    academic=$((academic+1))
  done
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
}

if [ $# -eq 0 ]; then
  usage
  exit 1
fi
for arg in "$@"; do
  case "$arg" in
    "settings") "$arg" ;;
    "services") "$arg" ;;
    "sql") "$arg" ;;
    "init") "$arg" ;;
    "tmuxs") "$arg" ;;
    "sysconfig") "$arg" ;;
    "sampleData") "$arg" ;;
    "eventOrganizers") "$arg" ;;
    *) echo "Option: $arg does not exist"; usage ;;
  esac
done
