const Discord = require('discord.js');
const client = new Discord.Client();
const config = require('./config.json');

var mysql = require('mysql'),
    connection = mysql.createConnection({
      host: config.dbhost,
      user: config.dbuser,
      password: config.dbpass,
      database: config.dbname,
//      port: 3306
    }),
  POLLING_INTERVAL = 5000,
  pollingTimer,
  lastID=config.lastID,
  headersOpt = { "content-type": "application/json" };

var pollingLoop = function() {

  // Doing the database query
  var query = connection.query('SELECT t1.*,t2.username,trim(t4.discord) as discord,t3.name as hostname,inet_ntoa(t3.ip) as ipaddr,TS_AGO(t1.ts) as ts_ago FROM stream as t1 LEFT JOIN player as t2 on t1.player_id=t2.id LEFT JOIN target as t3 on t3.id=t1.model_id left join profile as t4 on t4.player_id=t2.id WHERE t1.id > '+lastID+' and model="headshot" order by t1.ts, t1.id'),
  activity_stream = [];
  var content="";

  // setting the query listeners
  query
    .on('error', function(err) {
      console.log(err);
    })
    .on('result', function(entry) {
      content='`'+entry.username+'` got a headshot on `'+entry.hostname+'/'+entry.ipaddr+'`, '+entry.ts_ago+'. Well done!!! [`ID: '+entry.id+'`]';
      if(entry.discord!=""){
        var guild = client.guilds.get(config.defaultGuildID);
        member = client.guilds.get(config.defaultGuildID).members.find(member => member.user && ( member.user.tag.toLowerCase()===entry.discord.toLowerCase() || member.user.username.toLowerCase()===entry.discord.toLowerCase()));
        if(member && member.user)
        {
          content=`${member.user}`+' got a headshot :skull: on `'+entry.hostname+'/'+entry.ipaddr+'`, '+entry.ts_ago+'. Well done!!! [`ID: '+entry.id+'`]';
        }
      }
      console.log(content);
      lastID=entry.id;
    })
    .on('end', function() {
        pollingTimer = setTimeout(pollingLoop, POLLING_INTERVAL);
    });

};

/* Send help message */
function help(message)
{
  var msg="```\n~help This command\n~myid Sends your discord id to be added at your profile\n~ping Check if the bot is alive\n~target [shortname] displays target details```";
  return message.channel.send(msg);
}

/* LOOKUP TARGET DETAILS FROM THE DATABASE (temporary) */
function target_lookup(message,target)
{
  var sql    = 'SELECT t1.id,t1.name,t1.purpose, t1.description, t1.fqdn,inet_ntoa(t1.ip) as ip, count(distinct t2.id) as treasures, count(distinct t3.id) as findings,count(distinct t4.player_id) as headshots FROM target as t1 left join treasure as t2 on t2.target_id=t1.id LEFT JOIN finding as t3 on t3.target_id=t1.id LEFT JOIN headshot as t4 on t4.target_id=t1.id WHERE t1.name = ' + connection.escape(target)+' GROUP BY t1.id';
  connection.query(sql, function (error, results, fields) {
    if (error) throw error;
    if(!results.length) return message.reply(`target ${target} not found.`)
    let memberembed = new Discord.RichEmbed()
     .setColor('#94c11f')
     .setTitle(results[0].fqdn+" "+results[0].ip+" ID#"+results[0].id)
     .setDescription(results[0].purpose)
     .setURL(`https://echoctf.red/target/${results[0].id}`) // Their name, I use a different way, this should work
     .setThumbnail('https://echoctf.red/images/targets/_'+results[0].name+'.png') // Their icon
     .addField('Flags/Services',results[0].treasures+' / '+results[0].findings,true)
     .addField('Headshots',results[0].headshots,true);

    msg="https://echoctf.red/target/"+results[0].id;
    msg+="\n```"+'ID: '+ results[0].id + "\n";
    msg+='FQDN: '+ results[0].fqdn + "\n";
    msg+='IP: '+ results[0].ip + "\n";
    msg+='Flags/Services: '+ results[0].treasures + "/"+results[0].findings+"\n";
    msg+='Headshots: '+ results[0].headshots + "\n";
    msg+="```";
    return message.channel.send(memberembed);
    return message.channel.send(msg);
  });
}


// Create an event listener for messages
client.on('message', message => {
  // if bot or not on #general then ignore
  if (message.author.bot || message.channel.name!=="general") {
    return;
  }
  // if user not administrator and not allowedRole reject
  if (!message.member.hasPermission("ADMINISTRATOR") && !member.roles.has(member.guild.roles.find(r => r.name === config.allowedRole)))
      return message.reply(`only admins and ${config.allowedRole} are allowed to perform commands!`)

  console.log(`#${message.channel.name} ${message.author.username}#${message.author.tag}> ${message.content}`);

  // if not start with our prefix then ignore
  if (!message.content.startsWith(config.prefix)) return;
  console.log(`processing command ${message.author.tag}`)

  const args = message.content.slice(config.prefix.length).split(/ +/);
  const command = args.shift().toLowerCase();
  switch (command) {
    case 'purge':
      if (!message.member.hasPermission("ADMINISTRATOR"))
          return message.reply('only admins are allowed to perform this command!')
      const deleteCount = parseInt(args[0], 10);
      if(!deleteCount || deleteCount < 2 || deleteCount > 100)
        return message.reply("Please provide a number between 2 and 100 for the number of messages to delete");
      return message.channel.bulkDelete(deleteCount).catch(error => message.reply(`Couldn't delete messages because of: ${error}`));

    case 'say':
      const sayMessage = args.join(" ");
      message.delete().catch(O_o=>{});
      return message.channel.send(sayMessage);

    case 'leave':
      if (!message.member.hasPermission("ADMINISTRATOR"))
          return message.reply('only admins are allowed to perform this command!')
      return client.guilds.get(message.guild.id).leave();

    case 'help':
      return help(message);

    case 'ping':
      return message.reply('pong');

    case 'myid':
        return message.reply(`Your userid is: ${message.author.id}`);

    case 'target':
      if (!args.length) {
  		    return message.reply(`You didn't provide any arguments, ${message.author}!`);
  	   }
       else {
         return target_lookup(message,args[0])
       }
    default:
      return message.reply(`Have no idea what you meant there dude`);
  }
});

client.on('guildMemberRemove', member => {
  console.log(`member ${member.user.username} with id ${member} left :(`)
});

client.on('guildMemberAdd', member => {
    console.log(`${member.user.username} with id ${member} has joined`);
    if (!member.roles.has(member.guild.roles.find(r => r.name === config.autoRole)))
    {
      member.addRole(member.guild.roles.find(r => r.name === config.autoRole));
      console.log(`added new member ${member} on ${config.autoRole}`);
    }
    const channel = member.guild.channels.find(ch => ch.name === 'general');
    channel.send(`Welcome to the server, ${member}!`).then((newMessage) => {newMessage.react(":wave:");});
});

client.on("presenceUpdate", (oldMember, newMember) => {
    if(oldMember.presence.status !== newMember.presence.status){
        console.log(`${newMember.user.username} is now ${newMember.presence.status}`);
    }
});


client.on('ready', () => {
  console.log(`Logged in as ${client.user.tag}! ${config.defaultGuildID}`);
  client.user.setActivity("echoCTF.RED");
  connection.connect(function(err) {
      pollingLoop();
    });
});

client.login(config.token);
