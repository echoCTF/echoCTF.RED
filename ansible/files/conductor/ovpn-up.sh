#!/bin/sh
date +%s > /var/run/ovpn-probe.ok.tmp && mv /var/run/ovpn-probe.ok.tmp /var/run/ovpn-probe.ok
kill "$PPID"
