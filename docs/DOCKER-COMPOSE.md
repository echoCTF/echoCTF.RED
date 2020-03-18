# docker-compose instructions

exec on backend
```sh
./backend/yii migrate --interactive=0
./backend/yii init_data --interactive=0
./backend/yii ssl/create-ca
./backend/yii sysconfig/set mail_from dontreply@echoctf.red
./backend/yii user/create echothrust info@echothrust.com echothrust
./backend/yii ssl/create-ca

#./backend/yii player/register echothrust info@echothrust.com echothrust echothrust offense 1;\
```
