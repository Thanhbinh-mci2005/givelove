<?php
// Kết nối đến cơ sở dữ liệu
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

// Truy vấn tổng số tiền quyên góp cho từng dự án
$amounts = [];
$project_ids = [1 => 'ngon-lua-yeu-thuong', 2 => 'buoc-chan-hi-vong', 3 => 'nha-binh-yen', 4 => 'hat-giong-tuong-lai'];
foreach ($project_ids as $id => $name) {
    $sql = "SELECT SUM(amount) as total_amount FROM donations WHERE id_program = $id";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $amounts[$name] = $row['total_amount'] ? number_format($row['total_amount'], 0, ',', '.') . 'đ' : '0đ';
    } else {
        $amounts[$name] = '0đ';
    }
}

// Truy vấn dữ liệu từ ba bảng 'donors', 'donations', và 'programs'
$sql = "SELECT donors.full_name, donors.address, donations.amount, programs.program_name
        FROM donors
        JOIN donations ON donors.id_donor = donations.id_donor
        JOIN programs ON donations.id_program = programs.id_program";

$result = $conn->query($sql);

// Kiểm tra xem truy vấn SQL có thực thi thành công hay không
if (!$result) {
    die("Lỗi truy vấn SQL: " . $conn->error);
}

// Truy vấn dữ liệu từ bảng 'expenditure'
$sql_expenditure = "SELECT expenditure.description, expenditure.spend, date_format(expenditure.date_expenditure, '%d-%m-%Y') as formatted_date, programs.program_name
                   FROM expenditure
                   JOIN programs ON expenditure.id_program = programs.id_program";
$result_expenditure = $conn->query($sql_expenditure);

// Kiểm tra xem truy vấn SQL có thực thi thành công hay không
if (!$result_expenditure) {
    die("Lỗi truy vấn SQL: " . $conn->error);
}
?>



<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dự án - GiveLove</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #365486;
            background-image: url('z5943017721602_d3675d96a7b51956f159abaf1cedd69e.jpg'); /* Đường dẫn đến ảnh nền */
            background-size: cover; /* Kích thước ảnh bao phủ toàn bộ trang */
            background-position: center; /* Căn giữa ảnh nền */
            background-repeat: no-repeat; /* Không lặp lại ảnh nền */
            background-attachment: fixed;
            position: relative; /* Để lớp phủ overlay nằm bên trên */
        }

        body::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5); /* Lớp phủ tối màu đen với độ trong suốt */
            z-index: 1; /* Đảm bảo lớp phủ nằm trên nền */
            pointer-events: none; /* Đảm bảo lớp phủ không cản trở các phần tử tương tác */
        }

        /* Đảm bảo nội dung chính nằm trên lớp overlay */
        .container, header, .banner, .project-section {
            position: relative;
            z-index: 2; /* Đảm bảo các nội dung phía trên lớp phủ */
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

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

        .banner {
            background-image: url('assets.jpg');
            background-size: cover;
            background-position: center;
            padding: 50px 0;
            text-align: center;
            color: white;
        }

        .banner h1 {
            font-size: 64px; /* Phóng to tiêu đề "Dự án đang gây quỹ" */
            text-shadow: 0 4px 6px rgba(0, 0, 0, 0.3); /* Đổ bóng như cũ */
            transition: transform 0.3s ease, text-shadow 0.3s ease; /* Hiệu ứng mượt khi hover */
        }

        .banner p {
            font-size: 18px;
            margin-bottom: 10px;
        }

        .project-section {
            padding: 40px 0;
            text-align: center;
        }

        .project-section h2 {
            font-size: 28px;
            margin-bottom: 20px;
        }

        .project-grid {
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }

        .project-item {
            flex: 1 1 calc(25% - 20px); /* Mỗi ô chiếm 25% chiều rộng trừ đi khoảng cách giữa chúng */
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); /* Hiệu ứng shadow mặc định */
            text-align: left;
            transition: transform 0.3s ease, box-shadow 0.3s ease; /* Hiệu ứng chuyển động mượt */
            position: relative;
            z-index: 1; /* Đảm bảo các ô nổi lên trên */
        }

        .project-item:hover {
            transform: translateY(-10px); /* Nâng lên khi hover */
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.2); /* Bóng mạnh hơn khi hover */
            z-index: 2; /* Tăng chỉ số z-index để nó nổi lên trên các ô khác */
        }

        .project-item img {
            max-width: 100%;
            border-radius: 10px;
        }

        .project-item h3 {
            font-size: 20px;
            margin: 20px 0 10px 0;
        }

        .project-item p {
            font-size: 16px;
            color: #555;
        }

        .project-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 10px 0;
        }

        .project-goal {
            font-size: 14px;
            color: #333;
        }

        .project-fund {
            font-size: 14px;
            color: #e74c3c;
            font-weight: bold;
        }

        .progress-bar {
            width: 100%;
            background-color: #ddd;
            height: 10px;
            border-radius: 5px;
            overflow: hidden;
            margin-bottom: 10px;
        }

        .progress {
            height: 100%;
            background-color: #e74c3c;
        }

        footer {
            background-color: #365486;
            color: white;
            text-align: center;
            padding: 20px 0;
            position: relative;
            z-index: 1000;
            width: 100%;
        }

        footer p {
            margin: 0;
        }

    </style>
</head>
<body>

  <!-- Header -->
  <header>
      <nav>
          <div class="logo">
            <img src="d4d68581eefc56a20fed.jpg" alt="Logo">
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

    <!-- Banner -->
    <div class="banner">
        <div class="container">
            <h1>Dự Án Đang Gây Quỹ</h1>
        </div>
    </div>

    <!-- Dự án -->
    <div class="project-section">
        <div class="container">
            <div class="project-grid">
                <div class="project-item">
                    <a href="ngon-lua-yeu-thuong.html" style="text-decoration: none; color: inherit;">
                        <img src="80a7f6464434fc6aa525.jpg" alt="Ngọn Lửa Yêu Thương">
                        <h3 style="text-align:center;">
                          <span style="color: #365486;">"Ngọn Lửa Yêu Thương"</span> <br>
                          Quỹ Hỗ Trợ Khẩn Cấp
                        </h3>
                        <div class="project-info">
                            <p class="project-goal"><br>Mục tiêu: 3.000.000.000đ</p>
                            <p class="project-fund"><br>Đã đạt: <?php echo $amounts['ngon-lua-yeu-thuong']; ?></p>
                        </div>
                        <p>Chung tay thắp sáng ngọn lửa yêu thương, giúp những người chịu ảnh hưởng sau bão Yagi vượt qua khó khăn.</p>
                    </a>
                </div>
                <div class="project-item">
                    <a href="buoc-chan-hi-vong.html" style="text-decoration: none; color: inherit;">
                      <img src="1841087f960d2e53771c.jpg" alt="Bước Chân Hi Vọng">
                        <h3 style="text-align:center">
                          <span style="color: #365486;">"Bước Chân Hi Vọng" </span> <br> Quỹ Hỗ Trợ Y Tế
                        </h3>
                        <div class="project-info">
                            <p class="project-goal"><br>Mục tiêu: 2.500.000.000đ</p>
                            <p class="project-fund"><br>Đã đạt: <?php echo $amounts['buoc-chan-hi-vong']; ?></p>
                        </div>
                        <p>Một bước chân nhỏ của bạn có thể tạo nên hy vọng lớn cho những ai đang cần được chăm sóc y tế sau bão.</p>
                    </a>
                </div>
                <div class="project-item">
                    <a href="nha-binh-yen.html" style="text-decoration: none; color: inherit;">
                        <img src="0c98c7815df3e5adbce2.jpg" alt="Nhà Bình Yên">
                        <h3 style="text-align:center">
                            <span style="color: #365486;">"Nhà Bình Yên" </span> <br> Quỹ Hỗ Trợ Tái Thiết <br> Nhà Ở
                        </h3>
                        <div class="project-info">
                            <p class="project-goal">Mục tiêu: 10.000.000.000đ</p>
                            <p class="project-fund">Đã đạt: <?php echo $amounts['nha-binh-yen']; ?></p>
                        </div>
                        <p>Góp một viên gạch yêu thương, xây dựng lại những mái ấm bình yên cho các gia đình sau bão.</p>
                    </a>
                </div>
                <div class="project-item">
                    <a href="hat-giong-tuong-lai.html" style="text-decoration: none; color: inherit;">
                        <img src="873df8506f22d77c8e33.jpg" alt="Hạt Giống Tương Lai">
                        <h3 style="text-align:center">
                            <span style="color: #365486;">"Hạt Giống Tương Lai" </span> <br> Quỹ Hỗ Trợ Tái Thiết <br> Nông Nghiệp
                        </h3>
                        <div class="project-info">
                            <p class="project-goal">Mục tiêu: 5.000.000.000đ</p>
                            <p class="project-fund">Đã đạt: <?php echo $amounts['hat-giong-tuong-lai']; ?></p>
                        </div>
                        <p>Gieo hy vọng hôm nay, thu hoạch tương lai tươi sáng cho những người nông dân đang hồi sinh sau cơn bão.</p>
                    </a>
                </div>
            </div>
        </div>
    </div>

            <!--Table-->
                  <style>
                  .table-container {
                    background-color: white; /* Nền trắng cho toàn bộ nội dung bảng */
                    padding: 20px; /* Khoảng cách bên trong giữa bảng và viền của div */
                    margin-bottom: 50px; /* Khoảng cách giữa bảng và phần footer */
                    border-radius: 10px; /* Bo tròn các góc */
                    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); /* Hiệu ứng bóng */
                  }
                table, th, td {
                border:1px solid black;
                background-color: white; /* Màu nền trắng cho toàn bộ bảng */
                }
                </style>
                <div class="container">
                <div class="table-container">
                <h2 style = "text-align: center; color: #365486; font-size: 50px ">DANH SÁCH MẠNH THƯỜNG QUÂN</h2>
                <table style="width:100%">
                <?php
                // Kiểm tra xem có kết quả trả về không
                if ($result->num_rows > 0) {
                    // Hiển thị bảng HTML với dữ liệu từ cơ sở dữ liệu
                    echo '<table style="width:100%">
                            <tr>
                                <th style="width: 40%; color: #365486; font-size: 15px">Họ và tên</th>
                                <th style="width: 20%; color: #365486; font-size: 15px">Địa chỉ</th>
                                <th style="width: 20%; color: #365486; font-size: 15px">Số tiền ủng hộ</th>
                                <th style="width: 30%; color: #365486; font-size: 15px">Tên chương trình ủng hộ</th>
                            </tr>';

                    // Lặp qua kết quả và hiển thị từng dòng dữ liệu
                    while($row = $result->fetch_assoc()) {
                        echo '<tr style="text-align: center ">
                                <td>' . $row['full_name'] . '</td>
                                <td>' . $row['address'] . '</td>
                                <td>' . $row['amount'] . '</td>
                                <td>' . $row['program_name'] . '</td>
                              </tr>';
                    }
                    echo '</table>';
                } else {
                    echo "Không có dữ liệu";
                }
                ?>
                </table>
              </div>
                </div>

                  <!--Table CHI TIÊU-->
                <style>
                .table-container {
                  background-color: white; /* Nền trắng cho toàn bộ nội dung bảng */
                  padding: 20px; /* Khoảng cách bên trong giữa bảng và viền của div */
                  margin-bottom: 50px; /* Khoảng cách giữa bảng và phần footer */
                  border-radius: 10px; /* Bo tròn các góc */
                  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); /* Hiệu ứng bóng */
                }
                table, th, td {
                border:1px solid black;
                background-color: white; /* Màu nền trắng cho toàn bộ bảng */
                }
                </style>
                <div class="container">
                <div class="table-container">
                <h2 style = "text-align: center; color: #365486; font-size: 50px ">DANH MỤC CHI TIÊU QUỸ</h2>
                <table style="width:100%">
                <?php
                // Kiểm tra xem có kết quả trả về không
                if ($result_expenditure->num_rows > 0) {
                  // Hiển thị bảng HTML với dữ liệu từ cơ sở dữ liệu
                  echo '<table style="width:100%">
                          <tr>
                              <th style="width: 40%; color: #365486; font-size: 15px">Khoản chi tiêu</th>
                              <th style="width: 20%; color: #365486; font-size: 15px">Tên chương trình chi têu</th>
                              <th style="width: 20%; color: #365486; font-size: 15px">Số tiền</th>
                              <th style="width: 30%; color: #365486; font-size: 15px">Ngày chi tiêu</th>
                          </tr>';

                  // Lặp qua kết quả và hiển thị từng dòng dữ liệu
                  while($row = $result_expenditure->fetch_assoc()) {
                      echo '<tr style="text-align: center ">
                              <td>' . $row['description'] . '</td>
                              <td>' . $row['program_name'] . '</td>
                              <td>' . $row['spend'] . '</td>
                              <td>' . $row['formatted_date'] . '</td>
                            </tr>';
                  }
                  echo '</table>';
                } else {
                  echo "Không có dữ liệu";
                }

                // Đóng kết nối
                $conn->close();
                ?>
                </table>
                </div>
                </div>
    <!-- Footer -->
    <footer style="background-color: #365486; padding: 40px 20px; color: white; font-family: Arial, sans-serif;">
      <div class="container" style="max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: flex-start;">

          <!-- Logo và mô tả -->
          <div class="footer-left" style="flex: 1;">
          <h4 style="margin-bottom: 20px; font-size: 40px; margin-left: 50px">TRAO YÊU THƯƠNG<br>NHẬN HY VỌNG!</h4>
          </div>

          <!-- Thông tin liên hệ -->
          <div class="footer-middle" style="flex: 1; text-align: left; margin-left: 130px"> <!-- Chỉnh sửa đây để căn lề phải -->
              <h4 style="margin-bottom: 10px; font-size: 20px;">Liên hệ với chúng tôi</h4>
              <p style="margin-top: 5px; font-size: 18px; line-height: 1.8;">
                <strong>Hotline:</strong> <a href="tel:0352430981" style="color: #ffffff; text-decoration: none;">0352430981</a><br>
                <strong>Email:</strong> <a href="mailto:hotro@givelove.vn" style="color: white; text-decoration: none;">hotro@givelove.vn</a><br>
                <strong>Địa chỉ:</strong> Số 30, Tạ Quang Bửu, Bách Khoa, Hai Bà Trưng, Hà Nội
              </p>
          </div>
      </div>

      <!-- Dòng bản quyền -->
      <div style="text-align: center; margin-top: 0px; font-size: 14px;">
          <p><br>
            <br>
            &copy; 2024 GiveLove.vn – All Rights Reserved</p>
      </div>
  </footer>

</body>
</html>
