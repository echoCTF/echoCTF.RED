# LetsEncrypt through Cloudflare

Use LetsEncrypt DNS for generating and renewing certificates through DNS.

## Step 1 - Create a scoped Cloudflare API token

**Don't use the Global API Key.**

In Cloudflare → My Profile → API Tokens → Create Token → "Edit zone DNS" template:

- Permission: Zone → DNS → Edit
- Zone Resources: Include → Specific zone → example.com
- This token only matters for writing the `_acme-challenge.<domain>` TXT record.

## Step 2 - Initialize

```
pkg_add -vvi py3-pip acme.sh py3-cryptography
pip3 install cloudflare
mkdir -p /root/.acme.sh
acme.sh --set-default-ca --server letsencrypt
```

## Step 3 - Issue certificate

```
export CF_Token="your-scoped-token"

# ECDSA
acme.sh --issue --dns dns_cf -d svc.example.com --keylength ec-256
# RSA
acme.sh --issue --dns dns_cf -d svc.example.com --keylength 2048
acme.sh --issue --dns dns_cf -d svc.example.com --keylength 4096

acme.sh --install-cert -d svc.example.com \
  --fullchain-file /etc/ssl/svc.example.com.fullchain.pem \
  --key-file        /etc/ssl/private/svc.example.com.key \
  --reloadcmd       "rcctl reload relayd"
```

## Step 4 - Renewals

```
@midnight -ns ( acme.sh --issue --dns dns_cf -d svc.example.com --keylength 2048 && acme.sh --install-cert -d svc.example.com --fullchain-file /etc/ssl/svc.example.com.crt --key-file /etc/ssl/private/svc.example.com.key --reloadcmd "rcctl reload relayd" ) >>/var/log/acme-svc.example.com.log
```

## Appendix

### relayd.conf exmaple

```
table <local> { 127.0.0.1 }

http protocol "https" {
    tls keypair svc.example.com
    pass
}

relay "registry" {
    listen on 0.0.0.0 port 443 tls
    protocol "https"
    forward to <local> port 5000
}
```
