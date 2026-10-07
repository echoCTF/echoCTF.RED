#!/usr/bin/env python3
# multi-ports.py [-c FILE] [-l SOCKET] [PORT HOST ...]
#   multi-ports.py 50000 1.1.1.1 2.2.2.2 3.3.3.3 | gource or logstalgia
#   gource --log-format custom --highlight-all-users --realtime --multi-sampling --auto-skip-seconds 3 --seconds-per-day 1  -f -
#   logstalgia -x --hide-response-code -g "UDP,URI=udp?$,20" -g "TCP,URI=tcp?$,60" -g "ICMP,URI=icmp?$,20" -
#
#   -c FILE    take the targets from FILE, one "host port" per line, '#' starts a comment
#   -l SOCKET  serve consumers on the unix SOCKET instead of stdout. The targets are
#              connected while at least one consumer is connected, and FILE is read
#              again every time the first consumer connects.
#
import argparse
import socket
import select
import sys
import os
def eprint(*args, **kwargs):
    print(*args, file=sys.stderr, flush=True, **kwargs)

parser = argparse.ArgumentParser()
parser.add_argument('-c', metavar='FILE')
parser.add_argument('-l', metavar='SOCKET')
parser.add_argument('port', nargs='?', type=int)
parser.add_argument('hosts', nargs='*')
args = parser.parse_args()
if args.c is None and args.port is None:
    parser.error("give -c FILE or PORT HOST ...")

def read_targets():
    if args.c is None:
        return [(host, args.port) for host in args.hosts]
    targets = []
    try:
        with open(args.c) as f:
            for line in f:
                line = line.split('#', 1)[0].strip()
                if not line:
                    continue
                host, port = line.split()
                targets.append((host, int(port)))
    except Exception as e:
        eprint(f"Could not read {args.c}: {e}")
    return targets

def create_socket_connection(host, port):
    """Creates a socket connection to the specified host and port."""
    s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
    s.setblocking(0)  # Set to non-blocking mode
    s.connect_ex((host, port))  # Use connect_ex for non-blocking
    return s

def connect_all(sockets, buffers):
    for host, port in read_targets():
        try:
            sock = create_socket_connection(host, port)
            sockets.append(sock)
            buffers[sock] = ""  # Initialize buffer for each socket
            eprint(f"Connected to {host}:{port}")
        except Exception as e:
            eprint(f"Could not connect to {host}:{port}: {e}")

def close_all(sockets, buffers):
    for s in sockets:
        s.close()
    sockets.clear()
    buffers.clear()

def main():
    sockets = []
    buffers = {}  # Dictionary to store partial data for each socket
    clients = []
    listener = None

    if args.l is None:
        connect_all(sockets, buffers)
    else:
        try:
            os.unlink(args.l)
        except FileNotFoundError:
            pass
        listener = socket.socket(socket.AF_UNIX, socket.SOCK_STREAM)
        listener.bind(args.l)
        listener.listen(16)

    while True:
        # Use select to wait for any socket to have readable data
        watched = sockets + clients + ([listener] if listener else [])
        readable, _, _ = select.select(watched, [], [], 1.0)  # 1-second timeout

        for sock in readable:
            if sock is listener:
                client, _ = listener.accept()
                client.setblocking(0)
                clients.append(client)
                eprint(f"Consumer connected ({len(clients)} total)")
                if not sockets:
                    connect_all(sockets, buffers)
                continue

            if sock in clients:
                try:
                    data = sock.recv(1024)
                except Exception:
                    data = b""
                if not data:
                    clients.remove(sock)
                    sock.close()
                    eprint(f"Consumer disconnected ({len(clients)} total)")
                    if not clients:
                        close_all(sockets, buffers)
                        eprint("No consumers left, upstream connections closed")
                continue

            try:
                data = sock.recv(1024).decode('utf-8')  # Read data from the socket
                if data:
                    buffers[sock] += data  # Append the received data to the buffer

                    # Process full lines from the buffer
                    while '\n' in buffers[sock]:
                        line, buffers[sock] = buffers[sock].split('\n', 1)  # Split at the first newline
                        if listener is None:
                            print(f"{line}", flush=True)
                            continue
                        out = (line + '\n').encode('utf-8')
                        for client in list(clients):
                            try:
                                client.send(out)
                            except BlockingIOError:
                                pass
                            except Exception:
                                clients.remove(client)
                                client.close()
                                eprint(f"Consumer dropped ({len(clients)} total)")
                else:
                    # Connection closed by the server
                    eprint(f"Connection to {sock.getpeername()[0]}:{sock.getpeername()[1]} closed.")
                    sockets.remove(sock)
                    del buffers[sock]
                    sock.close()

            except Exception as e:
                eprint(f"Error receiving data from upstream: {e}")
                sockets.remove(sock)
                del buffers[sock]
                sock.close()

if __name__ == "__main__":
    main()
