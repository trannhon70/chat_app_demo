const express = require('express');
const http = require('http');
const socketIo = require('socket.io');
const cors = require('cors');

const app = express();
const server = http.createServer(app);
const io = socketIo(server, {
    cors: {
        origin: "*", // Hoặc cấu hình theo domain cụ thể
        methods: ["GET", "POST"],
        allowedHeaders: ["my-custom-header"],
        credentials: true
    }
});

const host = '127.0.0.1';
const port = 1337;

// Sử dụng cors middleware
app.use(cors());

// Cung cấp file HTML cho client
app.get('/', (req, res) => {
    res.sendFile(__dirname + '/index.html');
});

// Lắng nghe kết nối Socket.IO
io.on('connection', (socket) => {
    console.log('Một client đã kết nối');

    socket.on('message', (message) => {
        console.log('Tin nhắn nhận được:', message);
        // Phát lại tin nhắn cho tất cả clients
        io.emit('message', message);
    });

    socket.on('disconnect', () => {
        console.log('Một client đã ngắt kết nối');
    });
});

server.listen(port, host, () => {
    console.log(`Server đang lắng nghe trên ${host}:${port}`);
});
