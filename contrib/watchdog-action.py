#!/usr/local/bin/python3
#
# pip install watchdog
import argparse
import os
from watchdog.observers import Observer
from watchdog.events import FileSystemEventHandler

# CLI arguments
parser = argparse.ArgumentParser()
parser.add_argument("--file_path", required=True, help="Full path to the file to monitor")
parser.add_argument("--action", required=True, help="Full path to the file we will execute")
args = parser.parse_args()

FULL_PATH = args.file_path
FOLDER = os.path.dirname(FULL_PATH)
TARGET_FILE = os.path.basename(FULL_PATH)
ACTION = args.action

class Handler(FileSystemEventHandler):
    def __init__(self, observer):
        self.observer = observer

    def on_created(self, event):
        if not event.is_directory and event.src_path == FULL_PATH:
            os.system(ACTION)
            self.observer.stop()

observer = Observer()
handler = Handler(observer)
observer.schedule(handler, FOLDER, recursive=False)
observer.start()
observer.join()
