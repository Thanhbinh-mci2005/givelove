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

// Kiểm tra nếu form đã gửi đi với phương thức POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Lấy thông tin đăng nhập từ form và kiểm tra xem chúng có tồn tại không
    $entered_username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $entered_password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if (!empty($entered_username) && !empty($entered_password)) {
        // Truy vấn để lấy thông tin người dùng từ bảng users
        $sql = "SELECT * FROM users WHERE user_name = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("s", $entered_username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // Lấy dòng dữ liệu đầu tiên từ kết quả
                $row = $result->fetch_assoc();

                // So sánh mật khẩu trực tiếp (không mã hóa)
                if ($entered_password === $row['password']) {
                    // Đăng nhập thành công
                    echo "Đăng nhập thành công!";
                    // Điều hướng tới trang donationscus.php hoặc trang quản trị
                    header("Location: donor_form.html");
                    exit;
                } else {
                    // Mật khẩu không chính xác
                    echo "Mật khẩu không chính xác!";
                }
            } else {
                // Không tìm thấy tên đăng nhập
                echo "ID người dùng không tồn tại!";
            }
        } else {
            echo "Lỗi chuẩn bị truy vấn SQL: " . $conn->error;
        }
    } else {
        echo "Vui lòng nhập tên đăng nhập và mật khẩu!";
    }
} else {
    echo "Form không được gửi đúng cách!";
}

// Đóng kết nối
$conn->close();
?>

<!-- Modal đăng nhập -->
<div id="loginModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Đăng nhập</h2>
        <form action="process_login.php" method="POST">
            <div class="form-group">
                <label for="username">Tên đăng nhập:</label>
                <input type="text" id="username" name="username" placeholder="Nhập tên đăng nhập" required>
            </div>
            <div class="form-group">
                <label for="password">Mật khẩu:</label>
                <input type="password" id="password" name="password" placeholder="Nhập mật khẩu" required>
            </div>
            <input type="submit" value="Đăng nhập">
        </form>
    </div>
</div> -->

<!-- Script để điều khiển modal đăng nhập -->
<script>
    // Lấy modal
    var modal = document.getElementById("loginModal");

    // Lấy nút mở modal
    var btn = document.querySelector(".auth");

    // Lấy phần đóng modal
    var span = document.getElementsByClassName("close")[0];

    // Khi người dùng nhấn vào nút, mở modal
    btn.onclick = function() {
        modal.style.display = "block";
    }

    // Khi người dùng nhấn vào dấu X, đóng modal
    span.onclick = function() {
        modal.style.display = "none";
    }

    // Khi người dùng nhấn ra ngoài modal, đóng modal
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>
