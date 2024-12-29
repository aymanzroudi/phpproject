<?php
session_start();

// Vérifier si l'admin est connecté
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../config/database.php';

$error = '';
$success = '';
$event = null;

// Vérifier si un ID est fourni
if (!isset($_GET['id'])) {
    header('Location: dashboard.php');
    exit;
}

// Récupérer les informations de l'événement
try {
    $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $event = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$event) {
        header('Location: dashboard.php');
        exit;
    }
} catch(PDOException $e) {
    $error = 'Erreur lors de la récupération de l\'événement : ' . $e->getMessage();
}

// Traitement du formulaire de modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Récupérer et valider les données
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';
        $date = $_POST['date'] ?? '';
        $location = $_POST['location'] ?? '';
        $price = $_POST['price'] ?? 0;
        $category = $_POST['category'] ?? '';
        $max_participants = $_POST['max_participants'] ?? 0;
        $image_url = $_POST['image_url'] ?? '';

        // Validation basique
        if (empty($title) || empty($date) || empty($location)) {
            throw new Exception('Veuillez remplir tous les champs obligatoires');
        }

        // Mettre à jour l'événement
        $sql = "UPDATE events SET 
                title = ?, 
                description = ?, 
                date = ?, 
                location = ?, 
                price = ?, 
                category = ?, 
                max_participants = ?, 
                image_url = ?
                WHERE id = ?";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $title,
            $description,
            $date,
            $location,
            $price,
            $category,
            $max_participants,
            $image_url,
            $_GET['id']
        ]);

        // Mettre à jour les données affichées
        $event = [
            'id' => $_GET['id'],
            'title' => $title,
            'description' => $description,
            'date' => $date,
            'location' => $location,
            'price' => $price,
            'category' => $category,
            'max_participants' => $max_participants,
            'image_url' => $image_url
        ];

        $success = 'Événement modifié avec succès';
    } catch(Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - Modifier un événement</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        .admin-form-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .error-message {
            color: red;
            margin-bottom: 15px;
        }
        .success-message {
            color: green;
            margin-bottom: 15px;
        }
        .preview-image {
            max-width: 200px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="admin-form-container">
        <h1>Modifier l'événement</h1>
        
        <?php if ($error): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="title">Titre *</label>
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($event['title']); ?>" required>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="4"><?php echo htmlspecialchars($event['description']); ?></textarea>
            </div>

            <div class="form-group">
                <label for="date">Date et heure *</label>
                <input type="datetime-local" id="date" name="date" value="<?php echo htmlspecialchars($event['date']); ?>" required>
            </div>

            <div class="form-group">
                <label for="location">Lieu *</label>
                <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($event['location']); ?>" required>
            </div>

            <div class="form-group">
                <label for="price">Prix (MAD)</label>
                <input type="number" id="price" name="price" value="<?php echo htmlspecialchars($event['price']); ?>" min="0" step="0.01">
            </div>

            <div class="form-group">
                <label for="category">Catégorie</label>
                <select id="category" name="category">
                    <option value="">Sélectionnez une catégorie</option>
                    <option value="Sport" <?php echo $event['category'] === 'Sport' ? 'selected' : ''; ?>>Sport</option>
                    <option value="Culture" <?php echo $event['category'] === 'Culture' ? 'selected' : ''; ?>>Culture</option>
                    <option value="Musique" <?php echo $event['category'] === 'Musique' ? 'selected' : ''; ?>>Musique</option>
                    <option value="Technologie" <?php echo $event['category'] === 'Technologie' ? 'selected' : ''; ?>>Technologie</option>
                    <option value="Autre" <?php echo $event['category'] === 'Autre' ? 'selected' : ''; ?>>Autre</option>
                </select>
            </div>

            <div class="form-group">
                <label for="max_participants">Nombre maximum de participants</label>
                <input type="number" id="max_participants" name="max_participants" value="<?php echo htmlspecialchars($event['max_participants']); ?>" min="1">
            </div>

            <div class="form-group">
                <label for="image_url">URL de l'image</label>
                <input type="url" id="image_url" name="image_url" value="<?php echo htmlspecialchars($event['image_url']); ?>">
                <?php if ($event['image_url']): ?>
                    <img src="<?php echo htmlspecialchars($event['image_url']); ?>" alt="Aperçu" class="preview-image">
                <?php endif; ?>
            </div>

            <div class="form-group">
                <button type="submit" class="btn-primary">Enregistrer les modifications</button>
                <a href="dashboard.php" class="btn-secondary">Retour au tableau de bord</a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        flatpickr("#date", {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            defaultDate: "<?php echo $event['date']; ?>"
        });

        // Prévisualisation de l'image
        document.getElementById('image_url').addEventListener('change', function() {
            const previewImage = document.querySelector('.preview-image');
            if (previewImage) {
                previewImage.src = this.value;
            } else if (this.value) {
                const img = document.createElement('img');
                img.src = this.value;
                img.alt = 'Aperçu';
                img.className = 'preview-image';
                this.parentNode.appendChild(img);
            }
        });
    </script>
</body>
</html>
