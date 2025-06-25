<?php
function get_db_connection() {
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=health_project;charset=utf8", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        return $pdo;
    } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}

function execute_query($query, $params = []) {
    $pdo = get_db_connection();
    $stmt = $pdo->prepare($query);
    return $stmt->execute($params);
}

function get_row($query, $params = []) {
    $pdo = get_db_connection();
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function get_rows($query, $params = []) {
    $pdo = get_db_connection();
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
