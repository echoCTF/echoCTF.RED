#!/usr/local/bin/python3
#
# pip install requests
import argparse
import os
import select
import requests

parser = argparse.ArgumentParser()
parser.add_argument("--file_path", required=True, help="Full path to the file to monitor")
parser.add_argument("--url", required=True, help="HTTP endpoint URL to POST to")
parser.add_argument("--token", required=True, help="Bearer token for authorization")
args = parser.parse_args()

FULL_PATH = os.path.abspath(args.file_path)
FOLDER = os.path.dirname(FULL_PATH)
URL = args.url
BEARER_TOKEN = args.token

fd = os.open(FOLDER, os.O_RDONLY)
kq = select.kqueue()

watch = select.kevent(
    fd,
    filter=select.KQ_FILTER_VNODE,
    flags=select.KQ_EV_ADD | select.KQ_EV_CLEAR,
    fflags=select.NOTE_WRITE
)

print(f"Watching for {FULL_PATH} ...")

try:
    while True:
        events = kq.control([watch], 1)
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
    kq.close()
    os.close(fd)