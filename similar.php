<?php
require_once 'db.php';
$ref_id = isset($_GET['id']) ? intval($_GET['id']) : 1;

$stmt = $pdo->prepare("SELECT A.*, L.language_name, G.genre_name, F.tempo, F.pitch FROM AUDIO A 
                       LEFT JOIN LANGUAGE L ON A.languange_id = L.language_id
                       LEFT JOIN GENRE G ON A.genre_id = G.genre_id
                       LEFT JOIN AUDIO_FEATURE F ON A.audio_id = F.audio_id WHERE A.audio_id = ?");
$stmt->execute([$ref_id]); $ref_track = $stmt->fetch();

$matches = [];
if ($ref_track) {
    $stmtCand = $pdo->prepare("SELECT A.*, L.language_name, G.genre_name, F.tempo, F.pitch FROM AUDIO A
                               LEFT JOIN LANGUAGE L ON A.languange_id = L.language_id
                               LEFT JOIN GENRE G ON A.genre_id = G.genre_id
                               JOIN AUDIO_FEATURE F ON A.audio_id = F.audio_id WHERE A.audio_id != ?");
    $stmtCand->execute([$ref_id]); $candidates = $stmtCand->fetchAll();

    foreach ($candidates as $c) {
        $tempo_diff = abs($c['tempo'] - $ref_track['tempo']);
        $pitch_diff = abs($c['pitch'] - $ref_track['pitch']);
        $distance = ($tempo_diff * 0.6) + ($pitch_diff * 0.4);
        $similarity = max(0, min(100, 100 - $distance));
        
        $c['score'] = round($similarity, 0);
        $matches[] = $c;
    }
    usort($matches, function($a, $b) { return $b['score'] <=> $a['score']; });
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Similar Audio — AudioLang (CBR)</title>
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
      <li><a href="search.php">Search</a></li>
      <li><a href="similar.php" class="active">Similar</a></li>
    </ul>
  </div>
</header>

<section class="tm-section">
  <div class="tm-main">
    <h2>Find Similar Audio</h2>
    <p class="sub">Content-Based Retrieval (CBR) — match tracking distances using normalized metric matrices.</p>

    <?php if ($ref_track): ?>
    <div class="tm-panel">
      <h3>Reference Track Target</h3>
      <div class="tm-twocol">
        <div>
          <h4 style="margin-bottom:6px;"><?= htmlspecialchars($ref_track['file_name']) ?></h4>
          <div class="meta" style="margin-bottom:8px;">
            <span class="tm-tag blue"><?= htmlspecialchars($ref_track['language_name']) ?></span> 
            <span class="tm-tag gray"><?= htmlspecialchars($ref_track['genre_name']) ?></span> 
            <span class="tm-tag green"><?= htmlspecialchars($ref_track['file_type']) ?></span>
          </div>
        </div>
        <dl class="tm-kv">
          <dt>Tempo</dt><dd><?= $ref_track['tempo'] ?> BPM</dd>
          <dt>Pitch</dt><dd><?= $ref_track['pitch'] ?> Hz</dd>
        </dl>
      </div>
    </div>

    <h3 style="margin:30px 0 14px;">Top Dynamic Matches</h3>
    <table class="tm-table">
      <thead>
        <tr><th>#</th><th>File Name</th><th>Language</th><th>Genre</th><th>Tempo</th><th>Pitch</th><th>Similarity Score</th><th>Actions</th></tr>
      </thead>
      <tbody>
        <?php $idx = 1; foreach ($matches as $m): ?>
        <tr>
          <td><?= $idx++ ?></td>
          <td><?= htmlspecialchars($m['file_name']) ?></td>
          <td><span class="tm-tag"><?= htmlspecialchars($m['language_name']) ?></span></td>
          <td><?= htmlspecialchars($m['genre_name']) ?></td>
          <td><?= $m['tempo'] ?> BPM</td>
          <td><?= $m['pitch'] ?> Hz</td>
          <td><strong style="color:#ee5057;"><?= $m['score'] ?>%</strong></td>
          <td><a href="detail.php?id=<?= $m['audio_id'] ?>" class="btn btn-ghost">View</a></td>
        </tr>
        <?php endforeach; if(empty($matches)) echo "<tr><td colspan='8'>No alternative indexed rows recorded to rank.</td></tr>"; ?>
      </tbody>
    </table>
    <?php else: echo "<p>Select or upload a baseline track from the library tab first.</p>"; endif; ?>
  </div>
</section>

<footer class="tm-footer">
  <div class="tm-main"><span class="accent">GS04 · BITP 3353 Multimedia Database</span> · UTeM</div>
</footer>
</body>
</html>