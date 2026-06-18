<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require_once 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Upload Audio — AudioLang</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700">
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<header class="tm-top-bar">
  <div class="tm-main tm-nav">
    <div class="tm-brand"><span class="dot"></span> AudioLang</div>
    <ul class="tm-menu">
      <li><a href="index.php">Home</a></li>
      <li><a href="upload.php" class="active">Upload</a></li>
      <li><a href="library.php">Library</a></li>
      <li><a href="search.php">Search</a></li>
      <li><a href="similar.php">Similar</a></li>
      <li><a href="logout.php" style="color: #ff6b6b;">Logout</a></li>
    </ul>
  </div>
</header>

<section class="tm-section">
  <div class="tm-main">
    <h2>Upload Audio File</h2>
    <p class="sub">Upload an MP3 or WAV file. The Python AI engine will process the entire song timeline to calculate low-level features and assign metadata properties.</p>

    <form action="upload_process.php" method="POST" enctype="multipart/form-data" onsubmit="document.getElementById('loading-overlay').style.display = 'block';">
      <div class="tm-upload-zone" style="cursor:pointer;" onclick="document.getElementById('native-picker').click();">
        <div class="big">⬆</div>
        <h3>Click here to select your full song track</h3>
        <p>Supported formats: MP3, WAV · Full length processing mode enabled</p>
        <input type="file" id="native-picker" name="audio_file" accept=".mp3,.wav" required style="display:none;" onchange="document.getElementById('display-filename').innerText = 'Target File Loaded: ' + this.files[0].name;">
        <span id="display-filename" style="font-weight:700; color:#ee5057; display:block; margin-top:10px;"></span>
      </div>

      <div style="margin-top:22px; text-align: center;">
        <button type="submit" class="btn btn-primary" style="padding: 15px 40px; font-size: 14px;">Upload & Analyze Full Track with AI</button>
      </div>
    </form>

    <div id="loading-overlay" style="display:none; margin-top: 20px; padding: 20px; background: #fff; border-radius: 6px; text-align: center; border: 1px dashed #ee5057;">
       <strong style="color: #ee5057;">AI Engine Matrix Active:</strong> Processing full track timeline using Mel Fourier transformations. This may take a few seconds depending on track size...
    </div>
  </div>
</section>

<footer class="tm-footer">
  <div class="tm-main"><span class="accent">GS04 · BITP 3353 Multimedia Database</span> · UTeM</div>
</footer>
</body>
</html>