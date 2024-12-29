<?php
require_once 'config/database.php';
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$success = '';
$error = '';

// Récupérer les données actuelles de l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $bio = trim($_POST['bio']);
    $location = trim($_POST['location']);
    $favorite_sports = trim($_POST['favorite_sports']);
    
    // Vérifier si l'email est déjà utilisé par un autre utilisateur
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->execute([$email, $_SESSION['user_id']]);
    if ($stmt->rowCount() > 0) {
        $error = "Cette adresse email est déjà utilisée.";
    } else {
        // Traitement de l'upload de l'avatar
        $avatar_url = $user['avatar_url']; // Garder l'ancien avatar par défaut
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['avatar']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (in_array($ext, $allowed)) {
                $new_filename = uniqid() . '.' . $ext;
                $upload_dir = 'uploads/avatars/';
                
                // Créer le dossier s'il n'existe pas
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                if (move_uploaded_file($_FILES['avatar']['tmp_name'], $upload_dir . $new_filename)) {
                    $avatar_url = $upload_dir . $new_filename;
                }
            }
        }

        // Mise à jour du profil
        $stmt = $pdo->prepare("
            UPDATE users 
            SET username = ?, email = ?, bio = ?, location = ?, 
                favorite_sports = ?, avatar_url = ?
            WHERE id = ?
        ");
        
        if ($stmt->execute([
            $username, $email, $bio, $location, 
            $favorite_sports, $avatar_url, $_SESSION['user_id']
        ])) {
            $_SESSION['username'] = $username; // Mettre à jour le nom d'utilisateur dans la session
            $success = "Profil mis à jour avec succès!";
            
            // Recharger les données de l'utilisateur
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch();
        } else {
            $error = "Une erreur est survenue lors de la mise à jour du profil.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier le profil - SportEvents</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main class="edit-profile-page">
        <div class="edit-profile-container">
            <h2>Modifier mon profil</h2>

            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <form action="edit-profile.php" method="POST" enctype="multipart/form-data" class="edit-profile-form">
                <div class="form-group">
                    <label for="avatar">Photo de profil</label>
                    <?php if ($user['avatar_url']): ?>
                        <div class="current-avatar">
                            <img src="<?php echo htmlspecialchars($user['avatar_url']); ?>" alt="Avatar actuel">
                        </div>
                    <?php endif; ?>
                    <input type="file" id="avatar" name="avatar" accept="image/*">
                </div>

                <div class="form-group">
                    <label for="username">Nom d'utilisateur</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="bio">Bio</label>
                    <textarea id="bio" name="bio" rows="4"><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="location">Localisation</label>
                    <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($user['location'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="favorite_sports">Sports favoris</label>
                    <input type="text" id="favorite_sports" name="favorite_sports" value="<?php echo htmlspecialchars($user['favorite_sports'] ?? ''); ?>">
                    <small>Séparez les sports par des virgules</small>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">Enregistrer les modifications</button>
                    <a href="profile.php" class="btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
