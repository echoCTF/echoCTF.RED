var twit = require('twit');
var config = require('./config.js');
var T = new twit({
  consumer_key:         config.consumer_key,
  consumer_secret:     config.consumer_secret,
  access_token:         config.access_token,
  access_token_secret:  config.access_token_secret,
});

function followed(eventMessage){
  var name = eventMessage.source.name;
  var screenName = eventMessage.source.screen_name;
  console.log(`@${screenName} followed us`);
}
//var personalStream = T.stream('follow',followed)
//var stream = T.stream('statuses/filter', { track: 'echoCTF' })
//stream.on('tweet', function (tweet) {
//  console.log(tweet)
//})
var sample = T.stream('statuses/filter', { track: 'echoCTF' })

sample.on('tweet', function (tweet) {
  console.log('sample: ',tweet)
})
T.get('followers/list', function(error, tweets, response) {
  if(error) console.log(error);
  tweets.users.forEach(follower => {
    console.log(follower.screen_name);
  });
});
