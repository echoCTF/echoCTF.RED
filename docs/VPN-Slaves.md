# VPN Slaves

## Syncing OpenVPN crl.pem

1. Prepare your local system (where you run ansible from).

   ```sh
   cd ansible
   mkdir -p ssh_keys
   ssh-keygen -t ed25519 -f ssh_keys/crl_push -N ""
   ```

2. Copy the private key to your primary vpn

   ```sh
   scp ssh_keys/crl_push vpn01:.ssh/
   ```

3. Get the secondary vpn host keys to the primary server

   ```sh
   ansible vpn01 -i inventories/servers -m shell -a "ssh-keyscan 10.7.0.202 10.7.0.203 10.7.0.204 10.7.0.205 10.7.0.206 10.7.0.207 10.7.0.208 10.7.0.209 10.7.0.210 >> ~/.ssh/known_hosts"
   ```

4. Add the primary server ssh key to the secondary servers

   ```sh
   ansible vpn -i inventories/servers -m ansible.posix.authorized_key -a "user=root key=\"{{ lookup('file', 'ssh_keys/crl_push.pub') }}\""
   ```

5. Configure the default inventory `/etc/ansible/hosts`. From the primary vpn server (`vpn01`). NOTE: Dont forget to cree the folder first with `mkdir -p /etc/ansible/`

   ```ini
   [vpn_targets]
   10.7.0.202
   10.7.0.203
   10.7.0.204
   10.7.0.205
   10.7.0.206
   10.7.0.207
   10.7.0.208
   10.7.0.209
   10.7.0.210
   [vpn_targets:vars]
   ansible_user=root
   ansible_ssh_private_key_file=/root/.ssh/crl_push
   ansible_python_interpreter=/usr/local/bin/python3
   ```

6. Install the community collection

   ```sh
   ansible-galaxy collection install community.general
   ```

7. Configure your `/etc/ansible/ansible.cfg` on the primary server

   ```ini
   [defaults]
   stdout_callback = community.general.unixy
   bin_ansible_callbacks = True
   display_ok_hosts = false
   ```

8. Add the crontab entry to populate it

   ```text
   */5 * * * * -ns ANSIBLE_LOG_PATH=/var/log/ansible-crl.log /usr/local/bin/ansible vpn_targets -m ansible.builtin.copy -a "src=/etc/openvpn/crl.pem dest=/etc/openvpn/crl.pem owner=root group=wheel mode=0644"
   ```

9. Make sure you remove the crontab entry from the secondary VPN servers

    ```
    #Ansible: Generate CRL
    #*/1 * * * * /root/sources/backend/bin/ssl-generate-crl
    ```
