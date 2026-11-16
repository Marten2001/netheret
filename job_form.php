job_form.txt
100%
<?php
require __DIR__ . '/includes/auth.php';
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/queries.php';
require __DIR__ . '/includes/functions.php';
requireRole('manager');

$errors    = [];
$tickets   = $pdo->query("SELECT id, title FROM tickets WHERE status <> 'resolved' ORDER BY id")->fetchAll();
$engineers = $pdo->query("SELECT id, username FROM users WHERE role = 'engineer' ORDER BY username")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ticketId   = (int)($_POST['ticket_id'] ?? 0);
    $engineerId = (int)($_POST['engineer_id'] ?? 0);
    $postcode   = strtoupper(trim($_POST['site_postcode'] ?? ''));
    $brief      = trim($_POST['job_brief'] ?? '');
    $start      = DateTime::createFromFormat('Y-m-d\TH:i', $_POST['scheduled_start'] ?? '');

    if ($ticketId === 0 || $engineerId === 0) {
        $errors[] = 'Choose a ticket and an engineer.';
    }
    if ($start === false) {
        $errors[] = 'Enter a valid date and time.';
    }
    if (!isValidPostcode($postcode)) {
        $errors[] = 'Enter a valid UK postcode.';
    }

    if (empty($errors)) {
        try {
            createJob($pdo, $ticketId, $engineerId, $start->format('Y-m-d H:i:s'), $postcode, $brief);
            header('Location: schedule.php');
            exit;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $errors[] = 'The job could not be saved. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Schedule a job</title><link rel="stylesheet" href="css/ux.css"></head>
<body>
<main>
  <h1>Schedule a job</h1>
  <?php if ($errors): ?>
    <ul class="error" role="alert"><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
  <?php endif; ?>

  <form method="post" action="job_form.php">
    <label for="ticket_id">Ticket</label>
    <select id="ticket_id" name="ticket_id">
      <option value="">-- Choose --</option>
      <?php foreach ($tickets as $t): ?>
        <option value="<?= (int)$t['id'] ?>"><?= htmlspecialchars($t['id'] . ': ' . $t['title']) ?></option>
      <?php endforeach; ?>
    </select>

    <label for="engineer_id">Engineer</label>
    <select id="engineer_id" name="engineer_id">
      <option value="">-- Choose --</option>
      <?php foreach ($engineers as $e): ?>
        <option value="<?= (int)$e['id'] ?>"><?= htmlspecialchars($e['username']) ?></option>
      <?php endforeach; ?>
    </select>

    <label for="scheduled_start">Date and time</label>
    <input type="datetime-local" id="scheduled_start" name="scheduled_start">

    <label for="site_postcode">Site postcode</label>
    <input type="text" id="site_postcode" name="site_postcode" maxlength="10">

    <label for="job_brief">Job brief</label>
    <textarea id="job_brief" name="job_brief" rows="4"></textarea>

    <button type="submit">Schedule job</button>
  </form>
</main>
</body>
</html>
