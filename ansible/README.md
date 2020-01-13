# echoCTF-targets
echoCTF infrastructure and target playbooks

## Preparation
* Generate ssh keys
```
ssh-keygen -t rsa -C "keycomment" -f ssh_keys/ctf_rsa -N''
```

* Generate docker-target `host_vars` based on Dockerfiles available
```
echo "[docker-targets]">inventories/docker-targets/hosts
for i in Dockerfiles/*;do
  fqdn=$(grep ^fqdn $i/variables.yml|awk '{print $2}')
  ln -fs ../../../$i/variables.yml inventories/docker-targets/host_vars/${fqdn}.yml
  echo -e "${fqdn}\t\t\t# "$(basename $i)>>inventories/docker-targets/hosts
done
```

## Deploying
* Generating DNS ZONE files
```
ansible-playbook servers/generate-dnszones.yml  -i inventories/docker-targets
```

* Prepare the docker servers
```
ansible-playbook servers/docker-masters.yml -i inventories/docker-masters
```

* Build the images (this connects to builder ip 10.0.0.253)
```
ansible-playbook gameplays/build-images.yml -i inventories/docker-targets [ --extra-vars  "force_build=true" ]
```

* Update mui with target details
```
ansible-playbook gameplays/feed-mui.yml -i inventories/docker-targets
```

* Deploy docker targets
```
ansible-playbook gameplays/docker-targets.yml  -i inventories/docker-targets
```
