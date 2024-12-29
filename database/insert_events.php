<?php
require_once '../config/database.php';

// Taux de conversion EUR vers MAD (approximatif)
$eur_to_mad = 10.86;

// Tableau des événements à insérer
$events = [
    [
        'title' => 'Tournoi de Football Inter-régional',
        'description' => 'Compétition entre les meilleures équipes régionales. Prix et trophées à gagner.',
        'category' => 'football',
        'date' => '2025-01-15 14:00:00',
        'location' => 'Stade Municipal',
        'price' => 200.00, // 20 EUR * 10.86
        'image_url' => 'https://images.unsplash.com/photo-1579952363873-27f3bade9f55?w=800',
        'max_participants' => 100
    ],
    [
        'title' => 'Championnat National de Basketball',
        'description' => 'Tournoi national avec les meilleures équipes de basketball du pays.',
        'category' => 'basketball',
        'date' => '2025-01-20 15:00:00',
        'location' => 'Palais des Sports',
        'price' => 250.00, // 25 EUR * 10.86
        'image_url' => 'https://images.unsplash.com/photo-1546519638-68e109498ffc?w=800',
        'max_participants' => 80
    ],
    [
        'title' => 'Open de Tennis',
        'description' => 'Tournoi open de tennis pour tous les niveaux avec plusieurs catégories.',
        'category' => 'tennis',
        'date' => '2025-01-25 09:00:00',
        'location' => 'Club de Tennis',
        'price' => 300.00, // 30 EUR * 10.86
        'image_url' => 'https://images.unsplash.com/photo-1595435934249-5df7ed86e1c0?w=800',
        'max_participants' => 50
    ],
    [
        'title' => 'Marathon de la Ville',
        'description' => 'Course annuelle à travers la ville avec plusieurs distances disponibles.',
        'category' => 'athletisme',
        'date' => '2025-02-01 08:00:00',
        'location' => 'Centre-ville',
        'price' => 150.00, // 15 EUR * 10.86
        'image_url' => 'https://images.unsplash.com/photo-1552674605-db6ffd4facb5?w=800',
        'max_participants' => 200
    ],
    [
        'title' => 'Compétition de Natation',
        'description' => 'Compétition régionale de natation toutes catégories.',
        'category' => 'natation',
        'date' => '2025-02-10 10:00:00',
        'location' => 'Piscine Olympique',
        'price' => 120.00, // 12 EUR * 10.86
        'image_url' => 'https://images.unsplash.com/photo-1519315901367-f34ff9154487?w=800',
        'max_participants' => 60
    ]
];

try {
    // Désactiver temporairement les contraintes de clé étrangère
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    
    // Vider les tables
    $pdo->exec("TRUNCATE TABLE user_events");
    $pdo->exec("TRUNCATE TABLE events");
    
    // Réactiver les contraintes de clé étrangère
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

    // Préparer la requête d'insertion
    $stmt = $pdo->prepare("
        INSERT INTO events (title, description, category, date, location, price, image_url, max_participants)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    // Insérer chaque événement
    foreach ($events as $event) {
        $stmt->execute([
            $event['title'],
            $event['description'],
            $event['category'],
            $event['date'],
            $event['location'],
            $event['price'],
            $event['image_url'],
            $event['max_participants']
        ]);
    }

    echo "Les événements ont été ajoutés avec succès!";
} catch (PDOException $e) {
    echo "Erreur lors de l'insertion des événements : " . $e->getMessage();
}
?>
