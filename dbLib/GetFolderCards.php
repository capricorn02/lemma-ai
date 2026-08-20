<?php
require_once 'reconnectDB.php';
$folderId = (int)$_GET['folder_id'];
$stmt = $pdo->prepare("
    SELECT c.id, c.title, c.notes, c.updated_at 
    FROM cards c
    INNER JOIN card_folder_labels l ON c.id = l.card_id
    WHERE l.folder_id = ?
    ORDER BY c.id ASC
");
$stmt->execute([$folderId]);
$cards = $stmt->fetchAll() ?: [];
foreach ($cards as &$card) {
    $card['recid'] = $card['id']; // для w2ui grid
}
echo json_encode($cards, JSON_UNESCAPED_UNICODE);
exit;