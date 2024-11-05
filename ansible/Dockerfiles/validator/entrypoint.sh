#!/bin/bash
if [ "$FETCH_URL"x == "x" ] || [ "$VALIDATE_LANG"x == "x" ]; then
  echo "Error: FETCH_URL or VALIDATE_LANG env variable not set";
  exit 1
fi

wget -O /echoctf/script_to_validate."${VALIDATE_LANG}" "${FETCH_URL}"


if [ -x /usr/local/validators/${VALIDATE_LANG}_validator ]; then
  /usr/local/validators/${VALIDATE_LANG}_validator
fi
$@
