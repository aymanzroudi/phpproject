<?php
require_once 'config/database.php';
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();

$message = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message_content = trim($_POST['message']);

    // Validation
    if (empty($name) || empty($email) || empty($subject) || empty($message_content)) {
        $error = "Tous les champs sont obligatoires.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "L'adresse email n'est pas valide.";
    } else {
        // Créer une nouvelle instance de PHPMailer
        $mail = new PHPMailer(true);

        try {
            // Configuration du serveur
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'aymengta13@gmail.com'; // Votre email Gmail
            $mail->Password = 'votre_mot_de_passe_d_application'; // Mot de passe d'application Gmail
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Destinataires
            $mail->setFrom($email, $name);
            $mail->addAddress('aymengta13@gmail.com', 'EventFlow Admin');
            $mail->addReplyTo($email, $name);

            // Contenu
            $mail->isHTML(true);
            $mail->Subject = "Contact EventFlow: " . $subject;
            $mail->Body = "
                <h3>Nouveau message de contact</h3>
                <p><strong>Nom:</strong> {$name}</p>
                <p><strong>Email:</strong> {$email}</p>
                <p><strong>Sujet:</strong> {$subject}</p>
                <p><strong>Message:</strong></p>
                <p>{$message_content}</p>
            ";

            $mail->send();
            $message = "Votre message a été envoyé avec succès! Nous vous répondrons dans les plus brefs délais.";
        } catch (Exception $e) {
            $error = "Une erreur est survenue lors de l'envoi du message. Erreur: {$mail->ErrorInfo}";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - EventFlow</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .contact-page {
            padding: 4rem 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .contact-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }

        .contact-form {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .contact-info {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .contact-info-item {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .contact-info-item i {
            font-size: 1.5rem;
            margin-right: 1rem;
            color: #007bff;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }

        .form-group textarea {
            min-height: 150px;
            resize: vertical;
        }

        .social-links {
            margin-top: 2rem;
        }

        .social-links a {
            font-size: 1.5rem;
            margin-right: 1rem;
            color: #007bff;
            transition: color 0.3s;
        }

        .social-links a:hover {
            color: #0056b3;
        }

        @media (max-width: 768px) {
            .contact-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main class="contact-page">
        <h1>Contactez-nous</h1>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>

        <div class="contact-container">
            <div class="contact-form">
                <h2>Envoyez-nous un message</h2>
                <form action="contact.php" method="POST">
                    <div class="form-group">
                        <label for="name">Nom</label>
                        <input type="text" id="name" name="name" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>

                    <div class="form-group">
                        <label for="subject">Sujet</label>
                        <input type="text" id="subject" name="subject" required>
                    </div>

                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" required></textarea>
                    </div>

                    <button type="submit" class="btn-primary">Envoyer le message</button>
                </form>
            </div>

            <div class="contact-info">
                <h2>Informations de contact</h2>
                
                <div class="contact-info-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <div>
                        <h3>Adresse</h3>
                        <p>Avenue Mohammed VI<br>
                           Résidence Al Andalous, Bloc B2<br>
                           40000 Marrakech, Maroc</p>
                    </div>
                </div>

                <div class="contact-info-item">
                    <i class="fas fa-phone"></i>
                    <div>
                        <h3>Téléphone</h3>
                        <p>+212 5 24 30 50 60</p>
                    </div>
                </div>

                <div class="contact-info-item">
                    <i class="fas fa-envelope"></i>
                    <div>
                        <h3>Email</h3>
                        <p>aymengta13@gmail.com</p>
                    </div>
                </div>

                <div class="contact-info-item">
                    <i class="fas fa-clock"></i>
                    <div>
                        <h3>Horaires</h3>
                        <p>Lundi - Vendredi: 9h - 18h<br>
                           Samedi: 10h - 15h<br>
                           Dimanche: Fermé</p>
                    </div>
                </div>

                <div class="social-links">
                    <h3>Suivez-nous</h3>
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
