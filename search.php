<?php
require_once 'db.php';
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$matches = [];

if ($keyword !== '') {
    $sql = "SELECT A.*, T.transcript_text FROM AUDIO A
            JOIN TRANSCRIPT T ON A.audio_id = T.audio_id
            WHERE T.transcript_text LIKE :keyword";
    $stmt = $pdo->prepare($sql); $stmt->execute(['keyword' => '%' . $keyword . '%']);
    $matches = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Transcript Search — AudioLang (TBR)</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700">
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<header class="tm-top-bar">
  <div class="tm-main tm-nav">
    <div class="tm-brand"><span class="dot"></span> AudioLang</div>
    <ul class="tm-menu">
      <li><a href="index.php">Home</a></li>
      <li><a href="upload.php">Upload</a></li>
      <li><a href="library.php">Library</a></li>
      <li><a href="search.php" class="active">Search</a></li>
      <li><a href="similar.php">Similar</a></li>
    </ul>
  </div>
</header>

<section class="tm-section">
  <div class="tm-main">
    <h2>Transcript Search</h2>
    <p class="sub">Text-Based Retrieval (TBR) — scan pattern character instances over AI-generated speech rows.</p>

    <div class="tm-search-card" style="margin-top:0;">
      <form method="GET" action="search.php" class="tm-row">
        <div class="tm-field" style="flex:3 1 400px;">
          <label>Keyword</label>
          <input type="text" name="keyword" value="<?= htmlspecialchars($keyword) ?>" placeholder="Type keyword token here...">
        </div>
        <div class="tm-field" style="flex:0 0 160px; justify-content:flex-end;">
          <button type="submit" class="btn btn-primary">Search</button>
        </div>
      </form>
    </div>

    <?php if ($keyword !== ''): ?>
    <p style="margin:24px 0;color:#898989;"><?= count($matches) ?> transcripts contain the word "<strong style="color:#ee5057;"><?= htmlspecialchars($keyword) ?></strong>"</p>
    
    <?php foreach ($matches as $match): ?>
    <div class="tm-panel">
      <h3><?= htmlspecialchars($match['file_name']) ?></h3>
      <div class="tm-transcript" style="margin-top:10px;">
        <?= str_ireplace($keyword, "<mark style='background:#ffe2a8;'>".htmlspecialchars($keyword)."</mark>", htmlspecialchars($match['transcript_text'])) ?>
      </div>
      <p style="margin-top:12px;"><a href="detail.php?id=<?= $match['audio_id'] ?>" class="btn btn-ghost">Open Details</a></p>
    </div>
    <?php endforeach; endif; ?>
  </div>
</section>

<footer class="tm-footer">
  <div class="tm-main"><span class="accent">GS04 · BITP 3353 Multimedia Database</span> · UTeM</div>
</footer>
</body>
</html>