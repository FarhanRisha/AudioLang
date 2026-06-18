<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require_once 'db.php';

$file_type = $_GET['file_type'] ?? 'All';
$language_id = $_GET['language_id'] ?? 'All';
$genre_id = $_GET['genre_id'] ?? 'All';

$sql = "SELECT A.*, L.language_name, G.genre_name 
        FROM AUDIO A
        LEFT JOIN LANGUAGE L ON A.languange_id = L.language_id
        LEFT JOIN GENRE G ON A.genre_id = G.genre_id
        WHERE 1=1";
$params = [];

if ($file_type !== 'All') { $sql .= " AND A.file_type = :ft"; $params['ft'] = $file_type; }
if ($language_id !== 'All') { $sql .= " AND A.languange_id = :li"; $params['li'] = intval($language_id); }
if ($genre_id !== 'All') { $sql .= " AND A.genre_id = :gi"; $params['gi'] = intval($genre_id); }

$stmt = $pdo->prepare($sql); $stmt->execute($params);
$audio_records = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Library — AudioLang (ABR)</title>
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
      <li><a href="library.php" class="active">Library</a></li>
      <li><a href="search.php">Search</a></li>
      <li><a href="similar.php">Similar</a></li>
      <li><a href="logout.php" style="color: #ff6b6b;">Logout</a></li>
    </ul>
  </div>
</header>

<section class="tm-section">
  <div class="tm-main">
    <h2>Audio Library</h2>
    <p class="sub">Attribute-Based Retrieval (ABR) — filter full audio files by database schema criteria values.</p>

    <div class="tm-search-card" style="margin-top:0;">
      <form method="GET" action="library.php" class="tm-row">
        <div class="tm-field"><label>File Type</label>
          <select name="file_type">
            <option <?= $file_type == 'All' ? 'selected' : '' ?>>All</option>
            <option <?= $file_type == 'MP3' ? 'selected' : '' ?>>MP3</option>
            <option <?= $file_type == 'WAV' ? 'selected' : '' ?>>WAV</option>
          </select></div>
        <div class="tm-field"><label>Language</label>
          <select name="language_id">
            <option value="All" <?= $language_id == 'All' ? 'selected' : '' ?>>All</option>
            <option value="1" <?= $language_id == '1' ? 'selected' : '' ?>>English</option>
            <option value="2" <?= $language_id == '2' ? 'selected' : '' ?>>Malay</option>
          </select></div>
        <div class="tm-field"><label>Genre</label>
          <select name="genre_id">
            <option value="All" <?= $genre_id == 'All' ? 'selected' : '' ?>>All</option>
            <option value="1" <?= $genre_id == '1' ? 'selected' : '' ?>>Pop</option>
            <option value="2" <?= $genre_id == '2' ? 'selected' : '' ?>>Rock</option>
            <option value="3" <?= $genre_id == '3' ? 'selected' : '' ?>>Classical</option>
            <option value="4" <?= $genre_id == '4' ? 'selected' : '' ?>>Hip-Hop</option>
            <option value="5" <?= $genre_id == '5' ? 'selected' : '' ?>>Jazz</option>
            <option value="6" <?= $genre_id == '6' ? 'selected' : '' ?>>Electronic</option>
            <option value="7" <?= $genre_id == '7' ? 'selected' : '' ?>>R&B</option>
          </select></div>
        <div class="tm-field" style="flex:0 0 160px; justify-content:flex-end;">
          <button type="submit" class="btn btn-primary">Apply Filters</button>
        </div>
      </form>
    </div>

    <p style="margin:24px 0;color:#898989;">Showing <strong><?= count($audio_records) ?></strong> results</p>

    <div class="tm-grid">
      <?php foreach ($audio_records as $audio): 
      $label = strtoupper(substr($audio['language_name'], 0, 2)); // Produces "EN" or "MA"
      $class = $audio['language_name'] == 'Malay' ? 'lang-my' : 'lang-en';
      ?>
      <a href="detail.php?id=<?= $audio['audio_id'] ?>" class="tm-card">
        <div class="tm-card-cover <?= $class ?>"><?= $label ?></div>
        <div class="tm-card-body">
          <h4><?= htmlspecialchars($audio['file_name']) ?></h4>
          <div class="meta"><span class="tm-tag blue"><?= htmlspecialchars($audio['language_name']) ?></span><span class="tm-tag gray"><?= htmlspecialchars($audio['genre_name']) ?></span><span class="tm-tag green"><?= htmlspecialchars($audio['file_type']) ?></span></div>
          <div class="tm-card-footer"><span><?= htmlspecialchars($audio['duration']) ?> · <?= htmlspecialchars($audio['file_size']) ?></span><span><?= htmlspecialchars($audio['date_upload']) ?></span></div>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<footer class="tm-footer">
  <div class="tm-main"><span class="accent">GS04 · BITP 3353 Multimedia Database</span> · UTeM</div>
</footer>
</body>
</html>