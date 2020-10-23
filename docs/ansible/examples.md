# Examples

## Build target images
```sh
ansible-playbook playbooks/build-images.yml -i inventories/targets
# or
ansible-playbook playbooks/build-squash.yml -i inventories/targets
```

## Run target images locally
```sh
ansible-playbook playbooks/run-images.yml -i inventories/targets
```

## Feed mUI
**Feed Challenges**
```sh
ansible-playbook playbooks/feed-challenges.yml -i inventories/challenges
```

**Feed targets**
```sh
ansible-playbook playbooks/feed-targets.yml -i inventories/targets
```
