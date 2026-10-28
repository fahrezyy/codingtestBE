<?php
$dbFile = __DIR__ . '/database.sqlite';

try {
    $pdo = new PDO("sqlite:$dbFile");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Buat tabel products
    $pdo->exec("CREATE TABLE IF NOT EXISTS products (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        description TEXT NOT NULL,
        price REAL NOT NULL,
        category TEXT NOT NULL,
        status TEXT NOT NULL
    )");

    // 2. Buat tabel users (untuk menyimpan akun login)
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        email TEXT UNIQUE NOT NULL,
        password TEXT NOT NULL,
        role TEXT NOT NULL
    )");

    // Cek apakah data products kosong, isi data dummy
    $stmt = $pdo->query("SELECT COUNT(*) FROM products");
    if ($stmt->fetchColumn() == 0) {
        $pdo->exec("INSERT INTO products (name, description, price, category, status) VALUES 
            ('Laptop ASUS ROG', 'Laptop gaming performa tinggi dengan kartu grafis RTX', 15000000, 'Elektronik', 'tersedia'),
            ('Mouse Wireless Logitech', 'Mouse ergonomis dengan konektivitas bluetooth', 350000, 'Elektronik', 'tersedia'),
            ('Kemeja Flanel Pria', 'Kemeja katun lembut nyaman dipakai sehari-hari', 175000, 'Pakaian', 'habis')
        ");
    }

    // Cek apakah data users kosong, masukkan akun admin default
    $userStmt = $pdo->query("SELECT COUNT(*) FROM users");
    if ($userStmt->fetchColumn() == 0) {
        // Menggunakan password_hash untuk keamanan standar industri (best practice portofolio)
        $defaultPassword = password_hash('password123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, ?)");
        $stmt->execute(['admin@test.com', $defaultPassword, 'Administrator']);
    }

    return $pdo;
} catch (PDOException $e) {
    echo json_encode(["status" => 500, "message" => "Database Connection Error: " . $e->getMessage()]);
    exit;
}
?>