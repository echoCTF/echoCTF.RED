FROM node:8.7-wheezy
# Create app directory
WORKDIR /UI

# Bundle app source
COPY client.html .
COPY config.js .
COPY package.json .
COPY playerscores.js .

RUN npm install
# If you are building your code for production
# RUN npm install --only=production
WORKDIR /
EXPOSE 8000
CMD [ "npm", "start" ]
