#!/usr/local/bin/python3
#
# Watches a folder for a file to appear, then runs a shell
# action or POSTs to a URL, once, then exits.
#
# Produced by a slopcannon
#
import argparse
import os
import select
import subprocess
import requests

parser = argparse.ArgumentParser()
parser.add_argument("--file_path", required=True, help="Full path to the file to monitor")

group = parser.add_mutually_exclusive_group(required=True)
group.add_argument("--action", help="Command to execute when the file is created")
group.add_argument("--url", help="HTTP endpoint URL to POST to")

parser.add_argument("--token", help="Bearer token for authorization (required with --url)")
parser.add_argument("--event", default="apiNotifications", help="Event name sent in the JSON payload (used with --url)")
args = parser.parse_args()

if args.url and not args.token:
    parser.error("--token is required when using --url")

if args.action and args.token:
    parser.error("--token is not allowed with --action")

FULL_PATH = os.path.abspath(args.file_path)
FOLDER = os.path.dirname(FULL_PATH)

fd = os.open(FOLDER, os.O_RDONLY)
kq = select.kqueue()

watch = select.kevent(
    fd,
    filter=select.KQ_FILTER_VNODE,
    flags=select.KQ_EV_ADD | select.KQ_EV_CLEAR,
    fflags=select.KQ_NOTE_WRITE,
)

print(f"Watching {FULL_PATH} for creation...")

try:
    while True:
        events = kq.control([watch], 1)

        if events and os.path.exists(FULL_PATH):
            print(f"{FULL_PATH} detected.")

            if args.action:
                subprocess.run(args.action, shell=True)
            else:
                response = requests.post(
                    args.url,
                    headers={
                        "Authorization": f"Bearer {args.token}",
                        "Content-Type": "application/json"
                    },
                    json={"event": args.event}
                )
                print(f"Posted {FULL_PATH}, status: {response.status_code}")

            break

finally:
    kq.close()
    os.close(fd)