// Extend with ifexists for checking existing elements
$.fn.extend({
  'ifexists': function (callback) {
      if (this.length > 0) {
          return callback($(this));
      }
  }
});

/* Dummy escapeHtml implementation */
function escapeHtml(unsafe)
{
    return unsafe
         .replace(/&/g, "&amp;")
         .replace(/</g, "&lt;")
         .replace(/>/g, "&gt;")
         .replace(/"/g, "&quot;")
         .replace(/'/g, "&#039;");
}

//
function isFileImage(file) {
  const acceptedImageTypes = ['image/png'];

  return file && acceptedImageTypes.includes(file['type'])
}

/* Calculate luminanace */
function luminanace(r, g, b)
{
    var a = [r, g, b].map(function (v) {
        v /= 255;
        return v <= 0.03928
            ? v / 12.92
            : Math.pow( (v + 0.055) / 1.055, 2.4 );
    });
    return a[0] * 0.2126 + a[1] * 0.7152 + a[2] * 0.0722;
}
/**
 * Override the default yii confirm dialog. This function is
 * called by yii when a confirmation is requested.
 *
 * @param string message the message to display
 * @param string ok callback triggered when confirmation is true
 * @param string cancelCallback callback triggered when cancelled
 */
yii.confirm = function (message, okCallback, cancelCallback) {
  var title='Are you sure?';
  var swType='warning';
  var showCancelButton=true;
  if($(this).attr('data-title') !== 'undefined' && $(this).attr('data-title')!== false && $(this).attr('data-title') !== undefined)
  {
    title=$(this).attr('data-title')+'?';
  }

  if($(this).attr('data-swType') !== 'undefined' && $(this).attr('data-swType')!== false && $(this).attr('data-swType') !== undefined)
  {
    swType=$(this).attr('data-swType');
  }
  if($(this).attr('data-showCancelButton') !== 'undefined' && $(this).attr('data-showCancelButton')!== false && $(this).attr('data-showCancelButton') !== undefined && $(this).attr('data-showCancelButton') == 'false' )
  {
    showCancelButton=false;
  }

  swal({
    title: title,
    text: message,
    type: swType,
    showConfirmButton: true,
    showCancelButton: showCancelButton,
  }).then((action) => {
    if (action.value) {
      okCallback()
    }
  });
}
/*
 * Generate contrast between two rgb values
 * contrast([255, 255, 255], [255, 255, 0]); // 1.074 for yellow
 */
function contrast(rgb1, rgb2) {
    return (luminanace(rgb1[0], rgb1[1], rgb1[2]) + 0.05)
         / (luminanace(rgb2[0], rgb2[1], rgb2[2]) + 0.05);
}

function showTime(){

    var date = new Date();
    var h = date.getUTCHours(); // 0 - 23
    var m = date.getUTCMinutes(); // 0 - 59
    var s = date.getUTCSeconds(); // 0 - 59
    var session="";
    h = (h < 10) ? "0" + h : h;
    m = (m < 10) ? "0" + m : m;
    s = (s < 10) ? "0" + s : s;
    session = (h < 12) ? "AM" : "";

    var time = h + ":" + m;
    document.getElementById("time").innerText = time;
    document.getElementById("time").textContent = time;

    setTimeout(showTime, 1000);

}

jQuery( document ).ready(function() {

  $(".card-nav-tabs .nav-tabs .nav-item:nth-of-type(1) .nav-link").addClass('active');
  $(".tab-content .tab-pane:nth-of-type(1)").addClass('active');
  $(".markdown img").addClass("img-fluid");
  $('.markdown a').attr('target','_blank');
  $('#claim-flag').on('pjax:success', function(event) {
          window.location.reload();
  });

  if(document.getElementById('signupform-password'))
  {
    const capscheck=document.getElementById("signupform-password");
    capscheck.addEventListener( 'keydown', function( event ) {
      var caps = event.getModifierState && event.getModifierState( 'CapsLock' );
      if(caps)
        $('#form-signup').yiiActiveForm('updateAttribute', 'signupform-password', ['Caps Lock is on!']);
      else
        $('#form-signup').yiiActiveForm('updateAttribute', 'signupform-password', '');

    });
  }

  $(".copy-to-clipboard").click(function(e){
    e.preventDefault();
    e.stopPropagation();

    const el = document.createElement('textarea');
    el.value = $(this).attr('href');
    el.setAttribute('readonly', '');
    el.style.position = 'absolute';
    el.style.left = '-9999px';
    document.body.appendChild(el);
    el.select();
    document.execCommand('copy');
    document.body.removeChild(el);
    if($(this).parent('.dropdown-menu') && $(this).parent('.dropdown-menu').selectpicker)
      $(this).parent('.dropdown-menu').selectpicker('toggle');
    if($(this).attr('swal-data'))
      return Swal.fire($(this).attr('swal-data'));

    return false;
  })

  if(document.getElementById('writeup-content'))
  {
    const textarea=document.getElementById("writeup-content");
    var converter = new showdown.Converter({
        omitExtraWLInCodeBlocks: true,
        headerLevelStart: 2,
        parseImgDimensions: true,
        ghCodeBlocks: true,
        simplifiedAutoLink: true,
        tables: true,
        tasklists: true,
        simpleLineBreaks: true,
        openLinksInNewWindow: true,
        emoji: true,
        splitAdjacentBlockquotes: true,
      });
    converter.setFlavor('github');
    document.getElementById("markdown-preview").innerHTML=converter.makeHtml(textarea.value);
    textarea.addEventListener( 'keyup', function( event ) {
      var text      = textarea.value,
          html      = converter.makeHtml(text);
      document.getElementById("markdown-preview").innerHTML=html;
    });
  }

  //showTime();
});

$('#Notifications, #Hints').on('hide.bs.dropdown', function() {
  const curId=$(this).attr('id')
  clearDropdownCounters(curId)
})

function clearDropdownCounters(curId){
// on close remove the pill
  $('#'+curId+'>a>span').remove();
  // remove the text-primary
  const el = document.querySelector('#'+curId+'>a>i');
  el.classList.remove("text-primary");
}

var notifTimeout;
var intervalTimeout=5000

function apiNotifications(){
  notifTimeout=setInterval(function () {
    var request = new XMLHttpRequest();
    request.open("GET", "/api/notification");
    request.setRequestHeader('X-Requested-With', 'XMLHttpRequest')
    request.send();

    request.onreadystatechange = function () {
      if (this.readyState == 4 && this.status == 200) {
          jsonObj=JSON.parse(this.responseText)['items'];
          for(i=0;i<jsonObj.length;i++)
          {
            const record=jsonObj[i];
            if(record.category.startsWith('swal'))
            {
              if(!swal.isVisible())
              {
                swal.fire({ title: record.title, text: record.body, type: record.category.replace('swal:',''), showConfirmButton: true});
              }
            }
            else {
              $.notify({
                id:"notifw"+record.id,
                message:record.title,
                icon:"done"
              },{
                timer:"4000",
                type:record.category,
                })
            }
          }
          if(jsonObj.length>0) clearDropdownCounters('Notifications')
      }
    }
  }, intervalTimeout);
}

$(document).ready(function(){
  $('#Notifications, #Hints').ifexists(function(elem) {
    document.addEventListener('visibilitychange', function(e) {
      if (document.visibilityState === 'hidden') {
        clearTimeout(notifTimeout);
      }
      else
      {
        clearTimeout(notifTimeout);
        // clear any existing ones
        $('#Notifications, #Hints').ifexists(function(elem) { apiNotifications(); })
      }
    });
    apiNotifications();
  })
})
