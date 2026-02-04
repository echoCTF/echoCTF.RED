
$(function () {
  $("[data-toggle='tooltip']").tooltip();
  $("[data-toggle='popover']").popover();
});
$(document).ready(function () {

  var ticks = 0;

  var x = setInterval(function () {
    ticks++;

    if (typeof countDownDate === 'undefined')
      return;

    if (countDownDate === 0) {
      clearInterval(x);
      return;
    }

    // Server-based time, tick-driven
    var timeNow = countDownNow + (ticks * 1000);

    var distance = countDownDate - timeNow;
    var element = document.getElementById("event_countdown");
    var msg = "The competition ends in: <span>";

    if (countDownStart > 0 && countDownStart > timeNow) {
      distance = countDownStart - timeNow;
      msg = "The competition starts in: <span>";
    }

    if (distance < 0) {
      clearInterval(x);
      if (element)
        element.innerHTML = 'The competition is <b class="text-danger text-bold">finished</b>';
      return;
    }

    var days = Math.floor(distance / (1000 * 60 * 60 * 24));
    var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    var seconds = Math.floor((distance % (1000 * 60)) / 1000);

    if (element) {
      if (days > 0)
        element.innerHTML = msg + days + "d " + hours + "h " + minutes + "m " + seconds + "s</span>";
      else if (hours > 0)
        element.innerHTML = msg + hours + "h " + minutes + "m " + seconds + "s</span>";
      else if (minutes > 0)
        element.innerHTML = msg + minutes + "m " + seconds + "s</span>";
      else
        element.innerHTML = msg + seconds + "s</span>";
    }
  }, 1000);
});