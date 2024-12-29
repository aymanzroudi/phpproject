<?php
require_once '../config/database.php';

try {
    // Ajouter les colonnes reset_token et reset_token_expiry
    $sql = "ALTER TABLE users 
            ADD COLUMN IF NOT EXISTS reset_token VARCHAR(64) DEFAULT NULL,
            ADD COLUMN IF NOT EXISTS reset_token_expiry DATETIME DEFAULT NULL";
    
    $pdo->exec($sql);
    echo "La table users a été mise à jour avec succès!";
} catch(PDOException $e) {
    echo "Erreur lors de la mise à jour de la table: " . $e->getMessage();
}
?>
