const ws = new WebSocket(`ws://echoctf.local:8888/ws?token=${window.wsToken}`);
const wsHandlers = {
  notification: onNotification,
  apiNotifications: apiNotifications,
  target: onTarget,
  //instance: onInstance,
  //private_target: onPrivateTarget
};

const wsIconTypes = {
  error: 'error',
  danger: 'not_interested',
  success: 'done',
  info: 'info',
  warning: 'warning',
};

ws.addEventListener("open", () => {
  console.log("Connected!");
});

ws.addEventListener("message", (event) => {
  console.log("Received:", event.data);
  let msg;
  try {
    msg = JSON.parse(event.data);
  } catch {
    return console.error("Invalid JSON:", event.data);
  }

  const handler = wsHandlers[msg.event];
  if (!handler) {
    console.warn("Unhandled event:", msg.event);
    return;
  }

  handler(msg);
});

ws.addEventListener("close", () => {
  console.log("Connection closed");
});

ws.addEventListener("error", (err) => {
  console.error("WebSocket error:", err);
});

function onTarget({payload}){
  targetUpdates(payload.id)
}

function onNotification({ payload }) {

  if (payload.type.startsWith('swal')) {
    swal.fire({ title: payload.title, html: payload.body, type: payload.type.replace('swal:', ''), showConfirmButton: true });
  }
  else {
    $.notify({
      id: "notifw"+Math.floor(Math.random() * 100),
      message: payload.title,
      icon: wsIconTypes[payload.type]
    }, {
      timer: "4000",
      type: payload.type,
    })
  }
  apiNotifications();
}
