<?php
// Kết nối đến cơ sở dữ liệu
$servername = "localhost";
$username = "nopcv_3"; // Thay đổi nếu bạn dùng tài khoản khác
$password = "cRBXERBs"; // Nhập mật khẩu nếu có
$dbname = "nopcv_3"; // Tên cơ sở dữ liệu của bạn

$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}


    // Kiểm tra xem các biến POST có tồn tại không
    if (isset($_POST['description'], $_POST['spend'], $_POST['id_program'], $_POST['date_expenditure'])) {
        // Lấy dữ liệu từ form và xử lý để tránh SQL Injection
        $description = $conn->real_escape_string($_POST['description']);
        $spend = $conn->real_escape_string($_POST['spend']);
        $id_program = $conn->real_escape_string($_POST['id_program']);
        $date_expenditure = $conn->real_escape_string($_POST['date_expenditure']);

        // Kiểm tra xem các giá trị không bị rỗng
        if (empty($description) || empty($spend) || empty($id_program) || empty($date_expenditure)) {
            die("Vui lòng điền đầy đủ thông tin.");
        }

        // Tạo câu lệnh SQL để chèn dữ liệu vào bảng 'expenditure'
        $sql_expenditure = "INSERT INTO expenditure (description, spend, id_program, date_expenditure) VALUES ('$description', '$spend', '$id_program', '$date_expenditure')";

        // Thực thi câu lệnh SQL
        if ($conn->query($sql_expenditure) === TRUE) {
            echo '
            <!DOCTYPE html>
            <html lang="vi">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Xác nhận chi tiêu</title>
                <style>
                    body {
                        font-family: Parisienne, sans-serif;
                        background-image: url("965b5c1ffe40461e1f51.jpg");
                        background-size: cover;
                        background-position: center;
                        background-repeat: no-repeat;
                        background-attachment: fixed;
                        height: 100vh;
                        margin: 0;
                        display: flex;
                        flex-direction: column;
                        justify-content: center;
                        align-items: center;
                        text-align: center;
                        color: white;
                    }
                    h1 {
                        font-size: 48px;
                        margin-bottom: 20px;
                    }
                    p {
                        font-size: 24px;
                        margin-bottom: 30px;
                    }
                    .btn-home {
                        padding: 15px 30px;
                        background-color: #365486;
                        color: white;
                        border: none;
                        border-radius: 5px;
                        text-decoration: none;
                        font-size: 18px;
                        position: absolute;
                        bottom: 50px;
                    }
                    .btn-home:hover {
                        background-color: #2e4a6d;
                    }
                </style>
            </head>
             <body>
            <h1></h1>
            <a class="btn-home" href="expenditure-form.html">Quay lại</a>
            </body>
            </html>';
        } else {
            echo "Lỗi khi thêm vào bảng expenditure: " . $conn->error;
        }
    } else {
        die("Thiếu dữ liệu đầu vào.");
    }

    // Đóng kết nối
    $conn->close();
    ?>
