<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
// error list
// 1. パスワードが一致していない場合のエラー表示
// 2. メールアドレスがすでに登録されている場合のエラー表示
// 3. 教科が選択されていない場合のエラー表示
session_start();
if (isset($_SESSION['email'])) {
  header('Location: index.php');
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
  if (isset($_POST['grade']))
    $grade = $_POST['grade'];

  $flag = true;

  if ($password_input != $confirmPassword) {
    $errors[] = "Passwords do not match!";
    $flag = false;
  }

  if (!preg_match('/^s[0-9]{5}@higanet\.higa\.ed\.jp$/', $email)) {
    $errors[] = "Please Enter a Valid HiGA Email Address!";
    $flag = false;
  }


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
      $flag = false;
    } else {
      $subjectsList = implode(',', $subjects);
    }
    $query = "INSERT INTO tutors (email, password, salt, name, sex, preferred_language, university_choice, subjects) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssssss", $email, $hashedPassword, $salt, $name, $sex, $preferredLanguage, $universityChoice, $subjectsList);
  } elseif (isset($_POST['signup_as_student']) && $flag) {
    if (empty($_POST['subjects'])) {
      $errors[] = "Please select at least one subject!";
    } else {
      $subjectsList = implode(', ', $subjects);
    }
    $query = "INSERT INTO students (email, password, salt, name, sex, grade, preferred_language, university_choice, subjects) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssssssss", $email, $hashedPassword, $salt, $name, $sex, $grade, $preferredLanguage, $universityChoice, $subjectsList);
  }

  if ($stmt->execute() && empty($errors)) {
    $_SESSION['email'] = $email;
    $_SESSION['user_type'] = isset($_POST['signup_as_tutor']) ? 'tutor' : 'student';
    header('Location: index.php');
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
  <!-- Include jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
  <h1 class="title">Sign Up to <span>HiGA Tutor Portal</span></h1>
  <!-- Signup Option -->
  <div class="body">
    <div class="signup-option">
      <input type="radio" name="signup_option" value="tutor" id="tutor" style="display: none">
      <label for="tutor">Sign Up as Tutor</label>
      <input type="radio" name="signup_option" value="student" id="student" style="display: none">
      <label for="student">Sign Up as Student</label>
    </div>
    <!-- エラー表示 -->
    <?php if (!empty($errors)): ?>
      <div class="error-messages">
        <?php foreach ($errors as $error): ?>
          <p><?php echo htmlspecialchars($error); ?></p>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
    <!-- チューター登録 -->
    <div id="tutor-form" style="display: none;">
      <form method="post">
        <div class="bacic-info">
          <h2>Basic Information</h2>
          <div class="bacic-questions">
            <input type="text" name="email" id="tutor-email" placeholder="Email (s*****@higanet.higa.ed.jp)" value="<?php echo htmlspecialchars($email); ?>" required>
            <input type="password" name="password" id="tutor-password" placeholder="Password" required>
            <input type="password" name="confirm-password" id="tutor-confirm-password" placeholder="Confirm Password" required>
            <input type="text" name="name" id="tutor-name" placeholder="Name" value="<?php echo htmlspecialchars($name); ?>" required>
            <select name="sex" id="tutor-sex" required>
              <option value="" disabled <?php echo empty($sex) ? 'selected' : ''; ?>>Select Sex</option>
              <option value="male" <?php echo $sex == 'male' ? 'selected' : ''; ?>>Male</option>
              <option value="female" <?php echo $sex == 'female' ? 'selected' : ''; ?>>Female</option>
              <option value="other" <?php echo $sex == 'other' ? 'selected' : ''; ?>>Other</option>
            </select>
            <select name="preferred-language" id="tutor-preferred-language" required>
              <option value="" disabled <?php echo empty($preferredLanguage) ? 'selected' : ''; ?>>Select Preferred Language</option>
              <option value="english" <?php echo $preferredLanguage == 'english' ? 'selected' : ''; ?>>English</option>
              <option value="japanese" <?php echo $preferredLanguage == 'japanese' ? 'selected' : ''; ?>>Japanese</option>
              <option value="both" <?php echo $preferredLanguage == 'both' ? 'selected' : ''; ?>>Both</option>
            </select>
            <select name="university-choice" id="tutor-university-choice" required>
              <option value="" disabled <?php echo empty($universityChoice) ? 'selected' : ''; ?>>Select University Choice</option>
              <option value="abroad" <?php echo $universityChoice == 'abroad' ? 'selected' : ''; ?>>Abroad</option>
              <option value="domestic" <?php echo $universityChoice == 'domestic' ? 'selected' : ''; ?>>Domestic</option>
              <option value="both" <?php echo $universityChoice == 'both' ? 'selected' : ''; ?>>Both</option>
            </select>
          </div>
        </div>
        <div class="subjects">
          <h2>Subjects (that you can teach)</h2>
          <div class="subject">
            <div class="subject-group">
              <h3>Group 1: Studies in Language and Literature</h3>
              <div>
                <input type="checkbox" name="subjects[]" id="tutor-english-a-sl" value="english-a-sl" <?php echo in_array('english-a-sl', $subjects) ? 'checked' : ''; ?>>
                <label for="tutor-english-a-sl">English A SL</label>
                <input type="checkbox" name="subjects[]" id="tutor-english-a-hl" value="english-a-hl" <?php echo in_array('english-a-hl', $subjects) ? 'checked' : ''; ?>>
                <label for="tutor-english-a-hl">English A HL</label>
                <input type="checkbox" name="subjects[]" id="tutor-japanese-a-sl" value="japanese-a-sl" <?php echo in_array('japanese-a-sl', $subjects) ? 'checked' : ''; ?>>
                <label for="tutor-japanese-a-sl">Japanese A SL</label>
                <input type="checkbox" name="subjects[]" id="tutor-japanese-a-hl" value="japanese-a-hl" <?php echo in_array('japanese-a-hl', $subjects) ? 'checked' : ''; ?>>
                <label for="tutor-japanese-a-hl">Japanese A HL</label>
              </div>
            </div>
            <div class="subject-group">
              <h3>Group 2: Language Acquisition</h3>
              <div>
                <input type="checkbox" name="subjects[]" id="tutor-english-b-sl" value="english-b-sl" <?php echo in_array('english-b-sl', $subjects) ? 'checked' : ''; ?>>
                <label for="tutor-english-b-sl">English B SL</label>
                <input type="checkbox" name="subjects[]" id="tutor-english-b-hl" value="english-b-hl" <?php echo in_array('english-b-hl', $subjects) ? 'checked' : ''; ?>>
                <label for="tutor-english-b-hl">English B HL</label>
                <input type="checkbox" name="subjects[]" id="tutor-japanese-b-sl" value="japanese-b-sl" <?php echo in_array('japanese-b-sl', $subjects) ? 'checked' : ''; ?>>
                <label for="tutor-japanese-b-sl">Japanese B SL</label>
                <input type="checkbox" name="subjects[]" id="tutor-japanese-b-hl" value="japanese-b-hl" <?php echo in_array('japanese-b-hl', $subjects) ? 'checked' : ''; ?>>
                <label for="tutor-japanese-b-hl">Japanese B HL</label>
              </div>
            </div>
            <div class="subject-group">
              <h3>Group 3: Individuals and Societies</h3>
              <div>
                <input type="checkbox" name="subjects[]" id="tutor-history-sl" value="history-sl" <?php echo in_array('history-sl', $subjects) ? 'checked' : ''; ?>>
                <label for="tutor-history-sl">History SL</label>
                <input type="checkbox" name="subjects[]" id="tutor-history-hl" value="history-hl" <?php echo in_array('history-hl', $subjects) ? 'checked' : ''; ?>>
                <label for="tutor-history-hl">History HL</label>
                <input type="checkbox" name="subjects[]" id="tutor-geography-sl" value="geography-sl" <?php echo in_array('geography-sl', $subjects) ? 'checked' : ''; ?>>
                <label for="tutor-geography-sl">Geography SL</label>
                <input type="checkbox" name="subjects[]" id="tutor-geography-hl" value="geography-hl" <?php echo in_array('geography-hl', $subjects) ? 'checked' : ''; ?>>
                <label for="tutor-geography-hl">Geography HL</label>
              </div>
            </div>
            <div class="subject-group">
              <h3>Group 4: Sciences</h3>
              <div>
                <input type="checkbox" name="subjects[]" id="tutor-physics-sl" value="physics-sl" <?php echo in_array('physics-sl', $subjects) ? 'checked' : ''; ?>>
                <label for="tutor-physics-sl">Physics SL</label>
                <input type="checkbox" name="subjects[]" id="tutor-physics-hl" value="physics-hl" <?php echo in_array('physics-hl', $subjects) ? 'checked' : ''; ?>>
                <label for="tutor-physics-hl">Physics HL</label>
                <input type="checkbox" name="subjects[]" id="tutor-chemistry-sl" value="chemistry-sl" <?php echo in_array('chemistry-sl', $subjects) ? 'checked' : ''; ?>>
                <label for="tutor-chemistry-sl">Chemistry SL</label>
                <input type="checkbox" name="subjects[]" id="tutor-chemistry-hl" value="chemistry-hl" <?php echo in_array('chemistry-hl', $subjects) ? 'checked' : ''; ?>>
                <label for="tutor-chemistry-hl">Chemistry HL</label>
                <input type="checkbox" name="subjects[]" id="tutor-biology-sl" value="biology-sl" <?php echo in_array('biology-sl', $subjects) ? 'checked' : ''; ?>>
                <label for="tutor-biology-sl">Biology SL</label>
                <input type="checkbox" name="subjects[]" id="tutor-biology-hl" value="biology-hl" <?php echo in_array('biology-hl', $subjects) ? 'checked' : ''; ?>>
                <label for="tutor-biology-hl">Biology HL</label>
              </div>
            </div>
            <div class="subject-group">
              <h3>Group 5: Mathematics</h3>
              <div>
                <input type="checkbox" name="subjects[]" id="tutor-maths-aa-sl" value="maths-aa-sl" <?php echo in_array('maths-aa-sl', $subjects) ? 'checked' : ''; ?>>
                <label for="tutor-maths-aa-sl">Analysis and Approaches SL</label>
                <input type="checkbox" name="subjects[]" id="tutor-maths-aa-hl" value="maths-aa-hl" <?php echo in_array('maths-aa-hl', $subjects) ? 'checked' : ''; ?>>
                <label for="tutor-maths-aa-hl">Analysis and Approaches HL</label>
                <input type="checkbox" name="subjects[]" id="tutor-maths-ai-sl" value="maths-ai-sl" <?php echo in_array('maths-ai-sl', $subjects) ? 'checked' : ''; ?>>
                <label for="tutor-maths-ai-sl">Applications and Interpretation SL</label>
                <input type="checkbox" name="subjects[]" id="tutor-maths-ai-hl" value="maths-ai-hl" <?php echo in_array('maths-ai-hl', $subjects) ? 'checked' : ''; ?>>
                <label for="tutor-maths-ai-hl">Applications and Interpretation HL</label>
              </div>
            </div>
            <div class="subject-group">
              <h3>Group 6: The Arts</h3>
              <div>
                <input type="checkbox" name="subjects[]" id="tutor-film-sl" value="film-sl" <?php echo in_array('film-sl', $subjects) ? 'checked' : ''; ?>>
                <label for="tutor-film-sl">Film SL</label>
                <input type="checkbox" name="subjects[]" id="tutor-music-sl" value="music-sl" <?php echo in_array('music-sl', $subjects) ? 'checked' : ''; ?>>
                <label for="tutor-music-sl">Music SL</label>
              </div>
            </div>
            <input type="submit" name="signup_as_tutor" value="Sign Up as Tutor" id="signup_as_tutor">
          </div>
        </div>
      </form>
    </div>

    <!-- 生徒登録 -->
    <div id="student-form" style="display: none;">
      <form method="post">
        <div class="bacic-info">
          <h2>Bacic Information</h2>
          <div class="bacic-questions">
            <input type="text" name="email" id="student-email" placeholder="Email (s*****@higanet.higa.ed.jp)" value="<?php echo htmlspecialchars($email); ?>" required>
            <input type="password" name="password" id="student-password" placeholder="Password" required>
            <input type="password" name="confirm-password" id="student-confirm-password" placeholder="Confirm Password" required>
            <input type="text" name="name" id="student-name" placeholder="Name" value="<?php echo htmlspecialchars($name); ?>" required>
            <select name="sex" id="student-sex" required>
              <option value="" disabled <?php echo empty($sex) ? 'selected' : ''; ?>>Select Sex</option>
              <option value="male" <?php echo $sex == 'male' ? 'selected' : ''; ?>>Male</option>
              <option value="female" <?php echo $sex == 'female' ? 'selected' : ''; ?>>Female</option>
              <option value="other" <?php echo $sex == 'other' ? 'selected' : ''; ?>>Other</option>
            </select>
            <select name="grade" id="student-grade" required>
              <option value="" disabled <?php echo empty($grade) ? 'selected' : ''; ?>>Select Grade</option>
              <option value="grade9" <?php echo $grade == 'grade9' ? 'selected' : ''; ?>>Grade 9</option>
              <option value="grade10" <?php echo $grade == 'grade10' ? 'selected' : ''; ?>>Grade 10</option>
              <option value="grade11" <?php echo $grade == 'grade11' ? 'selected' : ''; ?>>Grade 11</option>
              <option value="grade12" <?php echo $grade == 'grade12' ? 'selected' : ''; ?>>Grade 12</option>
            </select>
            <select name="preferred-language" id="student-preferred-language" required>
              <option value="" disabled <?php echo empty($preferredLanguage) ? 'selected' : ''; ?>>Select Preferred Language</option>
              <option value="english" <?php echo $preferredLanguage == 'english' ? 'selected' : ''; ?>>English</option>
              <option value="japanese" <?php echo $preferredLanguage == 'japanese' ? 'selected' : ''; ?>>Japanese</option>
              <option value="both" <?php echo $preferredLanguage == 'both' ? 'selected' : ''; ?>>Both</option>
            </select>
            <select name="university-choice" id="student-university-choice" required>
              <option value="" disabled <?php echo empty($universityChoice) ? 'selected' : ''; ?>>Select University Choice</option>
              <option value="abroad" <?php echo $universityChoice == 'abroad' ? 'selected' : ''; ?>>Abroad</option>
              <option value="domestic" <?php echo $universityChoice == 'domestic' ? 'selected' : ''; ?>>Domestic</option>
              <option value="both" <?php echo $universityChoice == 'both' ? 'selected' : ''; ?>>Both</option>
            </select>
          </div>
        </div>
        <div class="subjects">
          <h2>Subjects (that you want to be taught)</h2>
          <div class="subject">
            <div class="subject-group">
              <h3>Group 1: Studies in Language and Literature</h3>
              <div>
                <input type="checkbox" name="subjects[]" id="student-english-a-sl" value="english-a-sl" <?php echo in_array('english-a-sl', $subjects) ? 'checked' : ''; ?>>
                <label for="student-english-a-sl">English A SL</label>
                <input type="checkbox" name="subjects[]" id="student-english-a-hl" value="english-a-hl" <?php echo in_array('english-a-hl', $subjects) ? 'checked' : ''; ?>>
                <label for="student-english-a-hl">English A HL</label>
                <input type="checkbox" name="subjects[]" id="student-japanese-a-sl" value="japanese-a-sl" <?php echo in_array('japanese-a-sl', $subjects) ? 'checked' : ''; ?>>
                <label for="student-japanese-a-sl">Japanese A SL</label>
                <input type="checkbox" name="subjects[]" id="student-japanese-a-hl" value="japanese-a-hl" <?php echo in_array('japanese-a-hl', $subjects) ? 'checked' : ''; ?>>
                <label for="student-japanese-a-hl">Japanese A HL</label>
              </div>
            </div>
            <div class="subject-group">
              <h3>Group 2: Language Acquisition</h3>
              <div>
                <input type="checkbox" name="subjects[]" id="student-english-b-sl" value="english-b-sl" <?php echo in_array('english-b-sl', $subjects) ? 'checked' : ''; ?>>
                <label for="student-english-b-sl">English B SL</label>
                <input type="checkbox" name="subjects[]" id="student-english-b-hl" value="english-b-hl" <?php echo in_array('english-b-hl', $subjects) ? 'checked' : ''; ?>>
                <label for="student-english-b-hl">English B HL</label>
                <input type="checkbox" name="subjects[]" id="student-japanese-b-sl" value="japanese-b-sl" <?php echo in_array('japanese-b-sl', $subjects) ? 'checked' : ''; ?>>
                <label for="student-japanese-b-sl">Japanese B SL</label>
                <input type="checkbox" name="subjects[]" id="student-japanese-b-hl" value="japanese-b-hl" <?php echo in_array('japanese-b-hl', $subjects) ? 'checked' : ''; ?>>
                <label for="student-japanese-b-hl">Japanese B HL</label>
              </div>
            </div>
            <div class="subject-group">
              <h3>Group 3: Individuals and Societies</h3>
              <div>
                <input type="checkbox" name="subjects[]" id="student-history-sl" value="history-sl" <?php echo in_array('history-sl', $subjects) ? 'checked' : ''; ?>>
                <label for="student-history-sl">History SL</label>
                <input type="checkbox" name="subjects[]" id="student-history-hl" value="history-hl" <?php echo in_array('history-hl', $subjects) ? 'checked' : ''; ?>>
                <label for="student-history-hl">History HL</label>
                <input type="checkbox" name="subjects[]" id="student-geography-sl" value="geography-sl" <?php echo in_array('geography-sl', $subjects) ? 'checked' : ''; ?>>
                <label for="student-geography-sl">Geography SL</label>
                <input type="checkbox" name="subjects[]" id="student-geography-hl" value="geography-hl" <?php echo in_array('geography-hl', $subjects) ? 'checked' : ''; ?>>
                <label for="student-geography-hl">Geography HL</label>
              </div>
            </div>
            <div class="subject-group">
              <h3>Group 4: Sciences</h3>
              <div>
                <input type="checkbox" name="subjects[]" id="student-physics-sl" value="physics-sl" <?php echo in_array('physics-sl', $subjects) ? 'checked' : ''; ?>>
                <label for="student-physics-sl">Physics SL</label>
                <input type="checkbox" name="subjects[]" id="student-physics-hl" value="physics-hl" <?php echo in_array('physics-hl', $subjects) ? 'checked' : ''; ?>>
                <label for="student-physics-hl">Physics HL</label>
                <input type="checkbox" name="subjects[]" id="student-chemistry-sl" value="chemistry-sl" <?php echo in_array('chemistry-sl', $subjects) ? 'checked' : ''; ?>>
                <label for="student-chemistry-sl">Chemistry SL</label>
                <input type="checkbox" name="subjects[]" id="student-chemistry-hl" value="chemistry-hl" <?php echo in_array('chemistry-hl', $subjects) ? 'checked' : ''; ?>>
                <label for="student-chemistry-hl">Chemistry HL</label>
                <input type="checkbox" name="subjects[]" id="student-biology-sl" value="biology-sl" <?php echo in_array('biology-sl', $subjects) ? 'checked' : ''; ?>>
                <label for="student-biology-sl">Biology SL</label>
                <input type="checkbox" name="subjects[]" id="student-biology-hl" value="biology-hl" <?php echo in_array('biology-hl', $subjects) ? 'checked' : ''; ?>>
                <label for="student-biology-hl">Biology HL</label>
              </div>
            </div>
            <div class="subject-group">
              <h3>Group 5: Mathematics</h3>
              <div>
                <input type="checkbox" name="subjects[]" id="student-maths-aa-sl" value="maths-aa-sl" <?php echo in_array('maths-aa-sl', $subjects) ? 'checked' : ''; ?>>
                <label for="student-maths-aa-sl">Mathematics: Analysis and Approaches SL</label>
                <input type="checkbox" name="subjects[]" id="student-maths-aa-hl" value="maths-aa-hl" <?php echo in_array('maths-aa-hl', $subjects) ? 'checked' : ''; ?>>
                <label for="student-maths-aa-hl">Mathematics: Analysis and Approaches HL</label>
                <input type="checkbox" name="subjects[]" id="student-maths-ai-sl" value="maths-ai-sl" <?php echo in_array('maths-ai-sl', $subjects) ? 'checked' : ''; ?>>
                <label for="student-maths-ai-sl">Mathematics: Applications and Interpretation SL</label>
                <input type="checkbox" name="subjects[]" id="student-maths-ai-hl" value="maths-ai-hl" <?php echo in_array('maths-ai-hl', $subjects) ? 'checked' : ''; ?>>
                <label for="student-maths-ai-hl">Mathematics: Applications and Interpretation HL</label>
              </div>
            </div>
            <div class="subject-group">
              <h3>Group 6: The Arts</h3>
              <div>
                <input type="checkbox" name="subjects[]" id="student-film-sl" value="film-sl" <?php echo in_array('film-sl', $subjects) ? 'checked' : ''; ?>>
                <label for="student-film-sl">Film SL</label>
                <input type="checkbox" name="subjects[]" id="student-music-sl" value="music-sl" <?php echo in_array('music-sl', $subjects) ? 'checked' : ''; ?>>
                <label for="student-music-sl">Music SL</label>
              </div>
            </div>
            <input type="submit" name="signup_as_student" value="Sign Up as Student" id="signup_as_student">
          </div>
        </div>
      </form>
    </div>
  </div>
  <p class="signup-link">
    Already have an account? <a href="login.php">Login</a>
  </p>

  <script>
    $(document).ready(function() {
      // Initially display the appropriate form with animation
      var selectedOption = $('input[name="signup_option"]:checked').val();

      // Animate form transitions on option change
      $('input[name="signup_option"]').change(function() {
        if (this.value == 'tutor') {
          $('#student-form').slideUp('normal', function() {
            $('#tutor-form').slideDown('normal');
          });
        } else {
          $('#tutor-form').slideUp('normal', function() {
            $('#student-form').slideDown('normal');
          });
        }
      });
    });
  </script>
</body>

</html>