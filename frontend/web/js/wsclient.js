const ws = new WebSocket(`ws://echoctf.local:8888/ws?token=${window.wsToken}`);

ws.addEventListener("open", () => {
  console.log("Connected!");
});

ws.addEventListener("message", (event) => {
  console.log("Received:", event.data);
});

ws.addEventListener("close", () => {
  console.log("Connection closed");
});

ws.addEventListener("error", (err) => {
  console.error("WebSocket error:", err);
});