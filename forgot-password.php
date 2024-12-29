<?php
require_once 'config/database.php';
session_start();

$message = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);

    // Vérifier si l'email existe
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($user = $stmt->fetch()) {
        // Générer un token unique
        $token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        // Sauvegarder le token dans la base de données
        $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_token_expiry = ? WHERE email = ?");
        $stmt->execute([$token, $expiry, $email]);
        
        // Envoyer l'email (à implémenter avec un service d'envoi d'emails)
        $resetLink = "http://" . $_SERVER['HTTP_HOST'] . "/php_project/reset-password.php?token=" . $token;
        
        // Pour le moment, afficher le lien directement (en développement uniquement)
        $message = "Un lien de réinitialisation a été envoyé à votre email. <br>
                   Lien de développement : <a href='$resetLink'>Réinitialiser le mot de passe</a>";
    } else {
        $error = "Aucun compte n'est associé à cet email.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié - EventFlow</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main class="auth-page">
        <div class="auth-container">
            <h2>Mot de passe oublié</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <?php if ($message): ?>
                <div class="alert alert-success"><?php echo $message; ?></div>
            <?php endif; ?>

            <form action="forgot-password.php" method="POST" class="auth-form">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <button type="submit" class="btn-primary">Réinitialiser le mot de passe</button>
            </form>

            <p class="auth-link">Retour à la <a href="login.php">connexion</a></p>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
