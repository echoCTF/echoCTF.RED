/* Avoid passive event warnings */
//jQuery.event.special.touchstart = {
//  setup: function( _, ns, handle ) {
//      this.addEventListener("touchstart", handle, { passive: !ns.includes("noPreventDefault") });
//  }
//};

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
  swal({
    title: 'Are you sure?',
    text: message,
    type: 'warning',
    showConfirmButton: true,
    showCancelButton: true,
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

jQuery( document ).ready(function() {
  $('#claim-flag').on('pjax:success', function(event) {
          window.location.reload();
  });
  //showTime();
});
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
