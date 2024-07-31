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

if( $_SERVER['REQUEST_METHOD'] === 'POST' &&  isset($_POST['submit'])){
    $userID = $_POST['user_id'];
    $updateQuery = "UPDATE users SET online = 0 WHERE id = '$userID'";
    if ($mysqli->query($updateQuery)) {
        echo "Cập nhật thành công!";
    } else {
        echo "Lỗi khi cập nhật: " . $mysqli->error;
    }
    session_destroy();
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$query = "SELECT id, username, name, online FROM users WHERE id != '$user_id'";
$queryMessage = "SELECT id, username, user_id, message, time FROM messages ";

$result = $mysqli->query($query);
$resultMessage = $mysqli->query($queryMessage);

$users = [];
$message_list = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

if ($resultMessage) {
    while ($rowMes = $resultMessage->fetch_assoc()) {
        $message_list[] = $rowMes;
    }
}


$mysqli->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <meta http-equiv="refresh" content="10"> -->
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
                    <form style="padding-left: 20px;" method="post" >
                        <input style="display: none;"  name="user_id" value="<?php echo  $_SESSION['user_id'] ?>" />
                        <button type="submit" name="submit" >Đăng xuất</button>
                    </form>
                </header>
                <div class="chat-body" id="messages-group">
                    <?php foreach ($message_list as $user) : ?>
                        <?php if ($user['user_id'] !== $_SESSION['user_id']) { ?>
                            <div style="display: flex; align-items: center; justify-content: flex-start; ">
                                <div class="chat-client">
                                   <div class="chat-client-message" > <?php echo $user['message'] ?></div>
                                    <div class="chat-client-not">
                                        người nhắn: <span><?php echo $user['username'] ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php } else { ?>
                            <div class="chat-container">
                                <div class="chat-user">
                                <div class="chat-client-message" > <?php echo $user['message'] ?></div>
                                    <div class="chat-user-not">
                                        người nhắn: <span><?php echo $user['username'] ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    <?php endforeach; ?>


                </div>
                <form id="chatForm" class="form-chat">
                    <input id="messageInput" placeholder="Nhập tin nhắn của bạn" type="text">
                    <button type="submit">gửi</button>
                </form>
            </div>
        </div>
    </main>

    <input style="display: none;" id="user_id" value="<?php echo  $_SESSION['user_id'] ?>" />


    <input style="display: none;" id="name" value=" <?php echo  $_SESSION['name'] ?>   " />



    <script>
        const userId = document.getElementById('user_id').value;
        const name = document.getElementById('name').value;
        const socket = io('http://127.0.0.1:1337');

        socket.on('connect', function() {
            console.log('Đã kết nối tới máy chủ Socket.IO');
        });

        socket.on('message', function(messages) {
            const messagesDiv = document.getElementById('messages-group');
            let newMessage;
            if (messages.user_id === userId) {
                newMessage = `<div class="chat-container new-message">
                        <div class="chat-user">
                        <div class="chat-client-message" >
                                ${messages.message}
                                </div>
                            <div class="chat-user-not">
                                người nhắn: <span>${messages.username}</span>
                            </div>
                        </div>
                      </div>`;
            } else {
                newMessage = `<div style="display: flex; align-items: center; justify-content: flex-start;" class="new-message">
                        <div class="chat-client">
                        <div class="chat-client-message" >
                        ${messages.message}
                        </div>
                            <div class="chat-client-not">
                                người nhắn: <span>${messages.username}</span>
                            </div>
                        </div>
                      </div>`;
            }

            messagesDiv.innerHTML += newMessage;

            const lastMessage = document.querySelector('.new-message:last-child');
            if (lastMessage) {
                lastMessage.scrollIntoView({
                    behavior: 'smooth'
                });
                lastMessage.classList.remove('new-message'); // Xóa class để tránh cuộn lại khi có tin nhắn mới
            }
        });


        socket.on('disconnect', function() {
            console.log('Đã đóng kết nối tới máy chủ Socket.IO');
        });

        const form = document.getElementById('chatForm');

        console.log(userId);
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            const messageInput = document.getElementById('messageInput').value;

            const messageObj = {
                user_id: userId,
                username: name,
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