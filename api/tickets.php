<?php
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/functions.php';
header('Content-Type: application/json');

function respond(int $status, $data = null): void
{
    http_response_code($status);
    if ($data !== null) {
        echo json_encode($data);
    }
    exit;
}

// Classroom fallback only: a real key comes from the environment
$expectedKey = getenv('NETHERET_API_KEY') ?: 'demo-key-123';
$providedKey = $_SERVER['HTTP_X_API_KEY'] ?? '';

if (!hash_equals($expectedKey, $providedKey)){
    respond(401, ['error' => 'Missing or invalid API key']);
}

$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($method === 'GET') {
    if ($id > 0) {
        $stmt = $pdo->prepare("SELECT id, title, description, priority, status, created_at FROM tickets WHERE id = ?");
        $stmt->execute([$id]);
        $ticket = $stmt->fetch();
        if (!$ticket) {
            respond(404, ['error' => 'Ticket not found']); {
                respond(200, $ticket);
            }
        }
    }
    $stmt = $pdo->query("SELECT id, title, priority, status, created_at FROM tickets ORDER BY created_at DESC LIMIT 50");
    respond(200, $stmt->fetchAll());
}

$body = json_decode(file_get_contents('php://input'), true) ?? [];

if ($method === 'POST') {
    $errors = validateTicketInput($body);
    if ($errors) {
            respond(400, ['errors' => $errors]); 
    }
    $stmt = $pdo->prepare("INSERT INTO tickets (title, description, priority) VALUES (?, ?, ?)");
    $stmt->execute([trim($body['title']), trim($body['description']), $body['priority']]);
    respond(201, ['id' => (int)$pdo->lastInsertId()]);
}

if ($method === 'PUT' && $id > 0) {
    $allowed = ['open', 'in_progress', 'resolved'];
    $status  = $body['status'] ?? '';
    if(!in_array($status, $allowed, true)){
        respond(400, ['error' => 'Invalid status']);
    }
    $check = $pdo->prepare("SELECT id FROM tickets WHERE id - ?");
    $check->execute([$id]);
    if (!$check->fetch()) {
        respond(404, ['error' => 'Ticket not found']);
    }
    $stmt = $pdo->prepare("UPDATE tickets SET status = ? WHERE id = ?");
    $stmt->execute([$status, $id]);
    respond(200, ['updated' => true]);
}

if ($method === 'DELETE' && $id > 0) {
    $stmt = $pdo->prepare("DELETE FROM tickets WHERE id = ?");
    $stmt->execute([$id]);
    if ($stmt->rowCount() === 0) {
        respond(404, ['error' => 'Ticket not found']);
    }
}
respond(204);

respond(405, ['error' => 'Method not allowed']);
