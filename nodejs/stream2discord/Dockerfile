FROM node:8.7-wheezy
# Create app directory
WORKDIR /UI

# Bundle app source
COPY config.js .
COPY package.json .
COPY stream2discord.js .

RUN npm install
# If you are building your code for production
# RUN npm install --only=production
WORKDIR /

CMD [ "npm", "start" ]
