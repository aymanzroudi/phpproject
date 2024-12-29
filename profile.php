<?php
require_once 'config/database.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Get user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Get user's events
$stmt = $pdo->prepare("
    SELECT e.* 
    FROM events e 
    JOIN user_events ue ON e.id = ue.event_id 
    WHERE ue.user_id = ?
    ORDER BY e.date DESC
");
$stmt->execute([$_SESSION['user_id']]);
$events = $stmt->fetchAll();

// Get user's ratings
$stmt = $pdo->prepare("
    SELECT AVG(rating) as avg_rating, COUNT(*) as total_ratings 
    FROM user_ratings 
    WHERE rated_user_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$ratings = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - SportEvents</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main class="profile-page">
        <div class="profile-container">
            <div class="profile-header">
                <div class="profile-avatar">
                    <img src="<?php echo $user['avatar_url'] ?? 'images/default-avatar.png'; ?>" alt="Photo de profil">
                </div>
                <div class="profile-info">
                    <h1><?php echo htmlspecialchars($user['username']); ?></h1>
                    <div class="profile-stats">
                        <div class="stat">
                            <i class="fas fa-star"></i>
                            <span><?php echo number_format($ratings['avg_rating'], 1); ?>/5</span>
                            <small>(<?php echo $ratings['total_ratings']; ?> avis)</small>
                        </div>
                        <div class="stat">
                            <i class="fas fa-calendar-check"></i>
                            <span><?php echo count($events); ?> événements</span>
                        </div>
                        <div class="stat">
                            <i class="fas fa-trophy"></i>
                            <span><?php echo $user['achievements'] ?? 0; ?> réalisations</span>
                        </div>
                    </div>
                </div>
                <a href="edit-profile.php" class="btn-secondary">Modifier le profil</a>
            </div>

            <div class="profile-content">
                <div class="profile-section">
                    <h2>À propos</h2>
                    <p><?php echo htmlspecialchars($user['bio'] ?? 'Aucune bio renseignée'); ?></p>
                    <div class="profile-details">
                        <div class="detail">
                            <i class="fas fa-map-marker-alt"></i>
                            <span><?php echo htmlspecialchars($user['location'] ?? 'Non renseigné'); ?></span>
                        </div>
                        <div class="detail">
                            <i class="fas fa-running"></i>
                            <span>Sports favoris: <?php echo htmlspecialchars($user['favorite_sports'] ?? 'Non renseigné'); ?></span>
                        </div>
                        <div class="detail">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Membre depuis <?php echo date('F Y', strtotime($user['created_at'])); ?></span>
                        </div>
                    </div>
                </div>

                <div class="profile-section">
                    <h2>Événements participés</h2>
                    <div class="events-grid">
                        <?php foreach ($events as $event): ?>
                            <div class="event-card">
                                <div class="event-image">
                                    <img src="<?php echo htmlspecialchars($event['image_url']); ?>" alt="<?php echo htmlspecialchars($event['title']); ?>">
                                </div>
                                <div class="event-info">
                                    <span class="event-category"><?php echo htmlspecialchars($event['category']); ?></span>
                                    <h3><?php echo htmlspecialchars($event['title']); ?></h3>
                                    <p class="event-date">
                                        <i class="fas fa-calendar"></i>
                                        <?php echo date('d/m/Y', strtotime($event['date'])); ?>
                                    </p>
                                    <p class="event-location">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <?php echo htmlspecialchars($event['location']); ?>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <?php if (empty($events)): ?>
                            <p class="no-events">Aucun événement participé pour le moment.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="profile-section">
                    <h2>Avis reçus</h2>
                    <div class="ratings-container">
                        <?php
                        $stmt = $pdo->prepare("
                            SELECT ur.*, u.username, u.avatar_url 
                            FROM user_ratings ur
                            JOIN users u ON ur.rater_user_id = u.id
                            WHERE ur.rated_user_id = ?
                            ORDER BY ur.created_at DESC
                            LIMIT 5
                        ");
                        $stmt->execute([$_SESSION['user_id']]);
                        $reviews = $stmt->fetchAll();
                        ?>

                        <?php foreach ($reviews as $review): ?>
                            <div class="rating-card">
                                <div class="rating-header">
                                    <img src="<?php echo $review['avatar_url'] ?? 'images/default-avatar.png'; ?>" alt="Avatar">
                                    <div class="rating-info">
                                        <h4><?php echo htmlspecialchars($review['username']); ?></h4>
                                        <div class="stars">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="fas fa-star <?php echo $i <= $review['rating'] ? 'active' : ''; ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                    <span class="rating-date"><?php echo date('d/m/Y', strtotime($review['created_at'])); ?></span>
                                </div>
                                <p class="rating-comment"><?php echo htmlspecialchars($review['comment']); ?></p>
                            </div>
                        <?php endforeach; ?>
                        <?php if (empty($reviews)): ?>
                            <p class="no-ratings">Aucun avis reçu pour le moment.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
