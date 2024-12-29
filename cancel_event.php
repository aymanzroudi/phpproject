<?php
session_start();
require_once 'config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Veuillez vous connecter.']);
    exit;
}

if (!isset($_POST['event_id'])) {
    echo json_encode(['success' => false, 'message' => 'ID de l\'événement manquant.']);
    exit;
}

$event_id = $_POST['event_id'];
$user_id = $_SESSION['user_id'];

try {
    $pdo->beginTransaction();

    // Mettre à jour le statut de l'inscription
    $stmt = $pdo->prepare("
        UPDATE user_events 
        SET status = 'cancelled', 
            cancellation_date = CURRENT_TIMESTAMP 
        WHERE user_id = ? AND event_id = ? AND status != 'cancelled'
    ");
    $stmt->execute([$user_id, $event_id]);

    if ($stmt->rowCount() === 0) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Aucune inscription active trouvée.']);
        exit;
    }

    // Vérifier s'il y a des personnes en liste d'attente
    $stmt = $pdo->prepare("
        SELECT ue.user_id 
        FROM user_events ue 
        WHERE ue.event_id = ? AND ue.status = 'waitlist' 
        ORDER BY ue.registration_date ASC 
        LIMIT 1
    ");
    $stmt->execute([$event_id]);
    $nextPerson = $stmt->fetch();

    if ($nextPerson) {
        // Promouvoir la première personne de la liste d'attente
        $stmt = $pdo->prepare("
            UPDATE user_events 
            SET status = 'registered' 
            WHERE user_id = ? AND event_id = ?
        ");
        $stmt->execute([$nextPerson['user_id'], $event_id]);
    }

    $pdo->commit();
    echo json_encode(['success' => true, 'message' => 'Inscription annulée avec succès.']);

} catch (PDOException $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'annulation: ' . $e->getMessage()]);
}
?>
