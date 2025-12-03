<?php
require "config.php";

$mensagem = null;
$erro     = null;

// -------------------- PUT: ATUALIZAR CATEGORIA (AJAX) --------------------
if ($_SERVER["REQUEST_METHOD"] === "PUT") {
    header("Content-Type: application/json; charset=utf-8");

    $dataRaw = file_get_contents("php://input");
    $data    = json_decode($dataRaw, true);

    if (!$data) {
        echo json_encode(["erro" => "JSON inválido"]);
        exit;
    }

    $id   = $data["id_categoria"] ?? null;
    $nome = trim($data["nm_categoria"] ?? "");

    if (!$id || $nome === "") {
        echo json_encode(["erro" => "Dados inválidos"]);
        exit;
    }

    try {
        $upd = $pdo->prepare("UPDATE categoria SET nm_categoria = ? WHERE id_categoria = ?");
        $upd->execute([$nome, $id]);

        echo json_encode(["mensagem" => "Categoria atualizada com sucesso"]);
    } catch (Exception $e) {
        echo json_encode(["erro" => "Erro ao atualizar categoria"]);
    }
    exit; // <<< MUITO IMPORTANTE
}

// -------------------- DELETE: EXCLUIR CATEGORIA (AJAX) --------------------
if ($_SERVER["REQUEST_METHOD"] === "DELETE") {
    header("Content-Type: application/json; charset=utf-8");

    // Pega ?id=... da query string
    parse_str($_SERVER["QUERY_STRING"] ?? "", $query);

    $id = $query["id"] ?? null;

    if (!$id) {
        echo json_encode(["erro" => "ID não informado"]);
        exit;
    }

    try {
        $del = $pdo->prepare("DELETE FROM categoria WHERE id_categoria = ?");
        $del->execute([$id]);

        echo json_encode(["mensagem" => "Categoria excluída com sucesso"]);
    } catch (Exception $e) {
        echo json_encode(["erro" => "Erro ao excluir categoria"]);
    }
    exit; // <<< MUITO IMPORTANTE
}


// -------------------- GET NORMAL: CARREGA LISTA E MONTA TELA --------------
$stmt = $pdo->query("SELECT id_categoria, nm_categoria FROM categoria ORDER BY nm_categoria ASC");
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Categorias</title>

    <!-- AJUSTE O CAMINHO DO CSS CONFORME ONDE ELE ESTIVER -->
    <link rel="stylesheet" href="assets/adm/dashboard.css">

    <style>
        /* Só pra garantir visual básico se o CSS não carregar */
        .h3ListCategoria { margin-bottom: 16px; }
        .tabela { width: 100%; border-collapse: collapse; }
        .tabela th, .tabela td { border: 1px solid #ddd; padding: 8px; }
        .ttlth { background: #f3f3f3; text-align: left; }
        .blocoCategoria:nth-child(even) { background: #fafafa; }
        .acoes { display: flex; gap: 8px; }
        .editar, .excluir {
            padding: 6px 10px;
            border: none;
            cursor: pointer;
            border-radius: 4px;
        }
        .editar { background: #4caf50; color: #fff; }
        .excluir { background: #e53935; color: #fff; }
    </style>
</head>

<body>

    <h3 class="h3ListCategoria">Lista de Categorias</h3>

    <?php if ($mensagem): ?>
        <div style="color: green; margin-bottom: 10px;">
            <?= htmlspecialchars($mensagem) ?>
        </div>
    <?php endif; ?>

    <?php if ($erro): ?>
        <div style="color: red; margin-bottom: 10px;">
            <?= htmlspecialchars($erro) ?>
        </div>
    <?php endif; ?>

    <table class="tabela">
        <thead>
            <tr>
                <th class="ttlth">Categoria</th>
                <th class="ttlth">Ações</th>
            </tr>
        </thead>

        <tbody>
            <?php if (empty($categorias)): ?>
                <tr>
                    <td colspan="2">Nenhuma categoria cadastrada.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($categorias as $cat): ?>
                    <tr class="blocoCategoria" id="cat-<?= $cat['id_categoria'] ?>">
                        <td class="tuplaDoMeio"><?= htmlspecialchars($cat['nm_categoria']) ?></td>
                        <td class="acoes">

                            <button class="editar"
                                    onclick="editarCategoria(<?= $cat['id_categoria'] ?>, '<?= htmlspecialchars($cat['nm_categoria'], ENT_QUOTES) ?>')">
                                Editar
                            </button>

                            <button class="excluir"
                                    onclick="excluirCategoria(<?= $cat['id_categoria'] ?>)">
                                Excluir
                            </button>

                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

<script>
// ======= EDITAR COM prompt() + FETCH PUT =======
function editarCategoria(id, nomeAtual){
    const novo = prompt("Novo nome da categoria:", nomeAtual);

    if (!novo || novo.trim() === "") return;

    fetch("categoria.php", {
        method: "PUT",
        headers: {"Content-Type": "application/json"},
        body: JSON.stringify({
            id_categoria: id,
            nm_categoria: novo.trim()
        })
    })
    .then(async (r) => {
        const texto = await r.text();
        console.log("RESPOSTA PUT BRUTA:", texto);

        let resp;
        try {
            resp = JSON.parse(texto);
        } catch (e) {
            console.error("Erro ao parsear JSON do PUT:", e);
            alert("Erro ao atualizar categoria (resposta não é JSON). Veja o console.");
            return;
        }

        if (resp.erro) {
            alert(resp.erro);
            return;
        }

        alert(resp.mensagem || "Categoria atualizada.");

        const td = document.querySelector(`#cat-${id} td.tuplaDoMeio`);
        if (td) td.innerText = novo.trim();
    })
    .catch(err => {
        console.error("Erro no PUT:", err);
        alert("Erro ao atualizar categoria (veja o console).");
    });
}

// ======= EXCLUIR COM confirm() + FETCH DELETE =======
function excluirCategoria(id){
    if (!confirm("Deseja realmente excluir esta categoria?")) return;

    fetch("categoria.php?id=" + id, {
        method: "DELETE"
    })
    .then(async (r) => {
        const texto = await r.text();
        console.log("RESPOSTA DELETE BRUTA:", texto);

        let resp;
        try {
            resp = JSON.parse(texto);
        } catch (e) {
            console.error("Erro ao parsear JSON do DELETE:", e);
            alert("Erro ao excluir categoria (resposta não é JSON). Veja o console.");
            return;
        }

        if (resp.erro) {
            alert(resp.erro);
            return;
        }

        alert(resp.mensagem || "Categoria excluída.");

        const tr = document.querySelector(`#cat-${id}`);
        if (tr) tr.remove();
    })
    .catch(err => {
        console.error("Erro no DELETE:", err);
        alert("Erro ao excluir categoria (veja o console).");
    });
}
</script>

</body>
</html>
