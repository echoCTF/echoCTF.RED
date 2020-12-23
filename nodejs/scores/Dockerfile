FROM node:15.4-buster-slim
# Create app directory
WORKDIR /UI
ENV DEBIAN_FRONTEND noninteractive
RUN set -ex \
    && apt-get update \
    && apt-get dist-upgrade -y \
    && apt-get autoremove -y \
    && rm -rf /usr/src/* /var/lib/apt/lists/*

# Bundle app source
COPY client.html .
COPY config.js .
COPY package.json .
COPY scores.js .

RUN npm install
# If you are building your code for production
# RUN npm install --only=production
WORKDIR /
EXPOSE 8000
CMD [ "npm", "start" ]
