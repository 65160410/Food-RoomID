const express = require("express");
const http = require("http");
const { Server } = require("socket.io");

const app = express();
const server = http.createServer(app);
const io = new Server(server, {
  cors: {
    origin: "http://127.0.0.1:5500", 
    methods: ["GET", "POST"]
  }
});

io.on("connection", (socket) => {
  console.log("A user connected");
  // จัดการ event ต่างๆ ที่นี่
});

server.listen(3000, () => {
  console.log("Socket.IO server is running on port 3000");
});
