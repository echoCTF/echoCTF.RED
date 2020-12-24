function escapeHtml(unsafe) {
    return unsafe
         .replace(/&/g, "&amp;")
         .replace(/</g, "&lt;")
         .replace(/>/g, "&gt;")
         .replace(/"/g, "&quot;")
         .replace(/'/g, "&#039;");
 }
function luminanace(r, g, b) {
    var a = [r, g, b].map(function (v) {
        v /= 255;
        return v <= 0.03928
            ? v / 12.92
            : Math.pow( (v + 0.055) / 1.055, 2.4 );
    });
    return a[0] * 0.2126 + a[1] * 0.7152 + a[2] * 0.0722;
}
function contrast(rgb1, rgb2) {
    return (luminanace(rgb1[0], rgb1[1], rgb1[2]) + 0.05)
         / (luminanace(rgb2[0], rgb2[1], rgb2[2]) + 0.05);
}
//contrast([255, 255, 255], [255, 255, 0]); // 1.074 for yellow
jQuery( document ).ready(function() {
  $('#profile-avatar').change(function() {
    if($(this).val()!="")
      $('#preview_avatar').attr('src','/images/avatars/'+$(this).val());
  });
  $('#claim-flag').on('pjax:success', function(event) {
          window.location.reload();
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
        //thelink.append('<span>'+data[i].message+'</span>');
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
      .appendTo('#hintsMenu');
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
        thelink.html(data[i].body);
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
