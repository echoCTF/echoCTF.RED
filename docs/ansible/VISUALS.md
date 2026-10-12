# Visuals playbook (ansible/runonce/visuals.yml)

This playbook configures an OpenBSD server to act as a network visuals concentrator for when there are many VPN servers deployed for an event.

**REMOTE**
Connect with SSH as root and ask password to connect
```sh
ansible-playbook runonce/visuals.yml -i 192.168.1.12, -uroot -k
```

**LOCAL**
Run on local OpenBSD system. Current user `root`.
```sh
pkg_add -vvi ansible
ansible-playbook runonce/visuals.yml --connection=local -i 127.0.0.1,
```
