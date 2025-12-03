

<?php
session_start();
require 'config.php';


if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header('Location: login.php');
    exit;
}

if ($_SESSION['tipo'] !== 'admin') {
    header('Location: index.php');
    exit;
}

$id_admin = $_SESSION['id_usuario']; 

try {
   
    $stmt_admin = $pdo->prepare("SELECT nm_usuario, email, telefone, dt_nascimento, cpf, foto FROM usuario WHERE id_usuario = :id");
    $stmt_admin->execute([':id' => $id_admin]);
    $dados_admin = $stmt_admin->fetch(PDO::FETCH_ASSOC);

    if ($dados_admin) {
        // Aplicação de htmlspecialchars nas variáveis do admin
        $nome_admin = htmlspecialchars($dados_admin['nm_usuario']);
        $foto_admin = htmlspecialchars($dados_admin['foto'] ?? "images/users/default.png");
        $email_admin = htmlspecialchars($dados_admin['email']);
        $telefone_admin = htmlspecialchars($dados_admin['telefone']);
        $cpf_admin = htmlspecialchars($dados_admin['cpf']);
        $data_nascimento_admin = htmlspecialchars($dados_admin['dt_nascimento']); // Formato YYYY-MM-DD
    } else {
       
        header('Location: logout.php');
        exit;
    }
} catch (PDOException $e) {
   //tratando erro
    die("Erro ao carregar dados do administrador: " . $e->getMessage());
}






$total_avaliacoes = $pdo->query("SELECT COUNT(*) FROM avaliacao")->fetchColumn();
$total_produtos   = $pdo->query("SELECT COUNT(*) FROM produto")->fetchColumn();
$total_usuarios   = $pdo->query("SELECT COUNT(*) FROM usuario")->fetchColumn();
$total_pedidos = $pdo->query("SELECT COUNT(*) FROM pedido")->fetchColumn();

// Seção atual
$secao = htmlspecialchars($_GET['secao'] ?? 'estatisticas'); 
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <link rel="icon" type="image/svg+xml" href="./assets/imagotipo.svg" sizes="any" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="./assets/styles/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
</head>

<body>

    <div class="admin-dashboard">

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

                <li onclick="window.location='users.php'">
                    <i class="fa-solid fa-users icon"></i>
                    <span class="links">Clientes</span>
                </li>

                <li onclick="window.location='?secao=config'">
                    <i class="fa-solid fa-gear icon"></i>
                    <span class="links">Configurações</span>
                </li>

            </ul>
        </aside>


        <main class="content">

            <header class="header">
                <h3>Bem-vinda, <?= $nome_admin ?>!</h3> 

                <div style="display: flex; gap: 25px; align-items:center">  
                <button class="btn-novo" onclick="abrirCriarPedido()">
                    <i class="fa-solid fa-plus"></i>
                    Novo Pedido
                </button>
                    <a href="index.php">Home</a>
                    <a href="logout.php" class="btn-logout">Sair</a>
                </div>
            </header>

            <section class="cards-area">

                <?php if ($secao === 'estatisticas'): ?>
                    <section class="dashboard-estatisticas">

                        <div class="cards-grid">
                            
                        <div class="card-item">
                            <h3>Avaliações</h3>
                            <p class="valor"><?= $total_avaliacoes ?></p> 
                            <a href="avaliaadmin.php">Gerenciar</a>
                        </div>
                        
                        <div class="card-item">
                            <h3>Produtos</h3>
                            <p class="valor"><?= $total_produtos ?></p>
                            <a href="dashboard.php?secao=produtos">Gerenciar</a>
                        </div>

                        <div class="card-item">
                            <h3>Pedidos</h3>
                            <p class="valor"><?= $total_produtos ?></p>
                            <a href="dashboard.php?secao=produtos">Gerenciar</a>
                        </div>

                        <div class="card-item">
                            <h3>Usuários</h3>
                            <p class="valor"><?= $total_usuarios ?></p>
                            <a href="users.php">Gerenciar</a>
                        </div>

                    </div>
                        <div class="quickActions">
                            <h3>Ações Rápidas</h3>
                            <div class="actions">
                                
                                <button class="card-acao" onclick="window.location='?secao=categorias'">
                                    <i class="fa-solid fa-tag"></i>
                                    <span>Adicionar Categoria</span>
                                </button>
                                <button class="card-acao" onclick="window.location='?secao=produtos'">
                                    <i class="fa-solid fa-box"></i>
                                    <span>Adicionar Produto</span>
                                </button>
                                <button class="card-acao" onclick="window.location='?secao=pedidos'">
                                    <i class="fa-solid fa-receipt"></i>
                                    <span>Adicionar Pedido</span>
                                </button>
                            </div>
                        </div>
                        <div class="quickActions">
                            <div class="header-pedidos">
                                <h3>Pedidos Recentes</h3>
                                <a href="dashboard.php?secao=pedidos">Ver Mais</a>
                            </div>
                            <div class="lista-pedidos">
                                <div class="pedido">
                                    <div style="display: flex; gap: 10px;">
                                        <canvas id="foto-cliente"></canvas>
                                        <div>
                                            <p style="margin: 0;">Nome do produto</p>
                                            <span>Nome do cliente que pediu</span>
                                        </div>
                                    </div>
                                    <div class="status-pedido">
                                        <span>Status do pedido</span>
                                    </div>
                                </div>
                                <div class="pedido">
                                    <div style="display: flex; gap: 10px;">
                                        <canvas id="foto-cliente"></canvas>
                                        <div>
                                            <p style="margin: 0;">Nome do produto</p>
                                            <span>Nome do cliente que pediu</span>
                                        </div>
                                    </div>
                                    <div class="status-pedido">
                                        <span>Status do pedido</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                <?php endif; ?>


                <?php if ($secao === 'categorias'): ?>
                    <h2>Categorias</h2>
                    <button class="btn-novo addCategoria" onclick="window.location='categoria.php'">Adicionar Categoria</button>
                    <?php include "categoriaList.php"; ?>
                <?php endif; ?>


                <?php if ($secao === 'produtos'): ?>
                    <h2>Produtos</h2>
                    <button class="btn-novo" onclick="window.location='produto_form.php'">
                        Adicionar novo produto
                    </button>
                    <?php include "produtos.php"; ?>
                <?php endif; ?>


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
                                        <td>#<?= htmlspecialchars($p['id_pedido']) ?></td>
                                        <td><?= htmlspecialchars($p['nm_usuario']) ?></td> 
                                        <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($p['dt_pedido']))) ?></td>
                                        <td>R$ <?= htmlspecialchars(number_format($p['vl_total'], 2, ',', '.')) ?></td>
                                        <td>
                                            <select onchange="alterarStatus(<?= htmlspecialchars($p['id_pedido']) ?>, this.value)">
                                                <?php
                                                $status = ['Pendente', 'Em andamento', 'A caminho', 'Concluído', 'Entregue', 'Cancelado'];
                                                foreach ($status as $s):
                                                ?>
                                                    <option value="<?= htmlspecialchars($s) ?>" <?= $p['status_pedido'] == $s ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($s) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>

                                        <td>
                                            <button class="btnCancelar"
                                                onclick="cancelarPedidoAdmin(<?= htmlspecialchars($p['id_pedido']) ?>)">
                                                Cancelar
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>

                <?php endif; ?>


                <?php if ($secao === 'galeria'): ?>
                    <h2>Galeria</h2>
                    <p>⚠ Inserir galeria.</p>
                <?php endif; ?>


                <?php if ($secao === 'financeiro'): ?>
                    <h2>Financeiro</h2>
                    <p>⚠ Relatórios financeiros.</p>
                <?php endif; ?>


                <?php if ($secao === 'clientes'): ?>
                    <h2>Clientes</h2>
                    <a href="users.php">Gerenciar</a>
                    <p>⚠ Listagem de clientes aqui.</p>
                <?php endif; ?>


                <?php if ($secao === 'config'): ?>
                    <div class="configuracoes">
                        <div class="info-adm">
                            <div class="layout">
                                <h3>Meu perfil</h3>
                                <div class="foto-ajuste">
                                    <div class="foto_upload">
                                       <img src="<?= $foto_admin ?>" alt="Foto do Administrador" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                                    </div>
                                    <input type="file" id="inputFotoAdmin" accept="image/*" style="display: none;">

                                    <button class="editar-foto" onclick="document.getElementById('inputFotoAdmin').click();" style="border-radius: 50px; padding: 20px">
                                     <i class="fa-solid fa-camera"></i>
                                     </button>
                                </div>
                                <p><?= $nome_admin ?></p> 
                                <small>cpf <?= $cpf_admin ?></small>
                                <button>Sair</button>
                            </div>
                            <div class="layout-form">
                               <form action="#" method="post" class="form-editar" enctype="multipart/form-data">
                                    <h3>Informações</h3>
                                    <div>
                                        <label htmlFor="nome_completo">Nome completo:</label>
                                        <input type="text" name="nome_completo" id="" value="<?= $nome_admin ?>" />
                                    </div>
                                    <div style={{display: "grid", gridTemplateColumns: "4fr 1fr", gap: "10px"}}>
                                        <div>
                                            <label htmlFor="email">E-mail:</label>
                                            <input type="email" name="email" id="" value="<?= $email_admin ?>"/>
                                        </div>
                                        <div>
                                            <label htmlFor="">Data de nascimento</label>
                                            <input type="date" name="data_nascimento" id="" value="<?= $data_nascimento_admin ?>"/>
                                        </div>
                                    </div>
                                    <div>
                                        <label htmlFor="telefone">Telefone:</label>
                                        <input type="tel" name="telefone" id="" value="<?= $telefone_admin ?>"/>
                                    </div>
                                    <input type="submit" value="Editar informações" />
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

            </section>

        </main>

    </div>
    <?php include 'footer.php'; ?>

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
                    <option value="<?= htmlspecialchars($c['id_usuario']) ?>"><?= htmlspecialchars($c['nm_usuario']) ?></option>
                <?php endforeach; ?>
            </select>

            <h3>Selecione os produtos:</h3>

            <div class="lista-produtos-pedido">
                <?php
                $prods = $pdo->query("SELECT * FROM produto ORDER BY nm_produto")->fetchAll();
                foreach ($prods as $prod):
                ?>
                    <div class="prodItem">

                        <input type="checkbox" class="checkProduto" value="<?= htmlspecialchars($prod['id_produto']) ?>"
                            data-tipo="<?= htmlspecialchars($prod['tipo']) ?>">
                        <label><?= htmlspecialchars($prod['nm_produto']) ?> - R$ <?= htmlspecialchars($prod['preco']) ?></label>

                        <div class="inputs-extra" id="extra<?= htmlspecialchars($prod['id_produto']) ?>" style="display:none;">
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