<?php
session_start();
$host = 'localhost'; // Thay đổi nếu cần thiết
$dbname = 'chat_socket'; // Tên cơ sở dữ liệu
$user = 'root'; // Tên người dùng MySQL
$password = ''; // Mật khẩu MySQL

// Tạo kết nối
$mysqli = new mysqli($host, $user, $password, $dbname);
$mysqli->set_charset("utf8mb4");
// Kiểm tra kết nối
if ($mysqli->connect_error) {
    die('Kết nối không thành công: ' . $mysqli->connect_error);
}
$user_id = $_SESSION['user_id'];
$query = "SELECT id, username, name, online FROM users WHERE id != '$user_id'";

$result = $mysqli->query($query);

$users = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}


$mysqli->close();
?>

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
        <div class="row">
            <div class="col-3 bg-light nav-bar">
                <div class="navbar-title">Group chat</div>
                <?php foreach ($users as $user) : ?>
                    <div class="navbar-card">
                        <div class="navbar-card-title"><?php echo $user['name'] ?></div>
                        <div class="navbar-card-text">Trạng thái
                            <?php if ($user['online'] == 0) { ?>
                                <span class="navbar-card-text-ofline">
                                    offline <span></span> </span>
                            <?php } else { ?>
                                <span class="navbar-card-text-online">
                                    online <span></span> </span>
                            <?php } ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="col-9" style="padding-left: 0px;">
                <header class="bg-info header">
                    hello <?php echo $_SESSION['name']; ?>
                </header>
                <div class="chat-body">

                    <div class="chat-client">
                        Lorem ipsum dolor sit amet, consectetur adipisicing elit. Fuga, quibusdam. Voluptatibus ad totam quasi earum recusandae consequuntur, corporis, maiores doloremque pariatur quisquam officia eius, quam fugit? Fugiat accusamus facere dolorem.
                        <div class="chat-client-not">
                            người nhắn: <span>Nguyễn Văn A</span>
                        </div>
                    </div>
                    <div class="chat-container">
                        <div class="chat-user">
                            Lorem ipsum dolor sit amet, consectetur adipisicing elit. Fuga, quibusdam. Voluptatibus ad totam quasi earum recusandae consequuntur, corporis, maiores doloremque pariatur quisquam officia eius, quam fugit? Fugiat accusamus facere dolorem.
                            <div class="chat-user-not">
                                người nhắn: <span>Nguyễn Văn A</span>
                            </div>
                        </div>
                    </div>

                </div>
                <form id="chatForm" class="form-chat">
                    <input id="messageInput" placeholder="Nhập tin nhắn của bạn" type="text">
                    <button type="submit">gửi</button>
                </form>
            </div>
        </div>
    </main>

    <input style="display: none;" id="user_id" value="<?php echo  $_SESSION['user_id'] ?>"  />
                                   
   
    <input style="display: none;" id="name" value=" <?php echo  $_SESSION['name'] ?>   "  />
                            


    <script>
        const socket = io('http://127.0.0.1:1337');

        socket.on('connect', function() {
            console.log('Đã kết nối tới máy chủ Socket.IO');
        });

        socket.on('message', function(message) {
            const messagesDiv = document.getElementById('messages');
            // messagesDiv.innerHTML += `<p><strong>${message.sender_id}:</strong> ${message.message}</p>`;
        });

        socket.on('disconnect', function() {
            console.log('Đã đóng kết nối tới máy chủ Socket.IO');
        });

        const form = document.getElementById('chatForm');
        const userId = document.getElementById('user_id').value;
        const name = document.getElementById('name').value;
        console.log(userId);
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            const messageInput = document.getElementById('messageInput').value;

            const messageObj = {
                user_id: userId, 
                name: name, 
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