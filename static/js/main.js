// The wake lock sentinel.
let wakeLock = null;

// Function that attempts to request a wake lock.
const requestWakeLock = async () => {
  try {
    wakeLock = await navigator.wakeLock.request('screen');
  } catch (err) {
  }
};

$(function() {
    requestWakeLock();
});

const handleVisibilityChange = async () => {
  if (wakeLock !== null && document.visibilityState === 'visible') {
    await requestWakeLock();
  }
};
document.addEventListener('visibilitychange', handleVisibilityChange);

function clickGenPassword(popuptitle, popuptext, popupbutton) {
  let pass = generatePassword(30);
  $('input#password').val(pass);
  $('input#password_confirmation').val(pass);
  let copy = copyTextToClipboard(pass);
  
  let text = copy ? popuptext : pass;
  $.alert({
        title: popuptitle,
        content: popuptext,
        escapeKey: 'cancel',
        backgroundDismiss: true,
        onOpenBefore: function () {
            //$('body').addClass('blockkeyactions');
        },
        onClose: function () {
            //$('body').removeClass('blockkeyactions');
        },
        buttons: {
            cancel: {
                text: popupbutton
            }
        }
    });
}
