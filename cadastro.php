<?php

require_once __DIR__ . '/config.php';

$mensagem = '';
$erro = '';

// =============================================
// PROCESSAR ENVIO DO FORMULÁRIO
// =============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['salvar_candidato'])) {

    $nome           = strip_tags($_POST['nome'] ?? '');
    $curso          = strip_tags($_POST['curso'] ?? '');
    $area           = strip_tags($_POST['area'] ?? '');
    $inicio         = $_POST['inicio_faculdade'] ?? null;
    $fim            = $_POST['fim_faculdade'] ?? null;
    $data_entrevista = $_POST['data_entrevista'] ?? null;

    $curriculo_path = '';

    // UPLOAD DO CURRÍCULO
    if (!empty($_FILES['curriculo']['name'])) {

        if (!is_dir(UPLOAD_DIR)) {
            mkdir(UPLOAD_DIR, 0755, true);
        }

        $ext = strtolower(pathinfo($_FILES['curriculo']['name'], PATHINFO_EXTENSION));

        if ($ext !== 'pdf') {
            $erro = 'O currículo deve ser um arquivo PDF.';
        } elseif ($_FILES['curriculo']['size'] > 5 * 1024 * 1024) {
            $erro = 'O arquivo é muito grande. Máximo 5MB.';
        } else {
            $nome_arquivo = uniqid('cv_') . '.pdf';
            move_uploaded_file($_FILES['curriculo']['tmp_name'], UPLOAD_DIR . $nome_arquivo);
            $curriculo_path = UPLOAD_URL . $nome_arquivo;
        }

    }

    if (!$erro) {

        if (empty($nome) || empty($curso)) {
            $erro = 'Nome e curso são obrigatórios.';
        } else {
            $stmt = db()->prepare("INSERT INTO candidatos (nome, curso, area, inicio_faculdade, fim_faculdade, data_entrevista, curriculo) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nome, $curso, $area, $inicio ?: null, $fim ?: null, $data_entrevista ?: null, $curriculo_path]);
            $mensagem = 'Currículo cadastrado com sucesso!';
        }

    }

}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Envio de Currículo</title>
<style>

* { box-sizing: border-box; margin: 0; padding: 0; }

body {
    font-family: Arial, Helvetica, sans-serif;
    background: #f0f2f5;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.sc-formulario {
    max-width: 500px;
    width: 100%;
    margin: auto;
    padding: 30px;
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.10);
}

.sc-formulario h2 {
    margin-bottom: 24px;
    font-size: 22px;
    color: #222;
}

.sc-formulario label {
    display: block;
    margin-top: 14px;
    font-size: 13px;
    color: #555;
    font-weight: 600;
    margin-bottom: 4px;
}

.sc-formulario input[type="text"],
.sc-formulario input[type="date"],
.sc-formulario input[type="file"] {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #ddd;
    border-radius: 7px;
    font-size: 14px;
    transition: border-color 0.2s;
}

.sc-formulario input:focus {
    border-color: #2d8cff;
    outline: none;
}

.sc-formulario button {
    margin-top: 22px;
    width: 100%;
    background: #2d8cff;
    border: none;
    padding: 13px;
    color: white;
    font-size: 15px;
    font-weight: 600;
    border-radius: 7px;
    cursor: pointer;
    transition: background 0.2s;
}

.sc-formulario button:hover {
    background: #1f6fd1;
}

.alerta-sucesso {
    background: #e6f9f0;
    border: 1px solid #5cb85c;
    color: #2d6a2d;
    padding: 12px 16px;
    border-radius: 7px;
    margin-bottom: 18px;
    font-size: 14px;
}

.alerta-erro {
    background: #fff0f0;
    border: 1px solid #e57373;
    color: #8b0000;
    padding: 12px 16px;
    border-radius: 7px;
    margin-bottom: 18px;
    font-size: 14px;
}

</style>
</head>
<body>

<div class="sc-formulario">

    <h2>📄 Envie seu Currículo</h2>

    <?php if ($mensagem): ?>
    <div class="alerta-sucesso"><?php echo htmlspecialchars($mensagem); ?></div>
    <?php endif; ?>

    <?php if ($erro): ?>
    <div class="alerta-erro"><?php echo htmlspecialchars($erro); ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">

        <label>Nome completo *</label>
        <input type="text" name="nome" placeholder="Seu nome" required>

        <label>Curso *</label>
        <input type="text" name="curso" placeholder="Ex: Administração, Engenharia..." required>

        <label>Área de interesse</label>
        <input type="text" name="area" placeholder="Ex: Marketing, TI, RH...">

        <label>Início da faculdade</label>
        <input type="date" name="inicio_faculdade">

        <label>Previsão de conclusão</label>
        <input type="date" name="fim_faculdade">

        <label>Data da entrevista</label>
        <input type="date" name="data_entrevista">

        <label>Currículo (PDF, máx. 5MB)</label>
        <input type="file" name="curriculo" accept="application/pdf">

        <button type="submit" name="salvar_candidato">Cadastrar</button>

    </form>

</div>

</body>
</html>
