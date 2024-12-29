<?php
session_start();
require_once 'config/database.php';

header('Content-Type: application/json');

// Activer l'affichage des erreurs PHP
ini_set('display_errors', 1);
error_reporting(E_ALL);

try {
    $conditions = [];
    $params = [];

    // Log des paramètres reçus
    error_log("Paramètres reçus : " . print_r($_GET, true));

    // Filtrer par catégorie
    if (!empty($_GET['category'])) {
        $conditions[] = "e.category = ?";
        $params[] = $_GET['category'];
    }

    // Filtrer par date
    if (!empty($_GET['date_from'])) {
        $conditions[] = "e.date >= ?";
        $params[] = $_GET['date_from'];
    }
    if (!empty($_GET['date_to'])) {
        $conditions[] = "e.date <= ?";
        $params[] = $_GET['date_to'];
    }

    // Filtrer par prix
    if (isset($_GET['price_min']) && $_GET['price_min'] !== '') {
        $conditions[] = "e.price >= ?";
        $params[] = floatval($_GET['price_min']);
    }
    if (isset($_GET['price_max']) && $_GET['price_max'] !== '') {
        $conditions[] = "e.price <= ?";
        $params[] = floatval($_GET['price_max']);
    }

    // Recherche par mot-clé
    if (!empty($_GET['keyword'])) {
        $conditions[] = "(e.title LIKE ? OR e.description LIKE ? OR e.location LIKE ?)";
        $keyword = "%" . trim($_GET['keyword']) . "%";
        $params[] = $keyword;
        $params[] = $keyword;
        $params[] = $keyword;
    }

    // Disponibilité
    if (isset($_GET['available']) && $_GET['available'] === 'true') {
        $conditions[] = "(
            SELECT COUNT(*) 
            FROM user_events ue 
            WHERE ue.event_id = e.id 
            AND ue.status = 'registered'
        ) < e.max_participants";
    }

    // Requête de base
    $sql = "SELECT e.*, 
            (SELECT COUNT(*) FROM user_events ue WHERE ue.event_id = e.id AND ue.status = 'registered') as registered_participants,
            (SELECT COUNT(*) FROM user_events ue WHERE ue.event_id = e.id AND ue.status = 'waitlist') as waitlist_count
            FROM events e";

    // Ajouter les conditions si elles existent
    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }

    $sql .= " ORDER BY e.date ASC";

    // Log de la requête SQL et des paramètres
    error_log("Requête SQL : " . $sql);
    error_log("Paramètres : " . print_r($params, true));

    // Préparer et exécuter la requête
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Log des résultats
    error_log("Nombre d'événements trouvés : " . count($events));

    echo json_encode([
        'success' => true, 
        'events' => $events,
        'debug' => [
            'sql' => $sql,
            'params' => $params,
            'conditions' => $conditions
        ]
    ]);

} catch (PDOException $e) {
    error_log("Erreur PDO : " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Erreur lors de la recherche: ' . $e->getMessage(),
        'debug' => [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]
    ]);
} catch (Exception $e) {
    error_log("Erreur générale : " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Erreur inattendue: ' . $e->getMessage(),
        'debug' => [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]
    ]);
}
