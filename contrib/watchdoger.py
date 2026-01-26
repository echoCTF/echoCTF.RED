#!/usr/local/bin/python3
#
# pip install watchdog requests
import argparse
import os
import requests
from watchdog.observers import Observer
from watchdog.events import FileSystemEventHandler

# CLI arguments
parser = argparse.ArgumentParser()
parser.add_argument("--file_path", required=True, help="Full path to the file to monitor")
parser.add_argument("--url", required=True, help="HTTP endpoint URL to POST to")
parser.add_argument("--token", required=True, help="Bearer token for authorization")
args = parser.parse_args()

FULL_PATH = args.file_path
FOLDER = os.path.dirname(FULL_PATH)
TARGET_FILE = os.path.basename(FULL_PATH)
URL = args.url
BEARER_TOKEN = args.token

class Handler(FileSystemEventHandler):
    def __init__(self, observer):
        self.observer = observer

    def on_created(self, event):
        if not event.is_directory and event.src_path == FULL_PATH:
            response = requests.post(
                URL,
                headers={
                    "Authorization": f"Bearer {BEARER_TOKEN}",
                    "Content-Type": "application/json"
                },
                json={
                    "event": "apiNotifications"
                }
            )
            print(f"Posted {event.src_path}, status: {response.status_code}")
            self.observer.stop()  # exit after sending

observer = Observer()
handler = Handler(observer)
observer.schedule(handler, FOLDER, recursive=False)
observer.start()
observer.join()
