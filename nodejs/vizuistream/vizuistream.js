var config = require('./config');
const url = require('url');
const path = require('path');
var jquery = require('jquery');
var app = require('http').createServer(handler),
  io = require('socket.io').listen(app),
  fs = require('fs'),
  mysql = require('mysql'),
  connectionsArray = [],
  connection = mysql.createConnection({
    host: config.db_host,
    user: config.db_user,
    password: config.db_pass,
    database: config.db_name,
    port: 3306
  }),
  POLLING_INTERVAL = 3000,
  lastID = 0,
  pollingTimer;

connection.connect(function(err) {
  console.log(err);
});

app.listen(config.port);

function handler(req, res) {
  console.log(`${req.method} ${req.url}`);

  // parse URL
  const parsedUrl = url.parse(req.url);
  // extract URL path
  let pathname = `.${parsedUrl.pathname}`;
  // based on the URL path, extract the file extention. e.g. .js, .doc, ...
  const ext = path.parse(pathname).ext;
  // maps file extention to MIME typere
  const map = {
    '.ico': 'image/x-icon',
    '.html': 'text/html',
    '.js': 'text/javascript',
    '.json': 'application/json',
    '.css': 'text/css',
    '.png': 'image/png',
    '.jpg': 'image/jpeg',
    '.wav': 'audio/wav',
    '.mp3': 'audio/mpeg',
    '.svg': 'image/svg+xml',
    '.pdf': 'application/pdf',
    '.doc': 'application/msword'
  };

  fs.exists(pathname, function (exist) {
    if(!exist) {
      // if the file is not found, return 404
      res.statusCode = 404;
      res.end(`File `+escapeHTML(pathname)+` not found!`);
      return;
    }

    // if is a directory search for index file matching the extention
    if (fs.statSync(pathname).isDirectory()) pathname += '/index' + ext;

    // read file from file system
    fs.readFile(pathname, function(err, data){
      if(err){
        res.statusCode = 500;
        res.end(`Error getting the file: `+escapeHTML(err));
      } else {
        // if the file is found, set Content-type and send data
        res.setHeader('Content-type', map[ext] || 'text/plain' );
        res.end(data);
      }
    });
  });
}

var pollingLoop = function() {
  var query = connection.query('SELECT * FROM vizui_stream WHERE id>'+lastID),
  events = [];
  query
    .on('error', function(err) {
      console.log(err);
      updateSockets(err);
    })
    .on('result', function(eventitem) {
      events.push(eventitem);
      lastID = eventitem.id
    })
    .on('end', function() {
      if (connectionsArray.length) {
        pollingTimer = setTimeout(pollingLoop, POLLING_INTERVAL);
        updateSockets({
          events: events
        });
      }
    });

};


io.sockets.on('connection', function(socket) {
  console.log(socket.handshake.headers.cookie);
  console.log('Number of connections:' + connectionsArray.length);
  if (!connectionsArray.length) {
    pollingLoop();
  }

  socket.on('disconnect', function() {
    var socketIndex = connectionsArray.indexOf(socket);
    console.log('socket = ' + socketIndex + ' disconnected');
    if (socketIndex >= 0) {
      connectionsArray.splice(socketIndex, 1);
    }
  });

  console.log('A new socket is connected!');
  connectionsArray.push(socket);

});

var updateSockets = function(data) {
    data.time = new Date();
    connectionsArray.forEach(function(tmpSocket) {
        tmpSocket.volatile.emit('notification', data);
        console.log(data);
    });
};
