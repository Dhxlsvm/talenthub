<?php

// =============================================
// CONFIGURAÇÕES DO BANCO DE DADOS
// Edite as informações abaixo conforme seu servidor
// =============================================

define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('UPLOAD_URL', '/sistema-curriculos/uploads/');

// Conexão PDO
function db() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $pdo = new PDO('sqlite:' . __DIR__ . '/database.db');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die('Erro: ' . $e->getMessage());
        }
    }
    return $pdo;
}

// Criar tabela se não existir
function criar_tabela() {
    db()->exec("
        CREATE TABLE IF NOT EXISTS candidatos (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            nome TEXT,
            curso TEXT,
            area TEXT,
            inicio_faculdade TEXT,
            fim_faculdade TEXT,
            data_entrevista TEXT,
            interesse INTEGER DEFAULT 0,
            retorno INTEGER DEFAULT 0,
            avaliacao INTEGER DEFAULT 0,
            observacao TEXT,
            curriculo TEXT,
            data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");
}

criar_tabela();
