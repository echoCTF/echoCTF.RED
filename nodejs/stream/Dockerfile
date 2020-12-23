FROM node:15.4-buster-slim
# Create app directory
WORKDIR /UI

# Bundle app source
COPY client.html .
COPY config.js .
COPY package.json .
COPY stream.js .

RUN npm install
# If you are building your code for production
RUN npm install --only=production
EXPOSE 8000
CMD [ "npm", "start" ]
