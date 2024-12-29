<?php
session_start();
require_once 'config/database.php';

// Récupérer les événements à venir
$stmt = $pdo->query("SELECT * FROM events ORDER BY date ASC LIMIT 3");
$events = $stmt->fetchAll();

// Définir les images par défaut si aucun événement n'est trouvé
if (empty($events)) {
    $default_events = [
        [
            'title' => 'Championnat de Football',
            'description' => 'Rejoignez-nous pour le plus grand tournoi de football de la région',
            'image_url' => 'https://images.unsplash.com/photo-1461896836934-ffe607ba8211?w=1200'
        ],
        [
            'title' => 'Marathon de la Ville',
            'description' => 'Participez à notre marathon annuel à travers la ville',
            'image_url' => 'https://images.unsplash.com/photo-1577471488278-16eec37ffcc2?w=1200'
        ],
        [
            'title' => 'Tournoi de Tennis',
            'description' => 'Affrontez les meilleurs joueurs de tennis de la région',
            'image_url' => 'https://images.unsplash.com/photo-1519861531473-9200262188bf?w=1200'
        ]
    ];
    $events = $default_events;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SportEvents - Accueil</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main>
        <!-- Hero Section avec Slider -->
        <section class="hero-slider">
            <?php foreach ($events as $index => $event): ?>
            <div class="slide <?php echo $index === 0 ? 'active' : ''; ?>">
                <img src="<?php echo htmlspecialchars($event['image_url']); ?>" alt="<?php echo htmlspecialchars($event['title']); ?>">
                <div class="slide-content">
                    <h2><?php echo htmlspecialchars($event['title']); ?></h2>
                    <p><?php echo htmlspecialchars($event['description']); ?></p>
                    <a href="evenements.php" class="btn-primary">En savoir plus</a>
                </div>
            </div>
            <?php endforeach; ?>
            <button class="slider-btn prev"><i class="fas fa-chevron-left"></i></button>
            <button class="slider-btn next"><i class="fas fa-chevron-right"></i></button>
            <div class="slider-dots"></div>
        </section>

        <!-- Featured Events Section -->
        <section class="featured-events">
            <h2>Événements à venir</h2>
            <div class="events-grid">
                <?php foreach ($events as $event): ?>
                <div class="event-card" data-category="<?php echo htmlspecialchars($event['category'] ?? ''); ?>">
                    <div class="event-image">
                        <img src="<?php echo htmlspecialchars($event['image_url']); ?>" alt="<?php echo htmlspecialchars($event['title']); ?>">
                    </div>
                    <div class="event-info">
                        <span class="event-category"><?php echo htmlspecialchars($event['category'] ?? 'Sport'); ?></span>
                        <h3><?php echo htmlspecialchars($event['title']); ?></h3>
                        <p class="event-description"><?php echo htmlspecialchars($event['description']); ?></p>
                        <?php if (isset($event['date'])): ?>
                        <p class="event-date"><i class="fas fa-calendar"></i> <?php echo date('d F Y', strtotime($event['date'])); ?></p>
                        <?php endif; ?>
                        <?php if (isset($event['location'])): ?>
                        <p class="event-location"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($event['location']); ?></p>
                        <?php endif; ?>
                        <?php if (isset($event['price'])): ?>
                        <p class="event-price" data-price="<?php echo $event['price']; ?>">
                            <i class="fas fa-ticket-alt"></i> <?php echo number_format($event['price'], 2); ?> MAD
                        </p>
                        <?php endif; ?>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <button class="btn-primary" onclick="registerForEvent(<?php echo $event['id'] ?? 0; ?>)">S'inscrire</button>
                        <?php else: ?>
                            <a href="login.php" class="btn-primary">Connectez-vous pour vous inscrire</a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="view-all-events">
                <a href="evenements.php" class="btn-secondary">Voir tous les événements</a>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script src="js/main.js"></script>
</body>
</html>
