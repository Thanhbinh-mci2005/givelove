<?php
// Kết nối đến cơ sở dữ liệu MySQL
$servername = "localhost";
$username = "nopcv_3"; // Thay đổi nếu bạn dùng tài khoản khác
$password = "cRBXERBs"; // Nhập mật khẩu nếu có
$dbname = "nopcv_3"; // Tên cơ sở dữ liệu của bạn

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Kiểm tra xem các biến POST có tồn tại không
if (isset($_POST['full_name'], $_POST['phone_number'], $_POST['email'], $_POST['address'], $_POST['amount'], $_POST['program'], $_POST['date'])) {
    // Lấy dữ liệu từ form và xử lý để tránh SQL Injection
    $full_name = $conn->real_escape_string($_POST['full_name']);
    $phone_number = $conn->real_escape_string($_POST['phone_number']);
    $email = $conn->real_escape_string($_POST['email']);
    $address = $conn->real_escape_string($_POST['address']);
    $amount = $conn->real_escape_string($_POST['amount']);
    $program = $conn->real_escape_string($_POST['program']);
    $date = $conn->real_escape_string($_POST['date']);

    // Kiểm tra xem các giá trị không bị rỗng
    if (empty($full_name) || empty($phone_number) || empty($email) || empty($address) || empty($amount) || empty($program) || empty($date)) {
        die("Vui lòng điền đầy đủ thông tin.");
    }

    // Tạo câu lệnh SQL để chèn dữ liệu vào bảng 'donors'
    $sql_donor = "INSERT INTO donors (full_name, email, phone_number, address) VALUES ('$full_name', '$email','$phone_number','$address')";
    if ($conn->query($sql_donor) === TRUE) {
        // Lấy ID của donor vừa thêm vào
        $id_donor = $conn->insert_id;
        // Chèn thông tin vào bảng donations
        $sql_donations = "INSERT INTO donations (id_donor, id_program, amount, date_donor) VALUES ('$id_donor', '$program', '$amount', '$date')";
        // Thực thi câu lệnh SQL
        if ($conn->query($sql_donations) === TRUE) {
            echo '
            <!DOCTYPE html>
            <html lang="vi">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Xác nhận ủng hộ</title>
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
            <a class="btn-home" href="donor_form.html">Quay lại</a>
            </body>
            </html>';
        } else {
            echo "Lỗi khi thêm vào bảng donations: " . $conn->error;
        }
    } else {
        echo "Lỗi khi thêm vào bảng donors: " . $conn->error;
    }
} else {
    die("Thiếu dữ liệu đầu vào.");
}

// Đóng kết nối
$conn->close();
?>
