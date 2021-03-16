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
yii.confirm = function (message, okCallback, cancelCallback) {
    swal({
        title: message,
        type: 'warning',
        showCancelButton: true,
        closeOnConfirm: true,
        allowOutsideClick: true
    }, okCallback);
};
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
});
