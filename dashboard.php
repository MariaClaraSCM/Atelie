<?php
session_start();
require 'config.php';

// Verificar login
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header('Location: login.php');
    exit;
}

// Verificar permissão de admin
if ($_SESSION['tipo'] !== 'admin') {
    header('Location: index.php');
    exit;
}

// Dados do admin
$nome_admin = $_SESSION['nm_usuario'];
$foto_admin = $_SESSION['foto'] ?? "images/users/default.png";

// Contagens
$total_avaliacoes = $pdo->query("SELECT COUNT(*) FROM avaliacao")->fetchColumn();
$total_produtos   = $pdo->query("SELECT COUNT(*) FROM produto")->fetchColumn();
$total_usuarios   = $pdo->query("SELECT COUNT(*) FROM usuario")->fetchColumn();

// Seção atual
$secao = $_GET['secao'] ?? 'estatisticas';
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>

    <link rel="stylesheet" href="./assets/styles/dashboard.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
</head>

<body>

    <div class="admin-dashboard">

        <!-- ========== SIDEBAR ============= -->
        <aside class="sidebar">
            <ul class="sidebar-menu">

                <h3 class="sidebar-title">Atalhos</h3>

                <li onclick="window.location='?secao=estatisticas'">
                    <i class="fa-solid fa-chart-pie icon"></i>
                    <span class="links">Estatísticas</span>
                </li>

                <li onclick="window.location='?secao=categorias'">
                    <i class="fa-solid fa-layer-group icon"></i>
                    <span class="links">Categorias</span>
                </li>

                <li onclick="window.location='?secao=produtos'">
                    <i class="fa-solid fa-box icon"></i>
                    <span class="links">Produtos</span>
                </li>

                <li onclick="window.location='?secao=pedidos'">
                    <i class="fa-solid fa-shopping-bag icon"></i>
                    <span class="links">Pedidos</span>
                </li>

                <li onclick="window.location='?secao=galeria'">
                    <i class="fa-solid fa-image icon"></i>
                    <span class="links">Galeria</span>
                </li>

                <li onclick="window.location='?secao=financeiro'">
                    <i class="fa-solid fa-coins icon"></i>
                    <span class="links">Financeiro</span>
                </li>

                <li onclick="window.location='?secao=clientes'">
                    <i class="fa-solid fa-users icon"></i>
                    <span class="links">Clientes</span>
                </li>

                <li onclick="window.location='?secao=config'">
                    <i class="fa-solid fa-gear icon"></i>
                    <span class="links">Configurações</span>
                </li>

            </ul>
        </aside>


        <!-- ========== CONTEÚDO PRINCIPAL ============= -->
        <main class="content">

            <!-- HEADER -->
            <header class="header">
                <h3>Bem-vinda, <?= htmlspecialchars($nome_admin) ?>!</h3>

                <a href="index.php">Home</a>

                <button class="btn-novo" onclick="abrirCriarPedido()">
                    <i class="fa-solid fa-plus"></i>
                    Novo Pedido
                </button>

                <a href="logout.php" class="btn-logout">Sair</a>
            </header>

            <!-- ÁREA DOS CARDS / COMPONENTES -->
            <section class="cards-area">

                <!-- ESTATÍSTICAS -->
                <?php if ($secao === 'estatisticas'): ?>
                    <div class="cards-grid">

                        <div class="card-item">
                            <h2>Avaliações</h2>
                            <p class="valor"><?= $total_avaliacoes ?></p>
                            <a href="avaliaadmin.php">Gerenciar</a>
                        </div>

                        <div class="card-item">
                            <h2>Produtos</h2>
                            <p class="valor"><?= $total_produtos ?></p>
                            <a href="servicos.php">Gerenciar</a>
                        </div>

                        <div class="card-item">
                            <h2>Usuários</h2>
                            <p class="valor"><?= $total_usuarios ?></p>
                            <a href="users.php">Gerenciar</a>
                        </div>

                    </div>
                <?php endif; ?>


                <!-- CATEGORIAS -->
                <?php if ($secao === 'categorias'): ?>
                    <h2>Categorias</h2>
                    <button class="btn-novo addCategoria" onclick="window.location='categoria.php'">Adicionar Categoria</button>
                    <?php include "categoriaList.php"; ?>
                <?php endif; ?>


                <!-- PRODUTOS -->
                <?php if ($secao === 'produtos'): ?>
                    <h2>Produtos</h2>
                    <button class="btn-novo" onclick="window.location='produto_form.php'">
                        Adicionar novo produto
                    </button>
                    <?php include "produtos.php"; ?>
                <?php endif; ?>


                <!-- PEDIDOS -->
                <?php if ($secao === 'pedidos'): ?>
                    <h2>Pedidos</h2>
                    <hr>
                    <?php
                    $ped = $pdo->query("
    SELECT p.*, u.nm_usuario 
    FROM pedido p
    JOIN usuario u ON u.id_usuario = p.id_usuario
    ORDER BY p.dt_pedido DESC
")->fetchAll(PDO::FETCH_ASSOC);

                    if (!$ped): ?>
                        <p>Nenhum pedido encontrado.</p>

                    <?php else: ?>
                        <table class="tabelaPedidos">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Cliente</th>
                                    <th>Data</th>
                                    <th>Valor</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php foreach ($ped as $p): ?>
                                    <tr>
                                        <td>#<?= $p['id_pedido'] ?></td>
                                        <td><?= htmlspecialchars($p['nm_usuario']) ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($p['dt_pedido'])) ?></td>
                                        <td>R$ <?= number_format($p['vl_total'], 2, ',', '.') ?></td>
                                        <td>
                                            <select onchange="alterarStatus(<?= $p['id_pedido'] ?>, this.value)">
                                                <?php
                                                $status = ['Pendente', 'Em andamento', 'A caminho', 'Concluído', 'Entregue', 'Cancelado'];
                                                foreach ($status as $s):
                                                ?>
                                                    <option value="<?= $s ?>" <?= $p['status_pedido'] == $s ? 'selected' : '' ?>>
                                                        <?= $s ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>

                                        <td>
                                            <button class="btnCancelar"
                                                onclick="cancelarPedidoAdmin(<?= $p['id_pedido'] ?>)">
                                                Cancelar
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>

                <?php endif; ?>


                <!-- GALERIA -->
                <?php if ($secao === 'galeria'): ?>
                    <h2>Galeria</h2>
                    <p>⚠ Inserir galeria.</p>
                <?php endif; ?>


                <!-- FINANCEIRO -->
                <?php if ($secao === 'financeiro'): ?>
                    <h2>Financeiro</h2>
                    <p>⚠ Relatórios financeiros.</p>
                <?php endif; ?>


                <!-- CLIENTES -->
                <?php if ($secao === 'clientes'): ?>
                    <h2>Clientes</h2>
                    <p>⚠ Listagem de clientes aqui.</p>
                <?php endif; ?>


                <!-- CONFIGURAÇÕES -->
                <?php if ($secao === 'config'): ?>
                    <h2>Configurações</h2>
                    <p>⚠ Área de Configurações.</p>
                <?php endif; ?>

            </section>

        </main>

    </div>
    <?php include 'footer.php'; ?>

    <!-- MODAL CRIAR PEDIDO -->
    <div id="modalCriarPedido" class="modalPedido">
        <div class="modalConteudo">

            <h2>Criar novo pedido</h2>

            <label>Selecionar cliente:</label>
            <select id="clienteSelect">
                <option value="">Selecione...</option>

                <?php
                $clientes = $pdo->query("SELECT id_usuario, nm_usuario FROM usuario ORDER BY nm_usuario")->fetchAll();
                foreach ($clientes as $c):
                ?>
                    <option value="<?= $c['id_usuario'] ?>"><?= $c['nm_usuario'] ?></option>
                <?php endforeach; ?>
            </select>

            <h3>Selecione os produtos:</h3>

            <div class="lista-produtos-pedido">
                <?php
                $prods = $pdo->query("SELECT * FROM produto ORDER BY nm_produto")->fetchAll();
                foreach ($prods as $prod):
                ?>
                    <div class="prodItem">

                        <input type="checkbox" class="checkProduto" value="<?= $prod['id_produto'] ?>"
                            data-tipo="<?= $prod['tipo'] ?>">
                        <label><?= $prod['nm_produto'] ?> - R$ <?= $prod['preco'] ?></label>

                        <div class="inputs-extra" id="extra<?= $prod['id_produto'] ?>" style="display:none;">
                            <input type="text" placeholder="Cor (opcional)" class="corPed">
                            <input type="text" placeholder="Personagem (opcional)" class="personPed">
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="ajustebotoes">
                <button class="btnSalvar" onclick="salvarPedido()">Salvar Pedido</button>
                <button class="btnFechar" onclick="fecharCriarPedido()">Fechar</button>
            </div>

        </div>
    </div>

    <style>
        .modalPedido {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #00000070;
            justify-content: center;
            align-items: center;
            z-index: 10;
        }

        .modalPedido .modalConteudo {
            background: #fff;
            padding: 30px;
            width: 500px;
            border-radius: 10px;
            max-height: 90vh;
            overflow: auto;
        }

        .lista-produtos-pedido {
            max-height: 300px;
            overflow: auto;
            padding: 10px;
        }

        .prodItem {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        .inputs-extra input {
            margin-top: 5px;
            width: 95%;
            padding: 6px;
        }
    </style>

    <script>
        function abrirCriarPedido() {
            document.getElementById('modalCriarPedido').style.display = "flex";
        }

        function fecharCriarPedido() {
            document.getElementById('modalCriarPedido').style.display = "none";
        }

        // Mostrar campos extras se produto for encomenda
        document.querySelectorAll(".checkProduto").forEach(chk => {
            chk.addEventListener("change", () => {
                let id = chk.value;
                let tipo = chk.dataset.tipo;

                let box = document.getElementById("extra" + id);

                if (chk.checked && tipo === "Encomenda") box.style.display = "block";
                else box.style.display = "none";
            });
        });

        // SALVAR NOVO PEDIDO
        async function salvarPedido() {

            const cliente = document.getElementById("clienteSelect").value;

            if (!cliente) {
                alert("Selecione um cliente!");
                return;
            }

            const selecionados = document.querySelectorAll(".checkProduto:checked");

            if (selecionados.length === 0) {
                alert("Selecione ao menos um produto!");
                return;
            }

            const itens = [];

            selecionados.forEach(chk => {

                const id = chk.value;
                const tipo = chk.dataset.tipo;

                // Inputs extras dentro do mesmo bloco
                const box = chk.closest(".prodItem").querySelector(".inputs-extra");

                const cor = (tipo === "Encomenda") ?
                    box.querySelector(".corPed").value.trim() || null :
                    null;

                const personagem = (tipo === "Encomenda") ?
                    box.querySelector(".personPed").value.trim() || null :
                    null;

                itens.push({
                    id: id,
                    cor: cor,
                    personagem: personagem
                });
            });

            console.log("ENVIANDO:", itens);

            const resp = await fetch("admin_pedidos.php", {
                method: "POST",
                body: new URLSearchParams({
                    action: "criar_pedido",
                    cliente: cliente,
                    produtos: JSON.stringify(itens)
                })
            });

            const data = await resp.json();

            if (data.ok) {
                alert("Pedido criado com sucesso!");
                location.reload();
            } else {
                alert("Erro ao salvar: " + data.msg);
            }
        }




        // ALTERAR STATUS
        async function alterarStatus(id, novoStatus) {
            const resp = await fetch("admin_pedidos.php", {
                method: "POST",
                body: new URLSearchParams({
                    action: "alterar_status",
                    id_pedido: id,
                    status: novoStatus
                })
            });

            const data = await resp.json();
            if (data.ok) {
                alert("Status atualizado!");
            } else {
                alert("Erro: " + data.msg);
            }
        }


        // CANCELAR PEDIDO
        async function cancelarPedidoAdmin(id) {

            if (!confirm("Cancelar pedido?")) return;

            const resp = await fetch("admin_pedidos.php", {
                method: "POST",
                body: new URLSearchParams({
                    action: "cancelar_pedido",
                    id_pedido: id
                })
            });

            const data = await resp.json();

            if (data.ok) {
                alert("Pedido cancelado!");
                location.reload();
            } else {
                alert("Erro: " + data.msg);
            }
        }
    </script>


</body>

</html>