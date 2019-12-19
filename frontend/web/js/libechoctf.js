window.cookieconsent.initialise({
  "palette": {
    "popup": {
      "background": "#000",
      "text": "#94c11f"
    },
    "button": {
      "background": "#94c11f"
    }
  },
  "position": "bottom",
  "theme": "classic",
  "content": {
    "message": "echoCTF RED needs cookies to operate."
  }
});

jQuery( document ).ready(function() {
  $.fn.selectpicker.Constructor.BootstrapVersion = '4';
  $('#profile-avatar').change(function() {
    if($(this).val()!="")
      $('#preview_avatar').attr('src','/images/avatars/'+$(this).val());
  });

  /* Fetch Notifications on click without prevent default */
  $('#navbarDropdownMenuLink').on('click',function(){
      /* Only proceeed if we are about to display the notifications */
      if ($(this).attr('aria-expanded') !== "false")
      return;
      $('#notificationsMenu').html('');
      $.get($(this).attr('href'),processNotifications);
  });

  /* Fetch Notifications on click without prevent default */
  $('#navbarHintsDropDown').on('click',function(){
      /* Only proceeed if we are about to display the notifications */
      if ($(this).attr('aria-expanded') !== "false") return;
      $('#hintsMenu').html('');
      $.get($(this).attr('href'),processHints);
  });

  function processHints( data,textStatus,jqXHR )
  {
    if(data.length==0)
    {
      $('#hintsMenu').prepend('<p class="dropdown-item">nothing here yet</p>');
      return;
    }

    for(i=0;i<data.length;i++)
    {
      var thelink = $('<a />',{
          class: "dropdown-item",
          text: data[i].title,
          title: data[i].title,
          href: '#'
        });
        if(parseInt(data[i].status)===1)
        {
          thelink.addClass("text-success");
          thelink.prepend('<i class="material-icons">whatshot</i> ');
        }
        thelink.appendTo('#hintsMenu');
    }
    total_pages=parseInt( jqXHR.getResponseHeader('X-Pagination-Page-Count'), 0 );
    current_page=parseInt( jqXHR.getResponseHeader('X-Pagination-Current-Page'), 0 );
    per_page=parseInt( jqXHR.getResponseHeader('X-Pagination-Per-Page'), 0 );
    total_recs=parseInt( jqXHR.getResponseHeader('X-Pagination-Total-Count'), 0 );
    if(total_pages>current_page)
    {
      var theLink=$('<a>',{
        class: 'dropdown-item',
        text: "Load More",
        href: $('#navbarHintsDropDown').attr('href')+'?playerHint-page='+parseInt(current_page+1)
      })
      .on('click',function(){
        $(this).remove();
        $.get($(this).attr('href'),processHints);
        return false;
      })
      .appendTo('#gintsMenu');
    }
  }
  /* Generic Notifications Handler */
  function processNotifications( data,textStatus,jqXHR )
  {
    if(data.length==0)
    {
      $('#notificationsMenu').prepend('<p class="dropdown-item">nothing here yet</p>');
      return;
    }
    for(i=0;i<data.length;i++)
    {
      var thelink = $('<a>',{
          class: "dropdown-item",
          text: data[i].title,
          title: data[i].title,
          href: '#'
        });
      if(parseInt(data[i].archived)===0)
      {
        thelink.addClass("text-success");
        thelink.prepend('<i class="material-icons">whatshot</i> ');
      }
      thelink.appendTo('#notificationsMenu');
    }
    total_pages=parseInt( jqXHR.getResponseHeader('X-Pagination-Page-Count'), 0 );
    current_page=parseInt( jqXHR.getResponseHeader('X-Pagination-Current-Page'), 0 );
    per_page=parseInt( jqXHR.getResponseHeader('X-Pagination-Per-Page'), 0 );
    total_recs=parseInt( jqXHR.getResponseHeader('X-Pagination-Total-Count'), 0 );
    if(total_pages>current_page)
    {
      var theLink=$('<a>',{
        class: 'dropdown-item',
        text: "Load More",
        href: $('#navbarDropdownMenuLink').attr('href')+'?notifications-page='+parseInt(current_page+1)
      })
      .on('click',function(){
        $(this).remove();
        $.get($(this).attr('href'),processNotifications);
        return false;
      })
      .appendTo('#notificationsMenu');
    }
  }
});
