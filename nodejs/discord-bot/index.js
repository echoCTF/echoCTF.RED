const Discord = require('discord.js');
const client = new Discord.Client();
const { prefix, allowedRole, autoRole, token, dbhost, dbuser, dbpass, dbname } = require('./config.json');

var mysql = require('mysql'),
    connection = mysql.createConnection({
      host: dbhost,
      user: dbuser,
      password: dbpass,
      database: dbname,
//      port: 3306
    }),
  POLLING_INTERVAL = 5000,
  pollingTimer,
  lastID=0,
  headersOpt = { "content-type": "application/json" };

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
    msg="https://echoctf.red/target/"+results[0].id;
    msg+="\n```"+'ID: '+ results[0].id + "\n";
    msg+='FQDN: '+ results[0].fqdn + "\n";
    msg+='IP: '+ results[0].ip + "\n";
    msg+='Flags/Services: '+ results[0].treasures + "/"+results[0].findings+"\n";
    msg+='Headshots: '+ results[0].headshots + "\n";
    msg+="```";
    return message.channel.send(msg);
  });
}

client.on('ready', () => {
  console.log(`Logged in as ${client.user.tag}!`);
});

// Create an event listener for messages
client.on('message', message => {
  // if bot or not on #general then ignore
  if (message.author.bot || message.channel.name!=="general") {
    return;
  }
  // if user not administrator and not allowedRole reject
  if (!message.member.hasPermission("ADMINISTRATOR") && !member.roles.has(member.guild.roles.find(r => r.name === allowedRole)))
      return message.reply(`only admins and ${allowedRole} are allowed to perform commands!`)

  console.log(`#${message.channel.name} ${message.author.username}#${message.author.tag}> ${message.content}`);

  // if not start with our prefix then ignore
  if (!message.content.startsWith(prefix)) return;
  console.log(`processing command ${message.author.tag}`)

  const args = message.content.slice(prefix.length).split(/ +/);
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
    if (!member.roles.has(member.guild.roles.find(r => r.name === autoRole)))
    {
      member.addRole(member.guild.roles.find(r => r.name === autoRole));
      console.log(`added new member ${member} on ${autoRole}`);
    }
    const channel = member.guild.channels.find(ch => ch.name === 'general');
    channel.send(`Welcome to the server, ${member}!`).then((newMessage) => {newMessage.react(":wave:");});
});

client.on("presenceUpdate", (oldMember, newMember) => {
    if(oldMember.presence.status !== newMember.presence.status){
        console.log(`${newMember.user.username} is now ${newMember.presence.status}`);
    }
});

client.login(token);
