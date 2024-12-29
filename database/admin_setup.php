<?php
require_once __DIR__ . '/../config/database.php';

try {
    // Créer la table des administrateurs
    $sql = "CREATE TABLE IF NOT EXISTS admins (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $pdo->exec($sql);

    // Vérifier si l'admin par défaut existe déjà
    $stmt = $pdo->prepare("SELECT id FROM admins WHERE username = ?");
    $stmt->execute(['admin']);
    
    if (!$stmt->fetch()) {
        // Créer l'admin par défaut (admin/admin)
        $hashedPassword = password_hash('admin', PASSWORD_DEFAULT);
        $sql = "INSERT INTO admins (username, password) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['admin', $hashedPassword]);
        echo "Admin par défaut créé avec succès.\n";
    } else {
        echo "L'admin par défaut existe déjà.\n";
    }

    echo "Configuration de l'administration terminée avec succès.\n";
} catch(PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
