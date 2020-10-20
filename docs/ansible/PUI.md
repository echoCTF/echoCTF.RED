# Participants UI playbook (ansible/runonce/pui.yml)

The playbook can run be run in remote and local mode depending on your setup
and access.

**REMOTE**
Connect with SSH as root and ask password to connect
```sh
ansible-playbook runonce/pui.yml -i 192.168.1.12, -uroot -k
```

**LOCAL**
Run on local OpenBSD system. Current user `root`.
```sh
pkg_add -vvi ansible
ansible-playbook runonce/pui.yml --connection=local -i 127.0.0.1,
```
