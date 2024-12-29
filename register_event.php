<?php
session_start();
require_once 'config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Veuillez vous connecter pour vous inscrire à un événement.']);
    exit;
}

if (!isset($_POST['event_id'])) {
    echo json_encode(['success' => false, 'message' => 'ID de l\'événement manquant.']);
    exit;
}

$event_id = $_POST['event_id'];
$user_id = $_SESSION['user_id'];

try {
    // Vérifier si l'utilisateur est déjà inscrit
    $stmt = $pdo->prepare("SELECT status FROM user_events WHERE user_id = ? AND event_id = ?");
    $stmt->execute([$user_id, $event_id]);
    $existing = $stmt->fetch();

    if ($existing) {
        if ($existing['status'] === 'cancelled') {
            // Réactiver l'inscription
            $stmt = $pdo->prepare("UPDATE user_events SET status = 'registered', cancellation_date = NULL WHERE user_id = ? AND event_id = ?");
            $stmt->execute([$user_id, $event_id]);
            echo json_encode(['success' => true, 'message' => 'Inscription réactivée avec succès!']);
            exit;
        }
        echo json_encode(['success' => false, 'message' => 'Vous êtes déjà inscrit à cet événement.']);
        exit;
    }

    // Vérifier le nombre de participants
    $stmt = $pdo->prepare("
        SELECT e.max_participants,
               (SELECT COUNT(*) FROM user_events ue WHERE ue.event_id = e.id AND ue.status = 'registered') as current_participants
        FROM events e
        WHERE e.id = ?
    ");
    $stmt->execute([$event_id]);
    $event = $stmt->fetch();

    if (!$event) {
        echo json_encode(['success' => false, 'message' => 'Événement non trouvé.']);
        exit;
    }

    $status = 'registered';
    if ($event['current_participants'] >= $event['max_participants']) {
        $status = 'waitlist';
    }

    // Inscrire l'utilisateur
    $stmt = $pdo->prepare("INSERT INTO user_events (user_id, event_id, status) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $event_id, $status]);

    $message = $status === 'registered' 
        ? 'Inscription réussie!' 
        : 'L\'événement est complet. Vous avez été ajouté à la liste d\'attente.';

    echo json_encode(['success' => true, 'message' => $message, 'status' => $status]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'inscription: ' . $e->getMessage()]);
}
?>
