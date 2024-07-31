const express = require('express');
const http = require('http');
const socketIo = require('socket.io');
const cors = require('cors');
const mysql = require('mysql2');

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

// Cấu hình kết nối MySQL
const db = mysql.createConnection({
    host: 'localhost',
    user: 'root',
    password: '', // Sử dụng mật khẩu trống
    database: 'chat_socket', // Tên cơ sở dữ liệu
    port: 3307 // Cổng MySQL
});

// Kết nối tới cơ sở dữ liệu MySQL
db.connect((err) => {
    if (err) {
        console.error('Lỗi kết nối tới cơ sở dữ liệu:', err);
        process.exit(1);
    }
    console.log('Đã kết nối tới cơ sở dữ liệu MySQL');
});

// Sử dụng cors middleware
app.use(cors());

// Cung cấp file HTML cho client
app.get('/', (req, res) => {
    res.sendFile(__dirname + '/index.html');
});

// Lắng nghe kết nối Socket.IO

io.on('connection', (socket) => {
    console.log('Một client đã kết nối');

    socket.on('message', (messages) => {
        console.log('Tin nhắn nhận được:', messages);

        // Lưu tin nhắn vào cơ sở dữ liệu
        const { user_id,username, message } = messages;
        const query = 'INSERT INTO messages (user_id, username, message) VALUES (?, ?, ?)';
        db.query(query, [user_id, username, message], (err, results) => {
            console.log(results,'re');
            if (err) {
                console.error('Lỗi khi lưu tin nhắn:', err);
            } else {
                console.log('Tin nhắn đã được lưu vào cơ sở dữ liệu');
            }
        });

        // // Phát lại tin nhắn cho tất cả clients
        io.emit('message', messages);
    });

    socket.on('disconnect', () => {
        console.log('Một client đã ngắt kết nối');
    });
});

const host = '127.0.0.1';
const port = 1337;

server.listen(port, host, () => {
    console.log(`Server đang lắng nghe trên ${host}:${port}`);
});
