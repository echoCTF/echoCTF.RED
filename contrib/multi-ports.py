#!/usr/bin/python3 -u
# Run with python -u so that the output is unbuffered
# multi-ports.py 50000 1.1.1.1 2.2.2.2 3.3.3.3 | gource or logstalgia
#   gource --log-format custom --highlight-all-users --realtime --multi-sampling --auto-skip-seconds 3 --seconds-per-day 1  -f -
#   logstalgia -x --hide-response-code -g "UDP,URI=udp?$,20" -g "TCP,URI=tcp?$,60" -g "ICMP,URI=icmp?$,20" -
#
import socket
import select
import sys
def eprint(*args, **kwargs):
    print(*args, file=sys.stderr, **kwargs)

# Configuration: You can adjust the host and ports as needed.
PORT = int(sys.argv[1]) # first argument port
HOSTS = sys.argv[2:-1]  # List of IP's to connect to
def create_socket_connection(host, port):
    """Creates a socket connection to the specified host and port."""
    s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
    s.setblocking(0)  # Set to non-blocking mode
    s.connect_ex((host, port))  # Use connect_ex for non-blocking
    return s

def main():
    sockets = []
    buffers = {}  # Dictionary to store partial data for each socket

    # Create connections to all hosts
    for host in HOSTS:
        try:
            sock = create_socket_connection(host, PORT)
            sockets.append(sock)
            buffers[sock] = ""  # Initialize buffer for each socket
            eprint(f"Connected to {host}:{PORT}")
        except Exception as e:
            eprint(f"Could not connect to {host}:{PORT}: {e}")

    while True:
        # Use select to wait for any socket to have readable data
        readable, _, _ = select.select(sockets, [], [], 1.0)  # 1-second timeout

        for sock in readable:
            try:
                data = sock.recv(1024).decode('utf-8')  # Read data from the socket
                if data:
                    buffers[sock] += data  # Append the received data to the buffer

                    # Process full lines from the buffer
                    while '\n' in buffers[sock]:
                        line, buffers[sock] = buffers[sock].split('\n', 1)  # Split at the first newline
                        eprint(f"{line}")
                else:
                    # Connection closed by the server
                    eprint(f"Connection to {sock.getpeername()[0]}:{sock.getpeername()[1]} closed.")
                    sockets.remove(sock)
                    del buffers[sock]
                    sock.close()

            except Exception as e:
                eprint(f"Error receiving data from {sock.getpeername()[0]}:{sock.getpeername()[1]}: {e}")
                sockets.remove(sock)
                del buffers[sock]
                sock.close()

if __name__ == "__main__":
    main()
