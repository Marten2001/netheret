<?php
require __DIR__ . '/includes/auth.php';
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/queries.php';
requireLogin();

$term = trim($_GET['q'] ?? '');
$articles = $term !== '' ? searchKnowledge($pdo, $term): $pdo->query("SELECT id, title, category FROM knowledge_articles ORDER BY")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Knowledge base</title><link rel="stylesheet" href="css/ux.css"></head>
<body>
<main>
  <h1>Knowledge base</h1>
  <form method="get" action="knowledge.php">
    <label for="q">Search articles</label>
    <input type="search" id="q" name="q" value="<?= htmlspecialchars($term) ?>">
    <button type="submit">Search</button>
  </form>

  <?php if (empty($articles)): ?>
    <p>No articles found.</p>
  <?php else: ?>
    <ul>
        <?php foreach ($articles as $a): ?>
            <li><?= htmlspecialchars($a['title']) ?> (<?= htmlspecialchars($a['category']) ?>)</li>
        <?php endforeach; ?>    
    </ul> 
  <?php endif; ?>  
</main>
</body>
</html>