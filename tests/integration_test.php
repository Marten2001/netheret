<?php
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/queries.php';

$ticketId = (int)$pdo->query("SELECT id FROM tickets ORDER BY id LIMIT 1")->fetchColumn();
$engineerId = (int)$pdo->query("SELECT id FROM users WHERE role = 'engineer' ORDER BY id LIMIT 1")->fetchColumn();

if (!$ticketId || !$engineerId) {
    exit("Set-up needed: at least one ticket and one engineer must exist.\n");
}

function report(string $name, bool $ok): void
{
    echo ($ok ? 'PASS' : 'FAIL') . " - $name\n";
}

$testBrief = 'TEST JOB - integration test';

try {
    $jobId = createJob($pdo, $ticketId, $engineerId, '2030-01-01 09:00:00', 'BR8 7RE', $testBrief);
    $scheduleIds = array_map('intval', array_column(getEngineerSchedule($pdo, $engineerId), 'id'));
    report('created job appears on the engineer schedule', in_array($jobId, $scheduleIds, true));

    updateJobStatus($pdo, $jobId, 'completed');
    $ids = array_map('intval', array_column(getEngineerSchedule($pdo, $engineerId), 'id'));
    report('completed job leaves the schedule', !in_array($jobId, $ids, true));

    deleteJob($pdo, $jobId);
    $count = $pdo->prepare("SELECT COUNT(*) FROM jobs WHERE id = ?");
    $count->execute([$jobId]);
    report('deleted job is gone from the database', (int)$count->fetchColumn() === 0);

    try {
        createJob($pdo, 999999, $engineerId, '2030-01-01 09:00:00', 'BR8 7RE', $testBrief);
        report('foreign key rejects a job for a missing ticket', false);
    } catch (PDOException $e) {
        report('foreign key rejects a job for a missing ticket', true);
    }
} finally {
    $cleanup = $pdo->prepare("DELETE FROM jobs WHERE job_brief = ?");
    $cleanup->execute([$testBrief]);
}