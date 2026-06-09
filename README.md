# Sistema de Currículos — PHP Puro (sem WordPress)

## Estrutura de arquivos

```
sistema-curriculos/
├── config.php      → Configurações do banco de dados
├── index.php       → Painel de gestão (uso interno)
├── cadastro.php    → Formulário público para candidatos
├── api.php         → Endpoints AJAX (salvar, editar, buscar)
└── uploads/        → Criada automaticamente ao primeiro upload
```

## Pré-requisitos

- PHP 7.4 ou superior
- MySQL 5.7 ou superior (ou MariaDB)
- Servidor web: XAMPP, WAMP, Laragon, ou servidor Linux com Apache/Nginx

## Instalação passo a passo

### 1. Criar o banco de dados

Acesse o phpMyAdmin (ou MySQL via terminal) e crie um banco:

```sql
CREATE DATABASE sistema_curriculos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 2. Configurar a conexão

Abra o arquivo `config.php` e edite:

```php
define('DB_HOST', 'localhost');   // geralmente localhost
define('DB_NAME', 'sistema_curriculos');
define('DB_USER', 'root');        // seu usuário MySQL
define('DB_PASS', '');            // sua senha MySQL
```

> A tabela `candidatos` é criada automaticamente na primeira execução.

### 3. Copiar os arquivos

Cole a pasta `sistema-curriculos/` dentro do diretório público do seu servidor:

- XAMPP: `C:/xampp/htdocs/sistema-curriculos/`
- WAMP:  `C:/wamp64/www/sistema-curriculos/`
- Linux: `/var/www/html/sistema-curriculos/`

### 4. Acessar o sistema

| Página          | URL de exemplo                                  | Quem acessa     |
|-----------------|--------------------------------------------------|-----------------|
| Painel interno  | `http://localhost/sistema-curriculos/`           | Equipe interna  |
| Cadastro público| `http://localhost/sistema-curriculos/cadastro.php` | Candidatos    |

## O que foi convertido

| WordPress                      | PHP Puro                     |
|-------------------------------|------------------------------|
| `$wpdb`                       | PDO com MySQL                |
| `wp_handle_upload()`          | `move_uploaded_file()`       |
| `admin-ajax.php`              | `api.php`                    |
| `sanitize_text_field()`       | `strip_tags()`               |
| `esc_html()` / `esc_attr()`   | `htmlspecialchars()`         |
| `add_shortcode()`             | Páginas PHP independentes    |
| `register_activation_hook()`  | `criar_tabela()` no config   |

## Permissões da pasta uploads (Linux)

```bash
chmod 755 uploads/
```
