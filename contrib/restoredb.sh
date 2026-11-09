#!/bin/sh
#
# Simple script to restore a database schema based on a dump
#

set -eu

DATABASE=${DATABASE:-echoCTF}
dumpfile=${1:-echoCTF.sql.gz}

echo "Using database: ${DATABASE}"

tmpfile=$(mktemp)
trap 'rm -f "$tmpfile"' EXIT

zcat "$dumpfile" > "$tmpfile"

mysqladmin drop -f "$DATABASE" || echo "Skipping drop of non existing database: ${DATABASE}"
mysql -e "CREATE DATABASE ${DATABASE} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
echo "Created database: ${DATABASE}"
mysql "$DATABASE" < "$tmpfile"
