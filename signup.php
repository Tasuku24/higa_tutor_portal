<!-- 生徒登録とチューター登録の表示切り替えとか頼む -->
<?php

// 流れ的には
// 1. チューター or 生徒を選択
// 2. 基本情報を入力
// 3. 科目を選択

error_reporting(E_ALL);
ini_set('display_errors', 1);
// error list
// 1. パスワードが一致していない場合のエラー表示
// 2. メールアドレスがすでに登録されている場合のエラー表示
// 3. チューター登録の場合、教科が選択されていない場合のエラー表示
// ってのをjavascriptとかでお願い
session_start();
if (isset($_SESSION['email'])) {
  header('Location: home.php');
  exit;
}
$errors = [];
$email = $password_input = $confirmPassword = $name = $sex = $preferredLanguage = $universityChoice = $grade = "";
$subjects = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $email = $_POST['email'];
  $password_input = $_POST['password'];
  $confirmPassword = $_POST['confirm-password'];
  $name = $_POST['name'];
  $sex = $_POST['sex'];
  $preferredLanguage = $_POST['preferred-language'];
  $universityChoice = $_POST['university-choice'];
  if (isset($_POST['subjects']))
    $subjects = $_POST['subjects'];

  if ($password_input != $confirmPassword) {
    $errors[] = "Passwords do not match!";
  }

  $flag = true;

  require('db.php');
  $query = "SELECT * FROM tutors WHERE email = ?";
  $stmt = $conn->prepare($query);
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();
  if ($result->num_rows > 0) {
    $errors[] = "Email already exists!";
    $flag = false;
  }

  $query = "SELECT * FROM students WHERE email = ?";
  $stmt = $conn->prepare($query);
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();
  if ($result->num_rows > 0) {
    $errors[] = "Email already exists!";
    $flag = false;
  }

  $salt = bin2hex(random_bytes(32));
  $hashedPassword = password_hash($password_input . $salt, PASSWORD_DEFAULT);

  if (isset($_POST['signup_as_tutor']) && $flag) {
    if (empty($_POST['subjects'])) {
      $errors[] = "Please select at least one subject!";
    } else {
      $subjectsList = implode(',', $subjects);
    }
    $query = "INSERT INTO tutors (email, password, salt, name, sex, preferred_language, university_choice, subjects) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssssss", $email, $hashedPassword, $salt, $name, $sex, $preferredLanguage, $universityChoice, $subjectsList);
  } elseif (isset($_POST['signup_as_student']) && $flag) {
    $grade = $_POST['grade'];
    $query = "INSERT INTO students (email, password, salt, name, sex, grade, preferred_language, university_choice) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt->prepare($query);
    $stmt->bind_param("ssssssss", $email, $hashedPassword, $salt, $name, $sex, $grade, $preferredLanguage, $universityChoice);
  }

  if ($stmt->execute() && empty($errors)) {
    $_SESSION['email'] = $email;
    $_SESSION['user_type'] = isset($_POST['signup_as_tutor']) ? 'tutor' : 'student';
    header('Location: home.php');
    exit;
  } else if (empty($errors)) {
    $errors[] = "An error occurred!";
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>HiGA Tutor Portal</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <h1 class="title">HiGA Tutor Portal</h1>
  <!-- エラー表示 -->
  <?php if (!empty($errors)): ?>
    <div class="error-messages">
      <?php foreach ($errors as $error): ?>
        <p><?php echo htmlspecialchars($error); ?></p>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
  <!-- チューター登録 -->
  <form method="post">
    <div class="subjects">
      <label for="subjects">Your Subjects:</label>
      <div class="subject-group">
        <h3>Mathematics</h3>
        <div>
          <input type="checkbox" name="subjects[]" id="maths-aa-sl" value="maths-aa-sl" <?php echo in_array('maths-aa-sl', $subjects) ? 'checked' : ''; ?>>
          <label for="maths-aa-sl">Mathematics: Analysis and Approaches SL</label>
          <input type="checkbox" name="subjects[]" id="maths-aa-hl" value="maths-aa-hl" <?php echo in_array('maths-aa-hl', $subjects) ? 'checked' : ''; ?>>
          <label for="maths-aa-hl">Mathematics: Analysis and Approaches HL</label>
          <input type="checkbox" name="subjects[]" id="maths-ai-sl" value="maths-ai-sl" <?php echo in_array('maths-ai-sl', $subjects) ? 'checked' : ''; ?>>
          <label for="maths-ai-sl">Mathematics: Applications and Interpretation SL</label>
          <input type="checkbox" name="subjects[]" id="maths-ai-hl" value="maths-ai-hl" <?php echo in_array('maths-ai-hl', $subjects) ? 'checked' : ''; ?>>
          <label for="maths-ai-hl">Mathematics: Applications and Interpretation HL</label>
        </div>
      </div>
      <div class="subject-group">
        <h3>Sciences</h3>
        <div>
          <input type="checkbox" name="subjects[]" id="biology-sl" value="biology-sl" <?php echo in_array('biology-sl', $subjects) ? 'checked' : ''; ?>>
          <label for="biology-sl">Biology SL</label>
          <input type="checkbox" name="subjects[]" id="biology-hl" value="biology-hl" <?php echo in_array('biology-hl', $subjects) ? 'checked' : ''; ?>>
          <label for="biology-hl">Biology HL</label>
          <input type="checkbox" name="subjects[]" id="chemistry-sl" value="chemistry-sl" <?php echo in_array('chemistry-sl', $subjects) ? 'checked' : ''; ?>>
          <label for="chemistry-sl">Chemistry SL</label>
          <input type="checkbox" name="subjects[]" id="chemistry-hl" value="chemistry-hl" <?php echo in_array('chemistry-hl', $subjects) ? 'checked' : ''; ?>>
          <label for="chemistry-hl">Chemistry HL</label>
          <input type="checkbox" name="subjects[]" id="physics-sl" value="physics-sl" <?php echo in_array('physics-sl', $subjects) ? 'checked' : ''; ?>>
          <label for="physics-sl">Physics SL</label>
          <input type="checkbox" name="subjects[]" id="physics-hl" value="physics-hl" <?php echo in_array('physics-hl', $subjects) ? 'checked' : ''; ?>>
          <label for="physics-hl">Physics HL</label>
        </div>
      </div>
      <div class="subject-group">
        <h3>Languages</h3>
        <div>
          <input type="checkbox" name="subjects[]" id="english-a-sl" value="english-a-sl" <?php echo in_array('english-a-sl', $subjects) ? 'checked' : ''; ?>>
          <label for="english-a-sl">English A SL</label>
          <input type="checkbox" name="subjects[]" id="english-a-hl" value="english-a-hl" <?php echo in_array('english-a-hl', $subjects) ? 'checked' : ''; ?>>
          <label for="english-a-hl">English A HL</label>
          <input type="checkbox" name="subjects[]" id="japanese-a-sl" value="japanese-a-sl" <?php echo in_array('japanese-a-sl', $subjects) ? 'checked' : ''; ?>>
          <label for="japanese-a-sl">Japanese A SL</label>
          <input type="checkbox" name="subjects[]" id="japanese-a-hl" value="japanese-a-hl" <?php echo in_array('japanese-a-hl', $subjects) ? 'checked' : ''; ?>>
          <label for="japanese-a-hl">Japanese A HL</label>
          <input type="checkbox" name="subjects[]" id="english-b-sl" value="english-b-sl" <?php echo in_array('english-b-sl', $subjects) ? 'checked' : ''; ?>>
          <label for="english-b-sl">English B SL</label>
          <input type="checkbox" name="subjects[]" id="english-b-hl" value="english-b-hl" <?php echo in_array('english-b-hl', $subjects) ? 'checked' : ''; ?>>
          <label for="english-b-hl">English B HL</label>
          <input type="checkbox" name="subjects[]" id="japanese-b-sl" value="japanese-b-sl" <?php echo in_array('japanese-b-sl', $subjects) ? 'checked' : ''; ?>>
          <label for="japanese-b-sl">Japanese B SL</label>
          <input type="checkbox" name="subjects[]" id="japanese-b-hl" value="japanese-b-hl" <?php echo in_array('japanese-b-hl', $subjects) ? 'checked' : ''; ?>>
          <label for="japanese-b-hl">Japanese B HL</label>
        </div>
      </div>
      <div class="subject-group">
        <h3>Humanities</h3>
        <div>
          <input type="checkbox" name="subjects[]" id="history-sl" value="history-sl" <?php echo in_array('history-sl', $subjects) ? 'checked' : ''; ?>>
          <label for="history-sl">History SL</label>
          <input type="checkbox" name="subjects[]" id="history-hl" value="history-hl" <?php echo in_array('history-hl', $subjects) ? 'checked' : ''; ?>>
          <label for="history-hl">History HL</label>
          <input type="checkbox" name="subjects[]" id="geography-sl" value="geography-sl" <?php echo in_array('geography-sl', $subjects) ? 'checked' : ''; ?>>
          <label for="geography-sl">Geography SL</label>
          <input type="checkbox" name="subjects[]" id="geography-hl" value="geography-hl" <?php echo in_array('geography-hl', $subjects) ? 'checked' : ''; ?>>
          <label for="geography-hl">Geography HL</label>
        </div>
      </div>
      <div class="subject-group">
        <h3>Arts</h3>
        <div>
          <input type="checkbox" name="subjects[]" id="film-sl" value="film-sl" <?php echo in_array('film-sl', $subjects) ? 'checked' : ''; ?>>
          <label for="film-sl">Film SL</label>
          <input type="checkbox" name="subjects[]" id="music-sl" value="music-sl" <?php echo in_array('music-sl', $subjects) ? 'checked' : ''; ?>>
          <label for="music-sl">Music SL</label>
        </div>
      </div>
    </div>
    <input type="text" name="email" id="email" placeholder="Email" value="<?php echo htmlspecialchars($email); ?>" required>
    <input type="password" name="password" id="password" placeholder="Password" required>
    <input type="password" name="confirm-password" id="confirm-password" placeholder="Confirm Password" required>
    <input type="text" name="name" id="name" placeholder="Name" value="<?php echo htmlspecialchars($name); ?>" required>
    <select name="sex" id="sex" required>
      <option value="" disabled <?php echo empty($sex) ? 'selected' : ''; ?>>Select Sex</option>
      <option value="male" <?php echo $sex == 'male' ? 'selected' : ''; ?>>Male</option>
      <option value="female" <?php echo $sex == 'female' ? 'selected' : ''; ?>>Female</option>
      <option value="other" <?php echo $sex == 'other' ? 'selected' : ''; ?>>Other</option>
    </select>
    <select name="preferred-language" id="preferred-language" required>
      <option value="" disabled <?php echo empty($preferredLanguage) ? 'selected' : ''; ?>>Select Preferred Language</option>
      <option value="english" <?php echo $preferredLanguage == 'english' ? 'selected' : ''; ?>>English</option>
      <option value="japanese" <?php echo $preferredLanguage == 'japanese' ? 'selected' : ''; ?>>Japanese</option>
      <option value="both" <?php echo $preferredLanguage == 'both' ? 'selected' : ''; ?>>Both</option>
    </select>
    <select name="university-choice" id="university-choice" required>
      <option value="" disabled <?php echo empty($universityChoice) ? 'selected' : ''; ?>>Select University Choice</option>
      <option value="abroad" <?php echo $universityChoice == 'abroad' ? 'selected' : ''; ?>>Abroad</option>
      <option value="domestic" <?php echo $universityChoice == 'domestic' ? 'selected' : ''; ?>>Domestic</option>
      <option value="both" <?php echo $universityChoice == 'both' ? 'selected' : ''; ?>>Both</option>
    </select>
    <input type="submit" name="signup_as_tutor" value="Sign Up as Tutor" id="signup_as_tutor">

    <!-- 生徒登録 -->
    <input type="text" name="email" id="email" placeholder="Email" value="<?php echo htmlspecialchars($email); ?>" required>
    <input type="password" name="password" id="password" placeholder="Password" required>
    <input type="password" name="confirm-password" id="confirm-password" placeholder="Confirm Password" required>
    <input type="text" name="name" id="name" placeholder="Name" value="<?php echo htmlspecialchars($name); ?>" required>
    <select name="sex" id="sex" required>
      <option value="" disabled <?php echo empty($sex) ? 'selected' : ''; ?>>Select Sex</option>
      <option value="male" <?php echo $sex == 'male' ? 'selected' : ''; ?>>Male</option>
      <option value="female" <?php echo $sex == 'female' ? 'selected' : ''; ?>>Female</option>
      <option value="other" <?php echo $sex == 'other' ? 'selected' : ''; ?>>Other</option>
    </select>
    <select name="grade" id="grade" required>
      <option value="" disabled <?php echo empty($grade) ? 'selected' : ''; ?>>Select Grade</option>
      <option value="grade9" <?php echo $grade == 'grade9' ? 'selected' : ''; ?>>Grade 9</option>
      <option value="grade10" <?php echo $grade == 'grade10' ? 'selected' : ''; ?>>Grade 10</option>
      <option value="grade11" <?php echo $grade == 'grade11' ? 'selected' : ''; ?>>Grade 11</option>
      <option value="grade12" <?php echo $grade == 'grade12' ? 'selected' : ''; ?>>Grade 12</option>
    </select>
    <select name="preferred-language" id="preferred-language" required>
      <option value="" disabled <?php echo empty($preferredLanguage) ? 'selected' : ''; ?>>Select Preferred Language</option>
      <option value="english" <?php echo $preferredLanguage == 'english' ? 'selected' : ''; ?>>English</option>
      <option value="japanese" <?php echo $preferredLanguage == 'japanese' ? 'selected' : ''; ?>>Japanese</option>
      <option value="both" <?php echo $preferredLanguage == 'both' ? 'selected' : ''; ?>>Both</option>
    </select>
    <select name="university-choice" id="university-choice" required>
      <option value="" disabled <?php echo empty($universityChoice) ? 'selected' : ''; ?>>Select University Choice</option>
      <option value="abroad" <?php echo $universityChoice == 'abroad' ? 'selected' : ''; ?>>Abroad</option>
      <option value="domestic" <?php echo $universityChoice == 'domestic' ? 'selected' : ''; ?>>Domestic</option>
      <option value="both" <?php echo $universityChoice == 'both' ? 'selected' : ''; ?>>Both</option>
    </select>
    <input type="submit" name="signup_as_student" value="Sign Up as Student" id="signup_as_student">
  </form>
  <p class="signup-link">
    Already have an account? <a href="login.php">Login</a>
  </p>
</body>

</html>