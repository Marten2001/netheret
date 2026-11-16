queries.txt
100%
<?php
function getEngineerSchedule(PDO $pdo, int $engineerId): array
{
    $stmt = $pdo->prepare(
        "SELECT j.id, j.scheduled_start, j.site_postcode, j.job_brief, t.title AS ticket_title
         FROM jobs j
         JOIN tickets t ON t.id = j.ticket_id
         WHERE j.engineer_id = ? AND j.status = 'scheduled'
         ORDER BY j.scheduled_start"
    );
    $stmt->execute([$engineerId]);
    return $stmt->fetchAll();
}

function searchKnowledge(PDO $pdo, string $term): array
{
    $like = '%' . $term . '%';
    $stmt = $pdo->prepare("SELECT id, title, category FROM knowledge_articles WHERE title LIKE ? OR body LIKE ? ORDER BY title");
    $stmt->execute([$like, $like]);
    return $stmt->fetchAll();
}

function createJob(PDO $pdo, int $ticketId, int $engineerId, string $start, string $postcode, string $brief): int
{
    $stmt = $pdo->prepare("INSERT INTO jobs (ticket_id, scheduled_start, site_postcode, job_brief) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$ticketId, $engineerId, $start, $postcode, $brief]);
    return (int)$pdo->lastInsertId();
}

function updateJobStatus(PDO $pdo, int $jobId, string $status): bool
{
    $allowed = ['scheduled', 'in_progress', 'completed', 'cancelled'];
    if (!in_array($status, $allowed, true)) {
        return false;
    }
    $stmt = $pdo->prepare("UPDATE jobs SET status = ? WHERE id = ?");
    $stmt->execute([$status, $jobId]);
    return true;
}

function deleteJob(PDO $pdo, int $jobId): bool
{
    $stmt = $pdo->prepare("DELETE FROM jobs WHERE id = ?");
    $stmt->execute([$jobId]);
    return $stmt->rowCount() > 0;
}
