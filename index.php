<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require_once 'db.php';

$stmt = $pdo->query("SELECT A.*, L.language_name, G.genre_name FROM AUDIO A 
                     LEFT JOIN LANGUAGE L ON A.languange_id = L.language_id 
                     LEFT JOIN GENRE G ON A.genre_id = G.genre_id 
                     ORDER BY A.audio_id DESC LIMIT 4");
$recent_uploads = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>AudioLang — Audio Language Detection System</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700">
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<header class="tm-top-bar">
  <div class="tm-main tm-nav">
    <div class="tm-brand"><span class="dot"></span> AudioLang</div>
    <ul class="tm-menu">
      <li><a href="index.php" class="active">Home</a></li>
      <li><a href="upload.php">Upload</a></li>
      <li><a href="library.php">Library</a></li>
      <li><a href="search.php">Search</a></li>
      <li><a href="similar.php">Similar</a></li>
      <li><a href="logout.php" style="color: #ff6b6b;">Logout</a></li>
    </ul>
  </div>
</header>

<section class="tm-hero">
  <div class="tm-main">
    <h1>Audio Language Detection System</h1>
    <p>A multimedia database project (BITP 3353) by Group GS04 — upload audio, auto-detect spoken language, classify music genre, and retrieve recordings using ABR, TBR and CBR techniques.</p>
    <a href="upload.php" class="btn btn-primary">Upload Audio</a>
    <a href="library.php" class="btn btn-outline" style="margin-left:10px;">Browse Library</a>
  </div>
</section>

<section class="tm-section">
  <div class="tm-main">
    <h2>Three Retrieval Methods</h2>
    <p class="sub">Our system integrates Attribute-Based, Text-Based and Content-Based Retrieval over a unified audio database.</p>
    <div class="tm-features">
      <div class="tm-feature">
        <div class="ico">ABR</div>
        <h4>Attribute-Based Retrieval</h4>
        <p>Filter by file type (MP3, WAV), spoken language and music genre options seamlessly.</p>
      </div>
      <div class="tm-feature">
        <div class="ico">TBR</div>
        <h4>Text-Based Retrieval</h4>
        <p>Auto-generated speech-to-text transcripts enable keyword search across every audio recording in the library.</p>
      </div>
      <div class="tm-feature">
        <div class="ico">CBR</div>
        <h4>Content-Based Retrieval</h4>
        <p>Pitch, tempo, rhythm, and waveform features power similarity search and language classification.</p>
      </div>
    </div>
  </div>
</section>

<section class="tm-section tm-section-white">
  <div class="tm-main">
    <h2>Recent Uploads</h2>
    <p class="sub">Latest audio files indexed by the system.</p>
    <div class="tm-grid">
      <?php foreach ($recent_uploads as $upload): 
        $label = $upload['language_name'] == 'Mandarin' ? '中' : strtoupper(substr($upload['language_name'], 0, 2));
        $class = $upload['language_name'] == 'Malay' ? 'lang-my' : ($upload['language_name'] == 'Mandarin' ? 'lang-cn' : 'lang-en');
      ?>
      <a href="detail.php?id=<?= $upload['audio_id'] ?>" class="tm-card">
        <div class="tm-card-cover <?= $class ?>"><?= $label ?></div>
        <div class="tm-card-body">
          <h4><?= htmlspecialchars($upload['file_name']) ?></h4>
          <div class="meta">
            <span class="tm-tag blue"><?= htmlspecialchars($upload['language_name']) ?></span>
            <span class="tm-tag gray"><?= htmlspecialchars($upload['genre_name']) ?></span>
          </div>
          <div class="tm-card-footer"><span><?= htmlspecialchars($upload['file_type']) ?> · <?= htmlspecialchars($upload['duration']) ?></span><span><?= htmlspecialchars($upload['date_upload']) ?></span></div>
        </div>
      </a>
      <?php endforeach; if(empty($recent_uploads)) echo "<p style='color:#898989;'>No items uploaded yet.</p>"; ?>
    </div>
  </div>
</section>

<footer class="tm-footer">
  <div class="tm-main"><span class="accent">GS04 · BITP 3353 Multimedia Database</span> · UTeM</div>
</footer>
</body>
</html>