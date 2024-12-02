<?php
session_start();
if (!isset($_SESSION['email'])) {
  header('Location: login.php');
}
?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <h1 class="title"><span>HiGA Tutor Portal</span> (for <?php echo ucfirst($_SESSION["user_type"]) ?>)</h1>
  <h2 class="wait">Please wait for the teachers' announcement</h2>
  <form method="post" action="logout.php">
    <button type="submit" class="logout">Logout</button>
  </form>
</body>

</html>
