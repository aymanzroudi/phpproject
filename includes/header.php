<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventFlow</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .auth-section {
            position: relative;
            margin-left: auto;
        }

        .profile-icon {
            cursor: pointer;
            padding: 8px;
            display: flex;
            align-items: center;
        }

        .profile-icon i {
            font-size: 24px;
            color: #333;
        }

        .profile-menu {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            min-width: 200px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            z-index: 1000;
            margin-top: 8px;
        }

        .profile-menu.show {
            display: block;
        }

        .profile-menu a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            text-decoration: none;
            color: #333;
            transition: background-color 0.3s;
        }

        .profile-menu a:hover {
            background-color: #f5f5f5;
        }

        .profile-menu a i {
            margin-right: 10px;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="logo">
            <a href="index.php">EventFlow</a>
        </div>
        <ul class="nav-links">
            <li><a href="index.php">Accueil</a></li>
            <li><a href="evenements.php">Événements</a></li>
            <li><a href="contact.php">Contact</a></li>
        </ul>
        <div class="auth-section">
            <div class="profile-icon" id="profileIcon">
                <i class="fas fa-user-circle"></i>
                <div class="profile-menu" id="profileMenu">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="profile.php"><i class="fas fa-user"></i> Mon profil</a>
                        <a href="evenements.php"><i class="fas fa-calendar"></i> Mes événements</a>
                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                            <a href="admin/dashboard.php"><i class="fas fa-cog"></i> Administration</a>
                        <?php endif; ?>
                        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
                    <?php else: ?>
                        <a href="login.php"><i class="fas fa-sign-in-alt"></i> Connexion</a>
                        <a href="signup.php"><i class="fas fa-user-plus"></i> Inscription</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const profileIcon = document.getElementById('profileIcon');
        const profileMenu = document.getElementById('profileMenu');
        
        if (profileIcon && profileMenu) {
            profileIcon.addEventListener('click', function(e) {
                e.stopPropagation();
                profileMenu.classList.toggle('show');
                console.log('Menu toggled');
            });

            document.addEventListener('click', function(e) {
                if (!profileIcon.contains(e.target)) {
                    profileMenu.classList.remove('show');
                }
            });

            profileMenu.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }
    });
    </script>
</body>
</html>