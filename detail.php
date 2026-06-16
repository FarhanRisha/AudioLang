<?php
require_once 'db.php';
$audio_id = isset($_GET['id']) ? intval($_GET['id']) : 1;

$sql = "SELECT A.*, L.language_name, G.genre_name, T.transcript_text, F.tempo, F.pitch, F.rhythm, F.waveform_feature, F.spectrogram_feature, U.name as user_name
        FROM AUDIO A
        LEFT JOIN LANGUAGE L ON A.languange_id = L.language_id
        LEFT JOIN GENRE G ON A.genre_id = G.genre_id
        LEFT JOIN TRANSCRIPT T ON A.audio_id = T.audio_id
        LEFT JOIN AUDIO_FEATURE F ON A.audio_id = F.audio_id
        LEFT JOIN USER U ON A.user_id = U.user_id
        WHERE A.audio_id = :id";

$stmt = $pdo->prepare($sql); $stmt->execute(['id' => $audio_id]); $audio = $stmt->fetch();
if (!$audio) { die("Error: Record row reference target not found."); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= htmlspecialchars($audio['file_name']) ?> — AudioLang</title>
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
    </ul>
  </div>
</header>

<section class="tm-detail-head">
  <div class="tm-main">
    <h1><?= htmlspecialchars($audio['file_name']) ?></h1>
    <div class="meta">
      <span class="tm-tag" style="background:rgba(255,255,255,0.25);color:#fff;"><?= htmlspecialchars($audio['language_name']) ?></span>
      <span class="tm-tag" style="background:rgba(255,255,255,0.25);color:#fff;"><?= htmlspecialchars($audio['genre_name']) ?></span>
      <span class="tm-tag" style="background:rgba(255,255,255,0.25);color:#fff;"><?= htmlspecialchars($audio['file_type']) ?> · <?= htmlspecialchars($audio['duration']) ?></span>
    </div>
    <div style="margin-top:20px;">
      <audio src="<?= htmlspecialchars($audio['file_path']) ?>" controls style="width:100%; max-width:400px;"></audio>
    </div>
    <p style="margin-top:15px;"><a href="similar.php?id=<?= $audio['audio_id'] ?>" class="btn btn-outline">Find Similar Tracks (CBR)</a></p>
  </div>
</section>

<section class="tm-section">
  <div class="tm-main tm-twocol">
    <div>
      <div class="tm-panel">
        <h3>Transcript (TBR)</h3>
        <div class="tm-transcript">"<?= htmlspecialchars($audio['transcript_text']) ?>"</div>
      </div>

      <div class="tm-panel">
        <h3>Audio Features (CBR Metrics)</h3>
        <dl class="tm-kv">
          <dt>Extracted Tempo</dt><dd><?= htmlspecialchars($audio['tempo']) ?> BPM</dd>
          <dt>Extracted Pitch</dt><dd><?= htmlspecialchars($audio['pitch']) ?> Hz</dd>
          <dt>Rhythm Structure</dt><dd><?= htmlspecialchars($audio['rhythm']) ?></dd>
          <dt>Waveform Sample Matrix</dt><dd style="font-family:monospace; font-size:11px; color:#666; word-break:break-all;"><?= htmlspecialchars($audio['waveform_feature']) ?></dd>
          <dt>Spectrogram Token Location</dt><dd><?= htmlspecialchars($audio['spectrogram_feature']) ?></dd>
        </dl>
      </div>
    </div>

    <div>
      <div class="tm-panel">
        <h3>File Metadata (ABR)</h3>
        <dl class="tm-kv">
          <dt>Audio ID</dt><dd>A-00<?= htmlspecialchars($audio['audio_id']) ?></dd>
          <dt>File Name</dt><dd><?= htmlspecialchars($audio['file_name']) ?></dd>
          <dt>File Type</dt><dd><?= htmlspecialchars($audio['file_type']) ?></dd>
          <dt>File Size</dt><dd><?= htmlspecialchars($audio['file_size']) ?></dd>
          <dt>Upload Date</dt><dd><?= htmlspecialchars($audio['date_upload']) ?></dd>
          <dt>Uploaded By</dt><dd><?= htmlspecialchars($audio['user_name'] ?? 'System Ingestion Bot') ?></dd>
        </dl>
      </div>
    </div>
  </div>
</section>

<footer class="tm-footer">
  <div class="tm-main"><span class="accent">GS04 · BITP 3353 Multimedia Database</span> · UTeM</div>
</footer>
</body>
</html>