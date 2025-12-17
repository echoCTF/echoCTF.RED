let ws;
let reconnectAttempts = 0;
let reconnectTimeout = null;

const MAX_RECONNECT_DELAY = 30000; // 30s
const BASE_DELAY = 1000;           // 1s

const wsHandlers = {
  notification: onNotification,
  apiNotifications: apiNotifications,
  target: onTarget,
};

const wsIconTypes = {
  error: 'error',
  danger: 'not_interested',
  success: 'done',
  info: 'info',
  warning: 'warning',
};

function connectWS() {
  if (ws && (ws.readyState === WebSocket.OPEN || ws.readyState === WebSocket.CONNECTING)) {
    return;
  }

  //console.log("Connecting WebSocket...");

  ws = new WebSocket(`ws://echoctf.local:8888/ws?token=${window.wsToken}`);

  ws.addEventListener("open", () => {
    console.log("WebSocket Connected!");
    reconnectAttempts = 0;
  });

  ws.addEventListener("message", (event) => {
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
    scheduleReconnect();
  });

  ws.addEventListener("error", (err) => {
    console.error("WebSocket error:", err);
    ws.close(); // ensure close event fires
  });
}

function scheduleReconnect() {
  if (reconnectTimeout) return;

  const delay = Math.min(
    BASE_DELAY * Math.pow(2, reconnectAttempts),
    MAX_RECONNECT_DELAY
  );

  console.log(`Reconnecting in ${delay}ms...`);
  reconnectAttempts++;

  reconnectTimeout = setTimeout(() => {
    reconnectTimeout = null;
    connectWS();
  }, delay);
}

/* ---- handlers ---- */

function onTarget({ payload }) {
  targetUpdates(payload.id);
}

function onNotification({ payload }) {
  if (payload.type.startsWith('swal')) {
    swal.fire({
      title: payload.title,
      html: payload.body,
      type: payload.type.replace('swal:', ''),
      showConfirmButton: true
    });
  } else {
    $.notify({
      id: "notifw" + Math.floor(Math.random() * 100),
      message: payload.title,
      icon: wsIconTypes[payload.type]
    }, {
      timer: "4000",
      type: payload.type,
    });
  }
}

/* ---- start connection ---- */
connectWS();
