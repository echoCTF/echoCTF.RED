#!/bin/bash
#
# Perform some very basic tests to ensure things are ok before pushing
#
set -euo pipefail
echo "contrib/Dockerfile"
docker build --build-arg GITHUB_OAUTH_TOKEN=${GITHUB_OAUTH_TOKEN} . --file contrib/Dockerfile --tag echoctf.red-all-in-one:test >/dev/null 2>&1

echo "contrib/Dockerfile-mariadb"
docker build . --file contrib/Dockerfile-mariadb --tag echoctf.red-db:test >/dev/null 2>&1

echo "contrib/Dockerfile-frontend"
docker build --build-arg GITHUB_OAUTH_TOKEN=${GITHUB_OAUTH_TOKEN} . --file contrib/Dockerfile-frontend --tag echoctf.red-frontend:test >/dev/null 2>&1

echo "contrib/Dockerfile-backend"
docker build --build-arg GITHUB_OAUTH_TOKEN=${GITHUB_OAUTH_TOKEN} . --file contrib/Dockerfile-backend --tag echoctf.red-backend:test >/dev/null 2>&1

echo "contrib/Dockerfile-vpn"
docker build --build-arg GITHUB_OAUTH_TOKEN=${GITHUB_OAUTH_TOKEN} . --file contrib/Dockerfile-vpn --tag echoctf.red-vpn:test >/dev/null 2>&1
