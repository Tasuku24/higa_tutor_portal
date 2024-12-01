<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require('db.php');

$errors = [];
$email = $password = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $email = $_POST['email'];
  $password = $_POST['password'];

  // Check in tutors table
  $query = "SELECT * FROM tutors WHERE email = ?";
  $stmt = $conn->prepare($query);
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $userType = 'tutor';
  } else {
    // Check in students table
    $query = "SELECT * FROM students WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
      $user = $result->fetch_assoc();
      $userType = 'student';
    } else {
      $errors[] = "Invalid email or password!";
    }
  }

  if (empty($errors)) {
    if (isset($user['PASSWORD']) && isset($user['salt'])) {
      $hashedPassword = $user['PASSWORD'];
      $salt = $user['salt'];
      if (password_verify($password . $salt, $hashedPassword)) {
        $_SESSION['email'] = $email;
        $_SESSION['user_type'] = $userType;
        header('Location: home.php');
      } else {
        $errors[] = "Invalid email or password!";
      }
    } else {
      $errors[] = "Invalid email or password!";
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - HiGA Tutor Portal</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <h1 class="title">Login to HiGA Tutor Portal</h1>
  <?php if (!empty($errors)): ?>
    <div class="error-messages">
      <?php foreach ($errors as $error): ?>
        <p><?php echo htmlspecialchars($error); ?></p>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
  <form method="post">
    <input type="text" name="email" id="email" placeholder="Email" value="<?php echo htmlspecialchars($email); ?>" required>
    <input type="password" name="password" id="password" placeholder="Password" required>
    <input type="submit" value="Login">
  </form>
  <p class="signup-link">
    Don't have an account? <a href="signup.php">Sign Up</a>
  </p>
</body>

</html>