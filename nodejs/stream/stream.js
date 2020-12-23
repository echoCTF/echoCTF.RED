var config = require('./config');
var url = require('url');
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
  pollingTimer,
  limit_results=0,
  academic=-1;

// var $ = require('jquery');
// If there is an error connecting to the database
process.on('uncaughtException', function (err) {
  console.error(err.stack);
  console.log("Node NOT Exiting...");
});
connection.connect(function(err) {
  // connected! (unless `err` is set)
  console.log(err);
});

app.listen( config.port );

// on server started we can load our client.html page
function handler(req, res) {
  var url_parts = url.parse(req.url, true);
  var query = url_parts.query;
  if(query.limit)
        limit_results=parseInt(query.limit);
  if(query.academic)
        academic=parseInt(query.academic);
  else {
    academic=-1;
  }

//  if (req.url == '/') {
    fs.readFile(__dirname + '/client.html', function(err, data) {
      if (err) {
        console.log(err);
        res.writeHead(500);
        return res.end('Error loading client.html');
      }
      res.writeHead(200);
      res.end(data);
    });
//  }
/*  else if (req.url == '/stream.css')
  {
    fs.readFile(__dirname + '/stream.css', function(err, data) {
      if (err) {
        console.log(err);
        res.writeHead(500);
        return res.end('Error loading leader.css');
      }
      res.writeHead(200);
      res.end(data);
    });

  }*/
}


/*
 *
 * HERE IT IS THE COOL PART
 * This function loops on itself since there are sockets connected to the page
 * sending the result of the database query after a constant interval
 *
 */

var pollingLoop = function() {

  // Doing the database query
  if(academic>0)
  {
    var querystr='SELECT * FROM public_stream WHERE academic=1';
  }
  else if(academic===0)
  {
    var querystr='SELECT * FROM public_stream WHERE academic=0';
  }
  else {
    var querystr='SELECT * FROM public_stream ';
  }
  if(limit_results>0)
    querystr=querystr+' LIMIT '+ mysql.escape(limit_results);

  var query = connection.query(querystr);
  var users = []; // this array will contain the result of our db query

  // setting the query listeners
  query
    .on('error', function(err) {
      // Handle error, and 'end' event will be emitted after this as well
      console.log(err);
      updateSockets(err);
    })
    .on('result', function(user) {
      // it fills our array looping on each user row inside the db
      users.push(user);
    })
    .on('end', function() {
      // loop on itself only if there are sockets still connected
      if (connectionsArray.length) {
        pollingTimer = setTimeout(pollingLoop, POLLING_INTERVAL);

        updateSockets({
          users: users
        });
      }
    });

};


// creating a new websocket to keep the content updated without any AJAX request
io.sockets.on('connection', function(socket) {
  console.log(socket.handshake.headers.cookie);
  console.log('Number of connections:' + connectionsArray.length);
  // starting the loop only if at least there is one user connected
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
  // adding the time of the last update
  data.time = new Date();
  // sending new data to all the sockets connected
  connectionsArray.forEach(function(tmpSocket) {
    tmpSocket.volatile.emit('notification', data);
  });
};
