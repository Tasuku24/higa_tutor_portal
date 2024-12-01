<!-- 生徒登録とチューター登録の表示切り替えとか頼む -->

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

  <!-- チューター登録 -->
  <form>
    <input type="text" id="email" placeholder="Email" required>
    <input type="password" id="password" placeholder="Password" required>
    <input type="password" id="confirm-password" placeholder="Confirm Password" required>
    <input type="text" id="name" placeholder="Name" required>
    <select id="sex" required>
      <option value="" disabled selected>Select Sex</option>
      <option value="male">Male</option>
      <option value="female">Female</option>
      <option value="other">Other</option>
    </select>
    <select id="preferred-language" required>
      <option value="" disabled selected>Select Preferred Language</option>
      <option value="english">English</option>
      <option value="japanese">Japanese</option>
      <option value="both">Both</option>
    </select>
    <select id="university-choice" required>
      <option value="" disabled selected>Select University Choice</option>
      <option value="abroad">Abroad</option>
      <option value="domestic">Domestic</option>
      <option value="both">Both</option>
    </select>
    <div class="subjects">
      <label for="subjects">Subjects you can teach:</label>
      <div class="subject-group">
        <h3>Mathematics</h3>
        <div>
          <input type="checkbox" id="maths-aa-sl" name="subjects" value="maths-aa-sl">
          <label for="maths-aa-sl">Mathematics: Analysis and Approaches SL</label>
          <input type="checkbox" id="maths-aa-hl" name="subjects" value="maths-aa-hl">
          <label for="maths-aa-hl">Mathematics: Analysis and Approaches HL</label>
          <input type="checkbox" id="maths-ai-sl" name="subjects" value="maths-ai-sl">
          <label for="maths-ai-sl">Mathematics: Applications and Interpretation SL</label>
          <input type="checkbox" id="maths-ai-hl" name="subjects" value="maths-ai-hl">
          <label for="maths-ai-hl">Mathematics: Applications and Interpretation HL</label>
        </div>
      </div>
      <div class="subject-group">
        <h3>Sciences</h3>
        <div>
          <input type="checkbox" id="biology-sl" name="subjects" value="biology-sl">
          <label for="biology-sl">Biology SL</label>
          <input type="checkbox" id="biology-hl" name="subjects" value="biology-hl">
          <label for="biology-hl">Biology HL</label>
          <input type="checkbox" id="chemistry-sl" name="subjects" value="chemistry-sl">
          <label for="chemistry-sl">Chemistry SL</label>
          <input type="checkbox" id="chemistry-hl" name="subjects" value="chemistry-hl">
          <label for="chemistry-hl">Chemistry HL</label>
          <input type="checkbox" id="physics-sl" name="subjects" value="physics-sl">
          <label for="physics-sl">Physics SL</label>
          <input type="checkbox" id="physics-hl" name="subjects" value="physics-hl">
          <label for="physics-hl">Physics HL</label>
        </div>
      </div>
      <div class="subject-group">
        <h3>Languages</h3>
        <div>
          <input type="checkbox" id="english-a-sl" name="subjects" value="english-a-sl">
          <label for="english-a-sl">English A SL</label>
          <input type="checkbox" id="english-a-hl" name="subjects" value="english-a-hl">
          <label for="english-a-hl">English A HL</label>
          <input type="checkbox" id="japanese-a-sl" name="subjects" value="japanese-a-sl">
          <label for="japanese-a-sl">Japanese A SL</label>
          <input type="checkbox" id="japanese-a-hl" name="subjects" value="japanese-a-hl">
          <label for="japanese-a-hl">Japanese A HL</label>
          <input type="checkbox" id="english-b-sl" name="subjects" value="english-b-sl">
          <label for="english-b-sl">English B SL</label>
          <input type="checkbox" id="english-b-hl" name="subjects" value="english-b-hl">
          <label for="english-b-hl">English B HL</label>
          <input type="checkbox" id="japanese-b-sl" name="subjects" value="japanese-b-sl">
          <label for="japanese-b-sl">Japanese B SL</label>
          <input type="checkbox" id="japanese-b-hl" name="subjects" value="japanese-b-hl">
          <label for="japanese-b-hl">Japanese B HL</label>
        </div>
      </div>
      <div class="subject-group">
        <h3>Humanities</h3>
        <div>
          <input type="checkbox" id="history-sl" name="subjects" value="history-sl">
          <label for="history-sl">History SL</label>
          <input type="checkbox" id="history-hl" name="subjects" value="history-hl">
          <label for="history-hl">History HL</label>
          <input type="checkbox" id="geography-sl" name="subjects" value="geography-sl">
          <label for="geography-sl">Geography SL</label>
          <input type="checkbox" id="geography-hl" name="subjects" value="geography-hl">
          <label for="geography-hl">Geography HL</label>
        </div>
      </div>
      <div class="subject-group">
        <h3>Arts</h3>
        <div>
          <input type="checkbox" id="film-sl" name="subjects" value="film-sl">
          <label for="film-sl">Film SL</label>
          <input type="checkbox" id="music-sl" name="subjects" value="music-sl">
          <label for="music-sl">Music SL</label>
        </div>
      </div>
    </div>
    <input type="submit" value="Sign Up" id="signup_as_tutor">
  </form>

  <!-- 生徒登録 -->
  <form>
    <input type="text" id="email" placeholder="Email" required>
    <input type="password" id="password" placeholder="Password" required>
    <input type="password" id="confirm-password" placeholder="Confirm Password" required>
    <input type="text" id="name" placeholder="Name" required>
    <select id="sex" required>
      <option value="" disabled selected>Select Sex</option>
      <option value="male">Male</option>
      <option value="female">Female</option>
      <option value="other">Other</option>
    </select>
    <select id="grade" required>
      <option value="" disabled selected>Select Grade</option>
      <option value="grade9">Grade 9</option>
      <option value="grade10">Grade 10</option>
      <option value="grade11">Grade 11</option>
      <option value="grade12">Grade 12</option>
    </select>
    <select id="preferred-language" required>
      <option value="" disabled selected>Select Preferred Language</option>
      <option value="english">English</option>
      <option value="japanese">Japanese</option>
      <option value="both">Both</option>
    </select>
    <select id="university-choice" required>
      <option value="" disabled selected>Select University Choice</option>
      <option value="abroad">Abroad</option>
      <option value="domestic">Domestic</option>
      <option value="both">Both</option>
    </select>
    <input type="submit" value="Sign Up" id="signup_as_student">
  </form>
  <p class="signup-link">
    Already have an account? <a href="login.php">Login</a>
  </p>
</body>

</html>