<?php
session_start();
if (!isset($_SESSION['email'])) {
  header('Location: login.php');
}
var_dump($_SESSION);
?>

<!DOCTYPE html>
<html>

<head>
  <title>Dashboard</title>
</head>

<body>
  <p>Please wait for the teachers' announcement</p>
  <form method="post" action="logout.php">
    <button type="submit">Logout</button>
  </form>
</body>

</html>