

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Form with Socket.IO</title>
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://cdn.socket.io/4.1.3/socket.io.min.js"></script>
</head>

<body>
    <!-- <div id="messages"></div>
    <form id="chatForm">
        <input type="text" id="messageInput" placeholder="Nhập tin nhắn của bạn" required>
        <button type="submit">Gửi</button>
    </form> -->
    <main>
        <div class="row" >
        <div class="col-3 bg-light nav-bar" >
            <div class="navbar-title" >Group chat</div>
            <div class="navbar-card">
                <div>nguyễn văn a</div>
                <div>tình trạng <span>online <span></span> </span></div>
            </div>
        </div>
        <div class="col-9" style="padding-left: 0px;" >
            <header class="bg-info header"  >
                    hello bạn a
            </header>
            <div class="chat-body" >

            </div>
        </div>
        </div>
    </main>

    <script>
        const socket = io('http://127.0.0.1:1337');

        socket.on('connect', function() {
            console.log('Đã kết nối tới máy chủ Socket.IO');
        });

        socket.on('message', function(message) {
            const messagesDiv = document.getElementById('messages');
            messagesDiv.innerHTML += `<p><strong>${message.sender_id}:</strong> ${message.message}</p>`;
        });

        socket.on('disconnect', function() {
            console.log('Đã đóng kết nối tới máy chủ Socket.IO');
        });

        const form = document.getElementById('chatForm');
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            const messageInput = document.getElementById('messageInput').value;

            const messageObj = {
                sender_id: 1, // Thay đổi thành ID của người gửi
                receiver_id: 2, // Thay đổi thành ID của người nhận
                message: messageInput
            };

            socket.emit('message', messageObj);

            form.reset();
        });
    </script>

<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>
