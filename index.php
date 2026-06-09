<?php

require_once __DIR__ . '/config.php';

// Buscar áreas e candidatos
$areas      = db()->query("SELECT DISTINCT area FROM candidatos WHERE area != '' ORDER BY area ASC")->fetchAll(PDO::FETCH_COLUMN);
$candidatos = db()->query("SELECT * FROM candidatos ORDER BY data_cadastro DESC")->fetchAll(PDO::FETCH_OBJ);

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sistema de Currículos</title>
<style>

* { box-sizing: border-box; margin: 0; padding: 0; }

body {
    font-family: Arial, Helvetica, sans-serif;
    background: #f0f2f5;
    min-height: 100vh;
}

/* ─── TOPO ─── */
.topo {
    background: #2d8cff;
    color: white;
    padding: 16px 30px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

.topo h1 { font-size: 20px; }

.topo a {
    color: white;
    text-decoration: none;
    font-size: 13px;
    background: rgba(255,255,255,0.2);
    padding: 6px 14px;
    border-radius: 6px;
}

.topo a:hover { background: rgba(255,255,255,0.3); }

/* ─── CONTEÚDO ─── */
.conteudo {
    max-width: 1300px;
    margin: 30px auto;
    padding: 0 20px;
}

/* ─── FILTROS ─── */
.filtros {
    display: flex;
    gap: 12px;
    margin-bottom: 18px;
    flex-wrap: wrap;
    align-items: center;
}

.filtros input[type="text"],
.filtros select {
    padding: 9px 12px;
    border: 1px solid #ddd;
    border-radius: 7px;
    font-size: 14px;
    background: white;
}

.filtros input[type="text"] { width: 280px; }

/* ─── TABELA ─── */
.sc-sistema table {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
}

.sc-sistema th {
    background: #f5f6fa;
    text-align: left;
    padding: 13px 14px;
    font-size: 13px;
    color: #444;
    border-bottom: 2px solid #eee;
}

.sc-sistema td {
    padding: 12px 14px;
    border-top: 1px solid #f0f0f0;
    font-size: 14px;
    color: #333;
    vertical-align: middle;
}

.sc-sistema tr:hover td { background: #fafbff; }

/* ─── BADGES ─── */
.badge {
    display: inline-block;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.badge-verde { background: #e6f9f0; color: #2a7d4f; }
.badge-amarelo { background: #fff8e1; color: #9a6f00; }
.badge-cinza { background: #f0f0f0; color: #666; }

/* ─── BOTÕES ─── */
.btn {
    border: none;
    padding: 6px 13px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 13px;
    font-weight: 600;
    transition: opacity 0.2s;
}

.btn:hover { opacity: 0.85; }
.btn-azul  { background: #2d8cff; color: white; }
.btn-verde { background: #28a745; color: white; }
.btn-cinza { background: #e0e0e0; color: #444; }
.btn-vermelho { background: #dc3545; color: white; }

/* ─── ESTRELAS ─── */
.estrelas span {
    font-size: 22px;
    cursor: pointer;
    color: #ddd;
    margin-right: 2px;
    transition: color 0.1s;
}

.estrelas span.ativo { color: #ffc107; }

/* ─── MODAL CURRÍCULO ─── */
#modal-curriculo {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.6);
    z-index: 999;
}

#modal-curriculo > div {
    background: #fff;
    width: 92%;
    height: 88vh;
    margin: 3% auto;
    border-radius: 12px;
    display: flex;
    gap: 0;
    overflow: hidden;
    box-shadow: 0 10px 40px rgba(0,0,0,0.3);
}

.modal-cv {
    flex: 1;
    height: 100%;
}

.modal-cv iframe {
    width: 100%;
    height: 100%;
    border: none;
}

.painel-avaliacao {
    width: 360px;
    height: 100%;
    overflow-y: auto;
    padding: 24px 20px;
    border-left: 1px solid #eee;
    background: #fafafa;
}

.painel-avaliacao h2 {
    font-size: 17px;
    margin-bottom: 20px;
    color: #222;
}

.bloco-estrela { margin-bottom: 22px; }

.bloco-estrela h3 {
    font-size: 14px;
    margin-bottom: 4px;
    color: #333;
}

.bloco-estrela p {
    font-size: 12px;
    color: #888;
    margin-bottom: 6px;
}

.painel-avaliacao textarea {
    width: 100%;
    height: 110px;
    border: 1px solid #ddd;
    border-radius: 7px;
    padding: 10px;
    font-size: 13px;
    resize: vertical;
}

#btn-fechar-modal {
    position: absolute;
    right: 14px;
    top: 14px;
    background: #ff5252;
    border: none;
    color: white;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    font-size: 16px;
    cursor: pointer;
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* ─── MODAL EDITAR ─── */
#modal-editar {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.55);
    backdrop-filter: blur(4px);
    z-index: 9999;
}

.modal-editar-conteudo {
    background: #fff;
    width: 580px;
    max-width: 94%;
    margin: 6% auto;
    padding: 28px;
    border-radius: 12px;
    position: relative;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}

.modal-editar-conteudo h2 { margin-bottom: 18px; font-size: 18px; }

#fechar-editar {
    position: absolute;
    right: 14px;
    top: 14px;
    border: none;
    background: #e0e0e0;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 16px;
}

#fechar-editar:hover { background: #ccc; }

.grid-form {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
    margin-top: 10px;
}

.campo { display: flex; flex-direction: column; }

.campo label {
    font-size: 12px;
    color: #666;
    margin-bottom: 5px;
    font-weight: 600;
}

.campo input {
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 7px;
    font-size: 14px;
}

.campo input:focus { border-color: #2d8cff; outline: none; }

#salvar-edicao {
    margin-top: 20px;
    width: 100%;
    background: #2d8cff;
    border: none;
    padding: 12px;
    color: white;
    border-radius: 7px;
    font-weight: 700;
    font-size: 15px;
    cursor: pointer;
}

#salvar-edicao:hover { background: #1f6fd1; }

/* ─── TOAST ─── */
#toast {
    position: fixed;
    bottom: 24px;
    right: 24px;
    background: #323232;
    color: white;
    padding: 12px 20px;
    border-radius: 8px;
    font-size: 14px;
    opacity: 0;
    transition: opacity 0.3s;
    z-index: 99999;
}

#toast.show { opacity: 1; }

</style>
</head>
<body>

<!-- TOPO -->
<div class="topo">
    <h1>Banco de Currículos e Acompanhamento de Candidatos</h1>
    <a href="cadastro.php" target="_blank">+ Novo candidato</a>
</div>

<!-- CONTEÚDO PRINCIPAL -->
<div class="conteudo sc-sistema">

    <!-- FILTROS -->
    <div class="filtros">
        <input type="text" id="buscar-candidato" placeholder="🔍 Buscar candidato...">
        <select id="filtro-area">
            <option value="">Todas as áreas</option>
            <?php foreach ($areas as $area): ?>
            <option value="<?php echo htmlspecialchars($area); ?>">
                <?php echo htmlspecialchars($area); ?>
            </option>
            <?php endforeach; ?>
        </select>
        <span style="font-size:13px; color:#888; margin-left:auto;">
            <?php echo count($candidatos); ?> candidato(s) cadastrado(s)
        </span>
    </div>

    <!-- TABELA -->
    <table>
        <thead>
        <tr>
            <th>Nome</th>
            <th>Curso</th>
            <th>Área</th>
            <th>Tempo restante</th>
            <th>Entrevista</th>
            <th>Currículo</th>
            <th>Ações</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($candidatos as $c):

            $tempo_restante = '—';

            if ($c->fim_faculdade) {
                $dias = (strtotime($c->fim_faculdade) - time()) / 86400;
                if ($dias > 0) {
                    $anos  = floor($dias / 365);
                    $meses = floor(($dias % 365) / 30);
                    $tempo_restante = $anos > 0
                        ? $anos . ' ano(s) e ' . $meses . ' mês(es)'
                        : $meses . ' mês(es)';
                    $badge_class = $dias < 180 ? 'badge-amarelo' : 'badge-verde';
                } else {
                    $tempo_restante = 'Finalizado';
                    $badge_class = 'badge-cinza';
                }
            } else {
                $badge_class = 'badge-cinza';
            }

            $entrevista_fmt = $c->data_entrevista && $c->data_entrevista !== '0000-00-00'
                ? date('d/m/Y', strtotime($c->data_entrevista))
                : '—';
        ?>
        <tr>
            <td><strong><?php echo htmlspecialchars($c->nome); ?></strong></td>
            <td><?php echo htmlspecialchars($c->curso); ?></td>
            <td><?php echo htmlspecialchars($c->area); ?></td>
            <td><span class="badge <?php echo $badge_class; ?>"><?php echo $tempo_restante; ?></span></td>
            <td><?php echo $entrevista_fmt; ?></td>
            <td>
                <?php if ($c->curriculo): ?>
                <button class="btn btn-verde ver-curriculo"
                    data-id="<?php echo $c->id; ?>"
                    data-curriculo="<?php echo htmlspecialchars($c->curriculo); ?>">
                    📄
                </button>
                <?php else: ?>
                <span style="color:#bbb; font-size:13px;">Sem arquivo</span>
                <?php endif; ?>
            </td>
            <td>
                <button class="btn btn-cinza editar-candidato"
                    data-id="<?php echo $c->id; ?>"
                    data-nome="<?php echo htmlspecialchars($c->nome); ?>"
                    data-curso="<?php echo htmlspecialchars($c->curso); ?>"
                    data-area="<?php echo htmlspecialchars($c->area); ?>"
                    data-inicio="<?php echo $c->inicio_faculdade; ?>"
                    data-fim="<?php echo $c->fim_faculdade; ?>"
                    data-entrevista="<?php echo $c->data_entrevista; ?>">
                    ✏️
                </button>
                <button class="btn btn-vermelho excluir-candidato"
                data-id="<?php echo $c->id; ?>">
                🗑️
                </button>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

</div><!-- /conteudo -->

<!-- ─── MODAL CURRÍCULO ─── -->
<div id="modal-curriculo">
<button id="btn-fechar-modal" title="Fechar">✕</button>
<div>

    <div class="modal-cv">
        <iframe id="iframe-curriculo" src=""></iframe>
    </div>

    <div class="painel-avaliacao">
        <h2>Avaliação do candidato</h2>

        <div class="bloco-estrela">
            <h3>Interesse</h3>
            <p>Interesse pelo tipo de vaga ofertada</p>
            <div class="estrelas" data-campo="interesse">
                <span data-valor="1">★</span>
                <span data-valor="2">★</span>
                <span data-valor="3">★</span>
                <span data-valor="4">★</span>
                <span data-valor="5">★</span>
            </div>
            <input type="hidden" id="interesse" value="0">
        </div>

        <div class="bloco-estrela">
            <h3>Retorno</h3>
            <p>Retornos a vagas e entrevistas enviadas</p>
            <div class="estrelas" data-campo="retorno">
                <span data-valor="1">★</span>
                <span data-valor="2">★</span>
                <span data-valor="3">★</span>
                <span data-valor="4">★</span>
                <span data-valor="5">★</span>
            </div>
            <input type="hidden" id="retorno" value="0">
        </div>

        <div class="bloco-estrela">
            <h3>Comunicação</h3>
            <p>Facilidade em conversar e falar sobre si</p>
            <div class="estrelas" data-campo="avaliacao">
                <span data-valor="1">★</span>
                <span data-valor="2">★</span>
                <span data-valor="3">★</span>
                <span data-valor="4">★</span>
                <span data-valor="5">★</span>
            </div>
            <input type="hidden" id="avaliacao" value="0">
        </div>

        <h3 style="margin-bottom:8px;">Observações</h3>
        <textarea id="observacao" placeholder="Anote suas impressões..."></textarea>

        <br><br>
        <button class="btn btn-azul" id="salvar-avaliacao" style="width:100%; padding:12px;">
            💾 Salvar avaliação
        </button>
    </div>

</div>
</div>

<!-- ─── MODAL EDITAR ─── -->
<div id="modal-editar">
<div class="modal-editar-conteudo">

    <button id="fechar-editar">✕</button>
    <h2>Editar candidato</h2>
    <input type="hidden" id="edit-id">

    <div class="grid-form">
        <div class="campo">
            <label>Nome</label>
            <input type="text" id="edit-nome">
        </div>
        <div class="campo">
            <label>Curso</label>
            <input type="text" id="edit-curso">
        </div>
        <div class="campo">
            <label>Área</label>
            <input type="text" id="edit-area">
        </div>
        <div class="campo">
            <label>Início da faculdade</label>
            <input type="date" id="edit-inicio">
        </div>
        <div class="campo">
            <label>Fim da faculdade</label>
            <input type="date" id="edit-fim">
        </div>
        <div class="campo">
            <label>Data da entrevista</label>
            <input type="date" id="edit-entrevista">
        </div>
    </div>

    <button id="salvar-edicao">Salvar alterações</button>

</div>
</div>

<!-- ─── TOAST ─── -->
<div id="toast"></div>

<script>

const API = 'api.php';

// ─── TOAST ───
function mostrarToast(msg) {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 2800);
}

// ─── PINTAR ESTRELAS ───
function pintarEstrelas(container, valor) {
    container.querySelectorAll('span').forEach(function(e) {
        e.classList.toggle('ativo', parseInt(e.dataset.valor) <= parseInt(valor));
    });
}

// ─── CLIQUE NAS ESTRELAS ───
document.querySelectorAll('.estrelas span').forEach(function(estrela) {
    estrela.addEventListener('click', function() {
        const valor     = this.dataset.valor;
        const container = this.parentElement;
        const campo     = container.dataset.campo;
        document.getElementById(campo).value = valor;
        pintarEstrelas(container, valor);
    });
});

// ─── FILTRO BUSCA ───
document.getElementById('buscar-candidato').addEventListener('keyup', function() {
    const filtro = this.value.toLowerCase();
    document.querySelectorAll('table tbody tr').forEach(function(linha) {
        linha.style.display = linha.innerText.toLowerCase().includes(filtro) ? '' : 'none';
    });
});

// ─── FILTRO ÁREA ───
document.getElementById('filtro-area').addEventListener('change', function() {
    const area = this.value.toLowerCase();
    document.querySelectorAll('table tbody tr').forEach(function(linha) {
        const celula = linha.children[2].innerText.toLowerCase();
        linha.style.display = (!area || celula.includes(area)) ? '' : 'none';
    });
});

// ─── ABRIR MODAL CURRÍCULO ───
let candidatoAtual = 0;

document.querySelectorAll('.ver-curriculo').forEach(function(botao) {
    botao.addEventListener('click', function() {
        const link = this.dataset.curriculo;
        candidatoAtual = this.dataset.id;

        document.getElementById('iframe-curriculo').src = link;
        document.getElementById('modal-curriculo').style.display = 'block';

        // Buscar avaliação existente
        fetch(API, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'action=sc_buscar_candidato&id=' + candidatoAtual
        })
        .then(r => r.json())
        .then(function(c) {
            const interesse = c.interesse || 0;
            const retorno   = c.retorno   || 0;
            const avaliacao = c.avaliacao || 0;

            document.getElementById('interesse').value = interesse;
            document.getElementById('retorno').value   = retorno;
            document.getElementById('avaliacao').value = avaliacao;
            document.getElementById('observacao').value = c.observacao || '';

            pintarEstrelas(document.querySelector('[data-campo="interesse"]'), interesse);
            pintarEstrelas(document.querySelector('[data-campo="retorno"]'),   retorno);
            pintarEstrelas(document.querySelector('[data-campo="avaliacao"]'), avaliacao);
        });
    });
});

// ─── FECHAR MODAL CURRÍCULO ───
document.getElementById('btn-fechar-modal').addEventListener('click', function() {
    document.getElementById('modal-curriculo').style.display = 'none';
    document.getElementById('iframe-curriculo').src = '';
});

// ─── SALVAR AVALIAÇÃO ───
document.getElementById('salvar-avaliacao').addEventListener('click', function() {
    const interesse = document.getElementById('interesse').value;
    const retorno   = document.getElementById('retorno').value;
    const avaliacao = document.getElementById('avaliacao').value;
    const observacao = document.getElementById('observacao').value;

    fetch(API, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=sc_salvar_avaliacao&id=' + candidatoAtual +
              '&interesse=' + interesse +
              '&retorno='   + retorno +
              '&avaliacao=' + avaliacao +
              '&observacao=' + encodeURIComponent(observacao)
    })
    .then(r => r.json())
    .then(function() {
        mostrarToast('✅ Avaliação salva com sucesso!');
    });
});

// ─── ABRIR MODAL EDITAR ───
document.querySelectorAll('.editar-candidato').forEach(function(btn) {
    btn.addEventListener('click', function() {
        document.getElementById('edit-id').value         = this.dataset.id;
        document.getElementById('edit-nome').value       = this.dataset.nome;
        document.getElementById('edit-curso').value      = this.dataset.curso;
        document.getElementById('edit-area').value       = this.dataset.area;
        document.getElementById('edit-inicio').value     = this.dataset.inicio;
        document.getElementById('edit-fim').value        = this.dataset.fim;
        document.getElementById('edit-entrevista').value = this.dataset.entrevista;
        document.getElementById('modal-editar').style.display = 'block';
    });
});

// ─── FECHAR MODAL EDITAR ───
document.getElementById('fechar-editar').addEventListener('click', function() {
    document.getElementById('modal-editar').style.display = 'none';
});

// ─── SALVAR EDIÇÃO ───
document.getElementById('salvar-edicao').addEventListener('click', function() {
    const id         = document.getElementById('edit-id').value;
    const nome       = document.getElementById('edit-nome').value;
    const curso      = document.getElementById('edit-curso').value;
    const area       = document.getElementById('edit-area').value;
    const inicio     = document.getElementById('edit-inicio').value;
    const fim        = document.getElementById('edit-fim').value;
    const entrevista = document.getElementById('edit-entrevista').value;

    fetch(API, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=sc_editar_candidato&id=' + id +
              '&nome='       + encodeURIComponent(nome) +
              '&curso='      + encodeURIComponent(curso) +
              '&area='       + encodeURIComponent(area) +
              '&inicio='     + inicio +
              '&fim='        + fim +
              '&entrevista=' + entrevista
    })
    .then(r => r.json())
    .then(function() {
        document.getElementById('modal-editar').style.display = 'none';
        mostrarToast('✅ Candidato atualizado!');
        setTimeout(() => location.reload(), 1500);
    });
});

// Fechar modais clicando fora
document.getElementById('modal-curriculo').addEventListener('click', function(e) {
    if (e.target === this) {
        this.style.display = 'none';
        document.getElementById('iframe-curriculo').src = '';
    }
});

document.getElementById('modal-editar').addEventListener('click', function(e) {
    if (e.target === this) this.style.display = 'none';
});

document.querySelectorAll('.excluir-candidato').forEach(function(btn) {
    btn.addEventListener('click', function() {

        const id = this.dataset.id;

        if (!confirm('Tem certeza que deseja excluir este candidato?')) {
            return;
        }

        fetch(API, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'action=sc_excluir_candidato&id=' + id
        })
        .then(r => r.json())
        .then(function() {
            mostrarToast('🗑️ Candidato excluído!');
            setTimeout(() => location.reload(), 1000);
        });

    });
});
</script>
</body>
</html>
