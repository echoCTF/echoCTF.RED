#!/bin/sh
date +%s > "$MARKER.tmp" && mv "$MARKER.tmp" "$MARKER"
kill "$PPID"
