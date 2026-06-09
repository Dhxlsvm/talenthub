<?php

require_once __DIR__ . '/config.php';

header('Content-Type: application/json; charset=utf-8');

$action = $_POST['action'] ?? '';

// =============================================
// BUSCAR CANDIDATO
// =============================================
if ($action === 'sc_buscar_candidato') {

    $id = intval($_POST['id']);
    $stmt = db()->prepare("SELECT * FROM candidatos WHERE id = ?");
    $stmt->execute([$id]);
    $candidato = $stmt->fetch(PDO::FETCH_OBJ);
    echo json_encode($candidato);
    exit;

}

// =============================================
// SALVAR AVALIAÇÃO
// =============================================
if ($action === 'sc_salvar_avaliacao') {

    $id        = intval($_POST['id']);
    $interesse = intval($_POST['interesse']);
    $retorno   = intval($_POST['retorno']);
    $avaliacao = intval($_POST['avaliacao']);
    $observacao = strip_tags($_POST['observacao'] ?? '');

    $stmt = db()->prepare("UPDATE candidatos SET interesse=?, retorno=?, avaliacao=?, observacao=? WHERE id=?");
    $stmt->execute([$interesse, $retorno, $avaliacao, $observacao, $id]);
    echo json_encode(['status' => 'ok']);
    exit;

}

// =============================================
// EDITAR CANDIDATO
// =============================================
if ($action === 'sc_editar_candidato') {

    $id    = intval($_POST['id']);
    $nome  = strip_tags($_POST['nome'] ?? '');
    $curso = strip_tags($_POST['curso'] ?? '');
    $area  = strip_tags($_POST['area'] ?? '');
    $inicio     = $_POST['inicio'] ?? null;
    $fim        = $_POST['fim'] ?? null;
    $entrevista = $_POST['entrevista'] ?? null;

    $stmt = db()->prepare("UPDATE candidatos SET nome=?, curso=?, area=?, inicio_faculdade=?, fim_faculdade=?, data_entrevista=? WHERE id=?");
    $stmt->execute([$nome, $curso, $area, $inicio ?: null, $fim ?: null, $entrevista ?: null, $id]);
    echo json_encode(['status' => 'ok']);
    exit;

}

// =============================================
// EXCLUIR CANDIDATO
// =============================================
if ($action === 'sc_excluir_candidato') {

    $id = intval($_POST['id']);

    // Buscar currículo pra excluir arquivo também
    $stmt = db()->prepare("SELECT curriculo FROM candidatos WHERE id = ?");
    $stmt->execute([$id]);
    $c = $stmt->fetch(PDO::FETCH_OBJ);

    if ($c && $c->curriculo) {
        $caminho = __DIR__ . str_replace('/sistema-curriculos', '', $c->curriculo);
        if (file_exists($caminho)) {
            unlink($caminho);
        }
    }

    // Deletar do banco
    $stmt = db()->prepare("DELETE FROM candidatos WHERE id = ?");
    $stmt->execute([$id]);

    echo json_encode(['status' => 'ok']);
    exit;
}
echo json_encode(['status' => 'acao_invalida']);
