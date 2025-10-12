<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "registration";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['action'])) {
  $action = $_GET['action'];

  if ($action === 'get') {
    $sql = "SELECT * FROM users ORDER BY id DESC";
    $result = $conn->query($sql);
    $users = [];
    while ($row = $result->fetch_assoc()) {
      $users[] = $row;
    }
    echo json_encode($users);
    exit;
  }

  if ($action === 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $check = $conn->prepare("SELECT id FROM users WHERE id = ?");
    $check->bind_param("i", $id);
    $check->execute();
    $check_result = $check->get_result();
    $check->close();

    if ($check_result->num_rows > 0) {
      $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
      $stmt->bind_param("i", $id);
      $stmt->execute();
      $stmt->close();
    }
    exit;
  }

  if ($action === 'edit') {
    $data = json_decode(file_get_contents("php://input"), true);
    $id = intval($data['id']);
    $first = $conn->real_escape_string($data['first_name']);
    $last = $conn->real_escape_string($data['last_name']);
    $email = $conn->real_escape_string($data['email']);
    $dob = $conn->real_escape_string($data['dob']);
    $address = $conn->real_escape_string($data['address']);

    $stmt = $conn->prepare("UPDATE users SET first_name=?, last_name=?, email=?, dob=?, address=? WHERE id=?");
    $stmt->bind_param("sssssi", $first, $last, $email, $dob, $address, $id);
    $stmt->execute();
    $stmt->close();
    exit;
  }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $first = $_POST['first_name'];
  $last = $_POST['last_name'];
  $email = $_POST['email'];
  $password = $_POST['password'];
  $dob = $_POST['dob'];
  $address = $_POST['address'];

  $hashed_password = password_hash($password, PASSWORD_DEFAULT);

  $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, dob, address) VALUES (?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("ssssss", $first, $last, $email, $hashed_password, $dob, $address);

  if ($stmt->execute()) {
    header("Location: index.html?success=1");
  } else {
    $error = urlencode("Error: " . $stmt->error);
    header("Location: index.html?error=$error");
  }
  $stmt->close();
}

$conn->close();
?>
