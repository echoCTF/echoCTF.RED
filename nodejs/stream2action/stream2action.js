var config = require('./config');
var request = require('request');
var net = require('net');
var cliID = process.argv.slice(2)[0];
var mysql = require('mysql'),
    connection = mysql.createConnection({
      host: config.db_host,
      user: config.db_user,
      password: config.db_pass,
      database: config.db_name,
      port: 3306
    }),
  POLLING_INTERVAL = 5000,
  pollingTimer,
  lastID=config.lastID,
  headersOpt = { "content-type": "application/json" };
var treasureActions;
if (cliID)
  lastID=cliID;
process.on('uncaughtException', function (err) {
  console.error(err.stack);
  console.log("Node NOT Exiting...");
});

async function do_commands(commands)
{
  var sleeping=0;
  console.log('Commands: '+commands.length);
  if(commands.length>1)
  {
    sleeping=2;
  }
  commands.forEach((element)=>function(element) {
    if(element.ip && element.port)
    {
      var client=new net.Socket();
      console.log('Sending ['+element.command +'] to '+element.ip+':'+element.port);
      client.setTimeout(500);
      client.connect(element.port,element.ip, function() {
        this.write(element.command);
        this.destroy();
      })
      .on('error',function(err){
        this.destroy();
      });
    }
    else
    { console.log('Empty IP command only ' + element.command); }
  });
}

var pollingLoop = function() {

  // Doing the database query
  var query = connection.query('SELECT id,model_id,model FROM stream WHERE id > '+lastID+' and model="treasure" order by id asc'),
  commands = []; // this array will contain the result of our db query
  var content="";

  // setting the query listeners
  query
    .on('error', function(err) {
      console.log(err);
    })
    .on('result', function(user) {
      for(var i in treasureActions){
        if(treasureActions[i].treasure_id == user.model_id){
          commands.push(treasureActions[i]);
        }
      }
      if (commands.length>0)
        do_commands(commands);

      lastID=user.id;
      console.log(lastID);
      commands=[];

    })
    .on('end', function() {
        pollingTimer = setTimeout(pollingLoop, POLLING_INTERVAL);
    });

};


connection.connect(function(err) {
  connection.query("SELECT id,treasure_id,INET_NTOA(ip) as ip,port,command FROM treasure_action ORDER BY treasure_id,weight,id", function (err, result, fields) {
      if (err) throw err;
      treasureActions=result;
  });

  pollingLoop();
  console.log(err);
});
