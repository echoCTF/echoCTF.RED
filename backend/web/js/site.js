
$(function () {
  $("[data-toggle='tooltip']").tooltip();
  $("[data-toggle='popover']").popover();

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

/*
 * Sideways (fly-out) submenus for nested navbar dropdowns.
 *
 * Bootstrap 5 has no nested dropdown support: a click on a nested toggle
 * closes its parent menu. We take over the nested toggles so that submenus
 * open next to their parent entry (on hover, focus or click) on the expanded
 * navbar and inline on the collapsed one. Styling lives in css/site.css.
 */
document.addEventListener('DOMContentLoaded', function () {
  var expanded = window.matchMedia('(min-width: 1200px)');

  function closeAll(scope, except) {
    scope.querySelectorAll('.has-submenu > .dropdown-menu.show').forEach(function (menu) {
      if (except && menu.contains(except)) return;
      menu.classList.remove('show');
      var t = menu.parentElement.querySelector(':scope > .dropdown-toggle');
      if (t) t.setAttribute('aria-expanded', 'false');
    });
  }

  // flip the submenu to the left when it would run off the right edge
  function place(wrap, submenu) {
    wrap.classList.remove('open-left');
    if (!expanded.matches) return;
    if (submenu.getBoundingClientRect().right > document.documentElement.clientWidth)
      wrap.classList.add('open-left');
  }

  document.querySelectorAll('.navbar .dropdown-menu .dropdown-menu').forEach(function (submenu) {
    var wrap = submenu.parentElement;
    var toggle = wrap.querySelector(':scope > .dropdown-toggle');
    if (!toggle) return;
    var parentMenu = wrap.parentElement.closest('.dropdown-menu');

    wrap.classList.add('has-submenu');
    // keep bootstrap away from the nested toggle
    toggle.removeAttribute('data-bs-toggle');
    toggle.setAttribute('aria-haspopup', 'true');
    toggle.setAttribute('aria-expanded', 'false');
    if (!toggle.hasAttribute('href')) toggle.setAttribute('tabindex', '0');

    function toggleSubmenu(e) {
      e.preventDefault();
      // do not let the click reach bootstrap, it would close the parent menu
      e.stopPropagation();
      var open = !submenu.classList.contains('show');
      closeAll(parentMenu, open ? submenu : null);
      submenu.classList.toggle('show', open);
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
      if (open) place(wrap, submenu);
    }

    toggle.addEventListener('click', toggleSubmenu);
    toggle.addEventListener('keydown', function (e) {
      if (e.key === 'Enter' || e.key === ' ') toggleSubmenu(e);
    });
    wrap.addEventListener('mouseenter', function () {
      if (!expanded.matches) return;
      // a submenu pinned open by a click must not overlap the hovered one
      closeAll(parentMenu, submenu);
      place(wrap, submenu);
    });
    wrap.addEventListener('focusin', function () {
      place(wrap, submenu);
    });
  });

  // closing a top level menu also closes whatever was opened inside it
  document.addEventListener('hidden.bs.dropdown', function (e) {
    var top = e.target.closest ? e.target.closest('.dropdown, .nav-item') : null;
    if (top) closeAll(top);
  });
});
