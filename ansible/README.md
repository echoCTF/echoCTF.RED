# echoCTF infrastructure and target playbooks
The following commands assume you have Ansible installed.


## Preparation

* Generate ssh keys that will be used for administering the CTF backend infrastructure
```sh
ssh-keygen -t rsa -C "keycomment" -f ssh_keys/ctf_rsa -N''
```

* Prepare directory structure that will hold your Ansible managed inventories
```sh
mkdir -p inventories/targets/{host_vars,group_vars}
mkdir -p inventories/dockers/{host_vars,group_vars}
...
```

* XXXFIXMEXXX Create and configure host_vars and group_vars for docker servers and targets

* XXXFIXMEXXX Create a docker target

* Generate `inventories/targets/host_vars` based on `Dockerfiles` available
```sh
INVENTORY="targets"
echo "[$INVENTORY]">inventories/targets/hosts
for i in Dockerfiles/*;do
  fqdn=$(grep ^fqdn $i/variables.yml|awk '{print $2}')
  ln -fs ../../../$i/variables.yml inventories/$INVENTORY/host_vars/${fqdn}.yml
  echo -e "${fqdn}\t\t\t# "$(basename $i)>>inventories/$INVENTORY/hosts
done
```


* Apply configuration settings to docker servers required to host and run docker targets.
```sh
ansible-playbook playbooks/docker-masters.yml -i inventories/dockers
```

## Deploying

* Build the images
```
ansible-playbook gameplays/build-images.yml -i inventories/targets
```

* Build the images to remote builder machine
```
ansible-playbook gameplays/build-images.yml -i inventories/targets --extra-vars  "BUILDER=10.20.30.40"
```


* Update backend user inteface with target details
```
ansible-playbook gameplays/feed-mui.yml -i inventories/docker-targets
```

* Update specific backend user inteface url with target details
```
ansible-playbook gameplays/feed-mui.yml -i inventories/docker-targets  -e '{"mui":{"URL": "http://127.0.0.1:8080"}}'
```

* (optional) Deploy docker targets by ansible. __This is not needed since the backend scripts take care of it__
```sh
ansible-playbook gameplays/docker-targets.yml  -i inventories/docker-targets
```

## General purpose commands
* Generating DNS ZONE files (currently in SQL format)
```
ansible-playbook servers/generate-dnszones.yml  -i inventories/docker-targets
```
