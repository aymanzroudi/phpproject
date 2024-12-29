<?php
session_start();
require_once 'config/database.php';

// Récupérer les catégories uniques
$stmt = $pdo->query("SELECT DISTINCT category FROM events ORDER BY category");
$categories = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Récupérer les prix min et max
$stmt = $pdo->query("SELECT MIN(price) as min_price, MAX(price) as max_price FROM events");
$price_range = $stmt->fetch();

// Récupérer tous les événements par défaut
$stmt = $pdo->query("
    SELECT e.*, 
    (SELECT COUNT(*) FROM user_events ue WHERE ue.event_id = e.id AND ue.status = 'registered') as registered_participants,
    (SELECT COUNT(*) FROM user_events ue WHERE ue.event_id = e.id AND ue.status = 'waitlist') as waitlist_count
    FROM events e 
    ORDER BY date ASC
");
$events = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Événements - SportEvents</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/nouislider/dist/nouislider.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main class="events-page">
        <div class="events-header">
            <h1>Tous les Événements Sportifs</h1>
            
            <!-- Advanced Search Form -->
            <div class="search-filters">
                <form id="searchForm" class="search-form">
                    <div class="search-row">
                        <div class="search-group">
                            <label for="keyword">Rechercher</label>
                            <input type="text" id="keyword" name="keyword" placeholder="Mot-clé...">
                        </div>
                        
                        <div class="search-group">
                            <label for="category">Catégorie</label>
                            <select id="category" name="category">
                                <option value="">Toutes les catégories</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo htmlspecialchars($category); ?>">
                                        <?php echo htmlspecialchars(ucfirst($category)); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="search-row">
                        <div class="search-group">
                            <label for="date_from">Date début</label>
                            <input type="text" id="date_from" name="date_from" class="datepicker" placeholder="De...">
                        </div>
                        
                        <div class="search-group">
                            <label for="date_to">Date fin</label>
                            <input type="text" id="date_to" name="date_to" class="datepicker" placeholder="À...">
                        </div>
                    </div>

                    <div class="search-row">
                        <div class="search-group">
                            <label for="price_range">Prix (MAD)</label>
                            <div id="price_slider"></div>
                            <div class="price-inputs">
                                <input type="number" id="price_min" name="price_min" 
                                       value="<?php echo floor($price_range['min_price']); ?>">
                                <span>à</span>
                                <input type="number" id="price_max" name="price_max" 
                                       value="<?php echo ceil($price_range['max_price']); ?>">
                            </div>
                        </div>
                        
                        <div class="search-group">
                            <label class="checkbox-label">
                                <input type="checkbox" id="available" name="available" value="true">
                                Afficher uniquement les événements disponibles
                            </label>
                        </div>
                    </div>

                    <div class="search-actions">
                        <button type="submit" class="btn-primary">Rechercher</button>
                        <button type="reset" class="btn-secondary">Réinitialiser</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="events-grid" id="events-container">
            <?php foreach ($events as $event): ?>
            <div class="event-card" data-category="<?php echo htmlspecialchars($event['category']); ?>">
                <div class="event-image">
                    <img src="<?php echo htmlspecialchars($event['image_url']); ?>" 
                         alt="<?php echo htmlspecialchars($event['title']); ?>">
                    <?php if ($event['registered_participants'] >= $event['max_participants']): ?>
                        <div class="event-status full">COMPLET</div>
                    <?php endif; ?>
                </div>
                <div class="event-info">
                    <span class="event-category"><?php echo htmlspecialchars(ucfirst($event['category'])); ?></span>
                    <h3><?php echo htmlspecialchars($event['title']); ?></h3>
                    <p class="event-description"><?php echo htmlspecialchars($event['description']); ?></p>
                    <p class="event-date">
                        <i class="fas fa-calendar"></i> 
                        <?php echo date('d F Y à H:i', strtotime($event['date'])); ?>
                    </p>
                    <p class="event-location">
                        <i class="fas fa-map-marker-alt"></i> 
                        <?php echo htmlspecialchars($event['location']); ?>
                    </p>
                    <p class="event-price" data-price="<?php echo $event['price']; ?>">
                        <i class="fas fa-ticket-alt"></i> 
                        <?php echo number_format($event['price'], 2); ?> MAD
                    </p>
                    <p class="event-participants">
                        <i class="fas fa-users"></i>
                        <?php echo $event['registered_participants']; ?> / <?php echo $event['max_participants']; ?> participants
                        <?php if ($event['waitlist_count'] > 0): ?>
                            <span class="waitlist-count">(<?php echo $event['waitlist_count']; ?> en liste d'attente)</span>
                        <?php endif; ?>
                    </p>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <button class="btn-primary register-btn" 
                                data-event-id="<?php echo $event['id']; ?>"
                                <?php echo $event['registered_participants'] >= $event['max_participants'] ? 'data-waitlist="true"' : ''; ?>>
                            <?php echo $event['registered_participants'] >= $event['max_participants'] 
                                ? 'Rejoindre la liste d\'attente' 
                                : 'S\'inscrire'; ?>
                        </button>
                    <?php else: ?>
                        <a href="login.php" class="btn-primary">Connectez-vous pour vous inscrire</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div id="no-results" class="no-results" style="display: none;">
            <p>Aucun événement ne correspond à vos critères de recherche.</p>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/fr.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/nouislider/distribute/nouislider.min.js"></script>
    <script src="js/events.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/nouislider/distribute/nouislider.min.js"></script>
</body>
</html>
