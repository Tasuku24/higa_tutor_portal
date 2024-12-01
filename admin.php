<?php
session_start();

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $username = $_POST['username'];
  $password = $_POST['password'];

  // Check credentials
  if ($username === 'admin' && $password === 'test') {
    $_SESSION['admin_logged_in'] = true;
    header('Location: admin.php');
    exit;
  } else {
    $error = "Invalid username or password";
  }
}

// Handle logout
if (isset($_POST['logout'])) {
  session_destroy();
  header('Location: admin.php');
  exit;
}

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
?>
  <!DOCTYPE html>
  <html lang="en">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - HiGA Tutor Portal</title>
    <link rel="stylesheet" href="style.css">
  </head>

  <body>
    <h1 class="title">Admin Login - HiGA Tutor Portal</h1>
    <?php if (isset($error)): ?>
      <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <form method="POST" action="admin.php">
      <label for="username">Username:</label>
      <input type="text" id="username" name="username" required>
      <br>
      <label for="password">Password:</label>
      <input type="password" id="password" name="password" required>
      <br>
      <button type="submit">Login</button>
    </form>
    <a href="./index.php">Go Back</a>
  </body>

  </html>
<?php
  exit;
}

// Fetch all users
require('db.php');
$query = "SELECT email, name, 'tutor' as user_type FROM tutors UNION SELECT email, name, 'student' as user_type FROM students";
$result = $conn->query($query);

if ($result->num_rows > 0) {
  $users = $result->fetch_all(MYSQLI_ASSOC);
} else {
  $users = [];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin - HiGA Tutor Portal</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <h1 class="title">Admin Panel - HiGA Tutor Portal</h1>
  <h2>All Users</h2>
  <table>
    <thead>
      <tr>
        <th>Email</th>
        <th>Name</th>
        <th>User Type</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($users as $user): ?>
        <tr>
          <td><?php echo htmlspecialchars($user['email']); ?></td>
          <td><?php echo htmlspecialchars($user['name']); ?></td>
          <td><?php echo htmlspecialchars($user['user_type']); ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php
  // Fetch all tutors
  $tutorQuery = "SELECT * FROM tutors";
  $tutorResult = $conn->query($tutorQuery);
  $tutors = $tutorResult->num_rows > 0 ? $tutorResult->fetch_all(MYSQLI_ASSOC) : [];

  // Fetch all students
  $studentQuery = "SELECT * FROM students";
  $studentResult = $conn->query($studentQuery);
  $students = $studentResult->num_rows > 0 ? $studentResult->fetch_all(MYSQLI_ASSOC) : [];

  // Find matching pairs
  $matchingPairs = [];

  foreach ($tutors as $tutor) {
    $tutorSubjects = array_map('trim', explode(',', $tutor['subjects']));
    foreach ($students as $student) {
      $studentSubjects = array_map('trim', explode(',', $student['subjects']));
      $commonSubjects = array_intersect($tutorSubjects, $studentSubjects);

      // Check for language compatibility
      $languageMatch = ($tutor['preferred_language'] === 'both' || $student['preferred_language'] === 'both') ||
        ($tutor['preferred_language'] === $student['preferred_language']);

      // Check for university compatibility
      $universityMatch = ($tutor['university_choice'] === 'both' || $student['university_choice'] === 'both') ||
        ($tutor['university_choice'] === $student['university_choice']);

      if (
        !empty($commonSubjects) &&
        $languageMatch &&
        $universityMatch
      ) {
        $matchingPairs[] = [
          'tutor_name' => $tutor['NAME'],
          'student_name' => $student['NAME'],
          'student_grade' => $student['grade'],
          'common_subjects' => implode(', ', $commonSubjects),
          'preferred_language' => $tutor['preferred_language'] === 'both' ? $student['preferred_language'] : $tutor['preferred_language'],
          'university_choice' => $tutor['university_choice'] === 'both' ? $student['university_choice'] : $tutor['university_choice']
        ];
      }
    }
  }
  ?>

  <h2>Matching Tutor-Student Pairs</h2>
  <table>
    <thead>
      <tr>
        <th>Tutor Name</th>
        <th>Student Name</th>
        <th>Student Grade</th>
        <th>Common Subjects</th>
        <th>Preferred Language</th>
        <th>University Choice</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($matchingPairs)): ?>
        <?php foreach ($matchingPairs as $pair): ?>
          <tr>
            <td><?php echo htmlspecialchars($pair['tutor_name']); ?></td>
            <td><?php echo htmlspecialchars($pair['student_name']); ?></td>
            <td><?php echo htmlspecialchars($pair['student_grade']); ?></td>
            <td><?php echo htmlspecialchars($pair['common_subjects']); ?></td>
            <td><?php echo htmlspecialchars($pair['preferred_language']); ?></td>
            <td><?php echo htmlspecialchars($pair['university_choice']); ?></td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="7">No matching pairs found.</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
  <a href="./login.php">Go Back</a>
</body>

</html>