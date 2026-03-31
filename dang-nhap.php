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
                    echo "";
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
    echo "";
}

// Đóng kết nối
$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap">
    <title>Đăng nhập</title>
    <style>

            header {
                background-color: white;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                position: sticky;
                top: 0;
                z-index: 1000;
            }

            nav {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 10px 0;
            }

            .nav-left, .nav-right {
                display: flex;
                align-items: center;
            }

            nav a {
                text-decoration: none;
                color: #333;
                font-size: 16px;
                margin: 0 20px;
                position: relative;
                transition: transform 0.3s ease, box-shadow 0.3s ease;
            }

            nav a:hover {
                transform: translateY(-5px); /* Nâng lên khi hover */
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Bóng nhẹ khi hover */
            }

            .nav-left a {
                margin: 0 30px;
            }

            .logo {
                margin-left: 40px; /* Dịch sang bên phải */
            }

            .logo img {
                height: 50px;
            }

            .donate-button {
                background-color: #365486;
                color: white;
                padding: 10px 20px;
                border-radius: 5px;
                text-decoration: none;
                font-weight: bold;
            }

            .donate-button:hover {
                background-color: #d10000;
            }
        body {
            margin: 0;
            padding: 0;
            font-family: 'Roboto', sans-serif;
            height: 100vh;
            background: url('HDSD.jpg') no-repeat center center fixed;
            background-size: cover;
            position: relative;
        }

        /* Lớp phủ màu đen */
        body::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            z-index: 1;
            width: 115%;
            height: 150%;
        }

        /* Modal đăng nhập */
        .modal {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            z-index: 4;
            position: relative;
        }

        .modal-content {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            width: 400px;
            max-width: 90%;
            position: relative;
            z-index: 5;
        }

        .close {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 24px;
            cursor: pointer;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 15px;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        .error-message {
            color: red;
            margin-bottom: 15px;
            text-align: center;
        }

        /* Đưa modal lên trước nền background */
        .modal-content {
            z-index: 5;
        }
    </style>
</head>
<!-- Header -->
<header>
    <nav>
        <div class="logo">
            <a href="index.html"><img src="z5942590383156_5619bbf6d5490789903ade2929e71df9.jpg" alt="Give Now Logo"></a>
        </div>
        <div class="menu">
            <a href="index.html">Trang chủ</a>
              <a href="du-an.php">Dự án</a>
            <a href="ve-chung-toi.html">Về chúng tôi</a>
            <a href="huong-dan.html">Hướng dẫn</a>
        </div>
        <div class="auth">
            <a href="ung-ho-ngay.html" class="donate-button">Ủng hộ ngay</a>
            <a href="dang-nhap.php">Đăng nhập</a>
        </div>
    </nav>
</header>

<body>
  <!-- Modal đăng nhập -->
  <div id="loginModal" class="modal">
      <div class="modal-content">
          <span class="close">&times;</span>
          <h2>Đăng nhập</h2>
          <form action="dang-nhap.php" method="POST">
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

      // Xác thực form
       function validateForm() {
           var username = document.getElementById("username").value;
           var password = document.getElementById("password").value;
           var errorMessage = document.getElementById("errorMessage");

           // Giả lập kiểm tra mật khẩu
           if (username === "" || password !== "123456") { // Giả sử mật khẩu đúng là "123456"
               errorMessage.textContent = "Mật khẩu không chính xác!";
               errorMessage.style.display = "block";
               return false;
           } else {
               errorMessage.style.display = "none";
               return true;
           }
       }

       // Giữ lại modal khi nhập sai thông tin và cho phép thử lại
       document.getElementById("loginForm").addEventListener("submit", function(e) {
           if (!validateForm()) {
               e.preventDefault();
               modal.style.display = "flex";
           }
       });
  </script>
</body>
</html>
