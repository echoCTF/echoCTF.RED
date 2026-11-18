#!/bin/sh
#
# Simple script to restore a database schema based on a dump
#

set -eu

usage() {
	echo "Usage: $(basename "$0") [dumpfile]"
	echo "  dumpfile  gzipped SQL dump (default: echoCTF.sql.gz)"
	echo "Env: DATABASE  target database (default: echoCTF, will be DROPPED)"
	exit 1
}

[ $# -eq 0 ] && usage

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
