<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "registration";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $first = $_POST['first_name'];
  $last = $_POST['last_name'];
  $email = $_POST['email'];
  $pass = $_POST['password'];
  $dob = $_POST['dob'];

  $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);

  $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, dob) VALUES (?, ?, ?, ?, ?)");
  $stmt->bind_param("sssss", $first, $last, $email, $hashed_pass, $dob);

  if ($stmt->execute()) {
    header("Location: " . $_SERVER['PHP_SELF'] . "?success=1");
    exit();
  } else {
    header("Location: " . $_SERVER['PHP_SELF'] . "?error=" . urlencode($conn->error));
    exit();
  }
  
  $stmt->close();
}

$conn->close();

if (isset($_GET['success'])) {
  $message = "<p style='color:green; text-align:center;'>Registration successful!</p>";
} elseif (isset($_GET['error'])) {
  $message = "<p style='color:red; text-align:center;'>Error: " . htmlspecialchars($_GET['error']) . "</p>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="box form-box">
            <header>Registration</header>
            <form action="" method="post">
                <div class="field input">
                    <label for="first_name">First Name</label>
                    <input type="text" name="first_name" id="first_name" required>
                </div>

                <div class="field input">
                    <label for="last_name">Last Name</label>
                    <input type="text" id="last_name" name="last_name" required>
                </div>

                <div class="field input">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="field input">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="field input">
                    <label for="dob">Date of Birth</label>
                    <input type="date" id="dob" name="dob" required>
                </div>

                <div class="field">
                    <input type="submit" name="submit" value="Register" class="btn submit">
                </div>
            </form>
            
            <?php if (!empty($message)) : ?>
                <div class="message">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>