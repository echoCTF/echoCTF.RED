#!/usr/local/bin/python3
#
#
import argparse
import os
import select
import subprocess

parser = argparse.ArgumentParser()
parser.add_argument("--file_path", required=True, help="Full path to the file to monitor")
parser.add_argument("--action", required=True, help="Command to execute when the file is created")
args = parser.parse_args()

FULL_PATH = os.path.abspath(args.file_path)
FOLDER = os.path.dirname(FULL_PATH)
ACTION = args.action

# Open the directory we're monitoring
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

            # Execute the action
            subprocess.run(ACTION, shell=True)

            break

finally:
    kq.close()
    os.close(fd)