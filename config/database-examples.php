<?php
/**
 * Exemples de configuration base de données selon l'hébergeur
 * Copiez la configuration appropriée dans database.php
 */

// ====================
// HÉBERGEMENT cPanel
// ====================
/*
$host = 'localhost';
$port = '3306';
$dbname = 'cpanel_user_intrasphere';  // Format: utilisateur_nombase
$username = 'cpanel_user_intrasphere';
$password = 'mot_de_passe_genere_cpanel';
$driver = 'mysql';
*/

// ====================
// HÉBERGEMENT OVH
// ====================
/*
$host = 'mysql-intrasphere.hosting.ovh.net';  // Hostname fourni par OVH
$port = '3306';
$dbname = 'intrasphere';
$username = 'intrasphere';
$password = 'cle_fournie_par_ovh';
$driver = 'mysql';
*/

// ====================
// HÉBERGEMENT 1&1 / IONOS
// ====================
/*
$host = 'db12345.hosting.1and1.com';  // Serveur fourni
$port = '3306';
$dbname = 'db12345678';  // ID base fourni
$username = 'dbo12345678';
$password = 'mot_de_passe_1and1';
$driver = 'mysql';
*/

// ====================
// VPS / SERVEUR DÉDIÉ
// ====================
/*
$host = '127.0.0.1';  // ou IP du serveur
$port = '3306';
$dbname = 'intrasphere';
$username = 'intrasphere_user';
$password = 'mot_de_passe_securise';
$driver = 'mysql';
*/

// ====================
// HÉBERGEMENT PostgreSQL
// ====================
/*
$host = 'postgres.hebergeur.com';
$port = '5432';
$dbname = 'intrasphere';
$username = 'postgres_user';
$password = 'postgres_password';
$driver = 'pgsql';
*/

// ====================
// CONFIGURATION LOCALE (XAMPP/WAMP)
// ====================
/*
$host = 'localhost';
$port = '3306';
$dbname = 'intrasphere';
$username = 'root';
$password = '';  // Vide pour XAMPP par défaut
$driver = 'mysql';
*/

// ====================
// HÉBERGEMENT GRATUIT (000webhost, etc.)
// ====================
/*
$host = 'mysql.hosting.gratuit.com';
$port = '3306';
$dbname = 'id12345_intrasphere';  // Format avec préfixe ID
$username = 'id12345_user';
$password = 'password_fourni';
$driver = 'mysql';
*/
?>