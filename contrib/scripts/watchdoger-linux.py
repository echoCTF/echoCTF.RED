#!/usr/bin/env python3
#
# pip install requests inotify_simple
#
import argparse
import os
import requests
from inotify_simple import INotify, flags

parser = argparse.ArgumentParser()
parser.add_argument("--file_path", required=True, help="Full path to the file to monitor")
parser.add_argument("--url", required=True, help="HTTP endpoint URL to POST to")
parser.add_argument("--token", required=True, help="Bearer token for authorization")
args = parser.parse_args()

FULL_PATH = os.path.abspath(args.file_path)
FOLDER = os.path.dirname(FULL_PATH)
URL = args.url
BEARER_TOKEN = args.token

inotify = INotify()
watch_flags = flags.CREATE | flags.CLOSE_WRITE | flags.MOVED_TO
inotify.add_watch(FOLDER, watch_flags)

print(f"Watching for {FULL_PATH} ...")

try:
    while True:
        events = inotify.read()
        if events and os.path.exists(FULL_PATH):
            response = requests.post(
                URL,
                headers={
                    "Authorization": f"Bearer {BEARER_TOKEN}",
                    "Content-Type": "application/json"
                },
                json={
                    "event": "apiNotifications"}
            )
            print(f"Posted {FULL_PATH}, status: {response.status_code}")
            break
finally:
    inotify.close()