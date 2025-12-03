<?php
session_start();
require 'config.php';

// ======================= AUTENTICAÇÃO =======================
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header('Location: login.php');
    exit;
}

$id_usuario    = $_SESSION['id_usuario'];
$nome_usuario  = $_SESSION['nm_usuario'];
$foto_usuario  = $_SESSION['foto'] ?? 'default.png';

// Primeiro nome
$nome_primeiro = explode(" ", $nome_usuario)[0];

// ======================= DADOS DO USUÁRIO ============================
$sql_user = $pdo->prepare("
    SELECT nm_usuario, email, telefone, foto
    FROM usuario
    WHERE id_usuario = ?
");
$sql_user->execute([$id_usuario]);
$dados_usuario = $sql_user->fetch(PDO::FETCH_ASSOC);

// Se existir, sobrescreve
if ($dados_usuario) {
    $nome_usuario = $dados_usuario['nm_usuario'];
    $email        = $dados_usuario['email'];
    $telefone     = $dados_usuario['telefone'];
    $foto_usuario = $dados_usuario['foto'] ?? 'default.png';
}

// ======================= ENDEREÇO DO USUÁRIO ============================
$sql_endereco = $pdo->prepare("
    SELECT *
    FROM endereco
    WHERE id_usuario = ?
    LIMIT 1
");
$sql_endereco->execute([$id_usuario]);
$endereco = $sql_endereco->fetch(PDO::FETCH_ASSOC) ?: [];


// ======================= FAVORITOS ==========================
$sql_favoritos = "
    SELECT 
        p.id_produto, p.nm_produto, p.preco, p.foto_produto 
    FROM 
        favoritos f 
    JOIN 
        produto p ON f.id_produto = p.id_produto
    WHERE 
        f.id_usuario = ?
    ORDER BY 
        p.nm_produto ASC
";

$query_fav = $pdo->prepare($sql_favoritos);
$query_fav->execute([$id_usuario]);
$favoritos = $query_fav->fetchAll(PDO::FETCH_ASSOC);


// ======================= PEDIDOS ============================
$sql_pedidos = "
    SELECT id_pedido, dt_pedido, status_pedido, vl_total
    FROM pedido
    WHERE id_usuario = ?
    ORDER BY dt_pedido DESC
";

$query_ped = $pdo->prepare($sql_pedidos);
$query_ped->execute([$id_usuario]);
$pedidos = $query_ped->fetchAll(PDO::FETCH_ASSOC);


// ======================= FILTROS DE STATUS ===================

// Status ATIVOS no seu banco:
$STATUS_ATIVOS = ['Pendente', 'Em andamento', 'A caminho'];

// Status HISTÓRICO no seu banco:
$STATUS_HISTORICO = ['Concluído', 'Entregue', 'Cancelado'];

$pedidos_ativos = array_filter($pedidos, function ($p) use ($STATUS_ATIVOS) {
    return in_array($p['status_pedido'], $STATUS_ATIVOS);
});

$historico = array_filter($pedidos, function ($p) use ($STATUS_HISTORICO) {
    return in_array($p['status_pedido'], $STATUS_HISTORICO);
});

$total_favoritos = count($favoritos);
$total_pedidos   = count($pedidos);

// Seção selecionada no dashboard
$selectedSection = $_GET['section'] ?? 'conta';
?>


<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Minha Conta</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="./assets/styles/perfilUser.css">
    <link rel="icon" type="image/svg+xml" href="./assets/imagotipo.svg" sizes="any" />

</head>

<body>
    <?php include 'header.php'; ?>

    <div class="userpage-container">
        <div class="ajustepage">

            <!-- MENU LATERAL -->
            <aside class="asideUserpage">

                <div class="juntos">
                    <picture class="fotoPerfilP">
                        <?php
                        if (!empty($_SESSION['foto'])): ?>
                            <img src="<?php echo htmlspecialchars($_SESSION['foto']); ?>"
                                alt="Foto de Usuário"
                                style="width: 35px; height: 35px; border-radius: 50%; object-fit: cover; margin-right: 10px; border: 2px solid #fff;"
                                class="shadow-sm">
                        <?php endif; ?>
                        <?= htmlspecialchars($nome_primeiro) ?>
                    </picture>

                    <button class="Infos <?= $selectedSection == 'conta' ? 'active' : '' ?>"
                        onclick="window.location.href='?section=conta'">
                        Minha Conta
                    </button>

                    <button class="Infos <?= $selectedSection == 'compras' ? 'active' : '' ?>"
                        onclick="window.location.href='?section=compras'">
                        Meus Pedidos
                    </button>

                    <button class="Infos <?= $selectedSection == 'historico' ? 'active' : '' ?>"
                        onclick="window.location.href='?section=historico'">
                        Histórico
                    </button>

                    <button class="Infos <?= $selectedSection == 'favoritos' ? 'active' : '' ?>"
                        onclick="window.location.href='?section=favoritos'">
                        Meus Favoritos
                    </button>
                </div>

                <button onclick="window.location.href='logout.php'"
                    class="btnLogout"
                    style="padding: 7px 10px; background-color: #d62882; border: 0; border-radius: 6px; color: white;"
                    >
                    Sair
                </button>

            </aside>

            <hr>

            <!-- ÁREA PRINCIPAL -->
            <main class="mainUserpage">

                <!-- SEÇÃO MINHA CONTA -->
                <?php if ($selectedSection == 'conta'): ?>
                    <div class="Forms">
                        <h2>Minhas Informações</h2>
                        <hr>
                        <div class="FormsAtt">

                            <!-- FORMULÁRIO INFORMAÇÕES DO USUÁRIO -->
                            <form action="update_usuario.php" method="POST" enctype="multipart/form-data" class="form-bloco">

                                <h3>Informações Pessoais</h3>

                                <label>Nome Completo:</label>
                                <input type="text" name="nm_usuario" value="<?= htmlspecialchars($nome_usuario) ?>">

                                <label>Email:</label>
                                <input type="email" name="email" value="<?= htmlspecialchars($email) ?>">

                                <label>Telefone:</label>
                                <input type="text" name="telefone" value="<?= htmlspecialchars($telefone) ?>">

                                <label>Nova Senha (opcional):</label>
                                <input type="password" name="senha">

                                <label>Foto de Perfil:</label>
                                <input type="file" name="foto">

                                <button type="submit" class="btnSalvar">Salvar alterações</button>

                            </form>


                            <!-- FORMULÁRIO ENDEREÇO -->
                            <form action="update_endereco.php" method="POST" class="form-bloco">

                                <h3>Endereço</h3>

                                <label>CEP:</label>
                                <input type="text" name="cep" value="<?= htmlspecialchars($endereco['cep'] ?? '') ?>">

                                <label>Rua:</label>
                                <input type="text" name="rua" value="<?= htmlspecialchars($endereco['rua'] ?? '') ?>">

                                <label>Número:</label>
                                <input type="text" name="numero" value="<?= htmlspecialchars($endereco['numero'] ?? '') ?>">

                                <label>Bairro:</label>
                                <input type="text" name="bairro" value="<?= htmlspecialchars($endereco['bairro'] ?? '') ?>">

                                <label>Cidade:</label>
                                <input type="text" name="cidade" value="<?= htmlspecialchars($endereco['cidade'] ?? '') ?>">

                                <label>Estado:</label>
                                <input type="text" name="estado" value="<?= htmlspecialchars($endereco['estado'] ?? '') ?>">

                                <label>Complemento:</label>
                                <input type="text" name="complemento" value="<?= htmlspecialchars($endereco['complemento'] ?? '') ?>">

                                <button type="submit" class="btnSalvar">Salvar Endereço</button>

                            </form>

                        </div>

                    </div>
                <?php endif; ?>



                <!-- SEÇÃO FAVORITOS -->
                <?php if ($selectedSection == 'favoritos'): ?>
                    <div class="favoritos">
                        <h2>Meus Favoritos ❤️</h2>
                        <hr>

                        <?php if (!$favoritos): ?>
                            <p>Você não favoritou nenhum produto ainda.</p>
                        <?php else: ?>
                            <div class="cards-favoritos">
                                <?php foreach ($favoritos as $fav): ?>
                                    <div class="card-fav">
                                        <img src="<?= $fav['foto_produto'] ?>" alt="">
                                        <h4><?= htmlspecialchars($fav['nm_produto']) ?></h4>
                                        <p>R$ <?= number_format($fav['preco'], 2, ',', '.') ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>



                <!-- SEÇÃO COMPRAS -->
                <?php if ($selectedSection == 'compras'): ?>
                    <div class="compras">
                        <h2>Pedidos Ativos</h2>
                        <hr>

                        <?php if (empty($pedidos_ativos)): ?>
                            <p>Nenhum pedido ativo no momento.</p>

                        <?php else: ?>
                            <?php foreach ($pedidos_ativos as $p): ?>
                                <div class="pedido-item">
                                    <div class="ajusteFlex">
                                        <h4>Pedido #<?= $p['id_pedido'] ?></h4>

                                        <p>Data: <?= date('d/m/Y H:i', strtotime($p['dt_pedido'])) ?></p>

                                        <p>Valor:
                                            <b>R$ <?= number_format($p['vl_total'], 2, ',', '.') ?></b>
                                        </p>

                                        <p>Status:
                                            <b><?= htmlspecialchars($p['status_pedido']) ?></b>
                                        </p>
                                    </div>
                                    <button class="cancelarPedido" onclick="cancelarPedido(<?= $p['id_pedido'] ?>)">
                                        Cancelar pedido
                                    </button>
                                </div>


                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>




                <!-- SEÇÃO HISTÓRICO -->
                <?php if ($selectedSection == 'historico'): ?>
                    <div class="historico">
                        <h2>Histórico</h2>
                        <hr>

                        <?php if (!$historico): ?>
                            <p>Nenhum pedido encontrado.</p>
                        <?php else: ?>
                            <?php foreach ($historico as $h): ?>
                                <div class="pedido-item">
                                    <h4>Pedido #<?= $h['id_pedido'] ?></h4>
                                    <p>Data: <?= date('d/m/Y', strtotime($h['dt_pedido'])) ?></p>
                                    <p>Status: <?= htmlspecialchars($h['status_pedido']) ?></p>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

            </main>

        </div>
    </div>

    <footer>
        <p>© 2025 Ateliê Vó Egina. Todos os direitos reservados.</p>
    </footer>

    <script>
        async function cancelarPedido(idPedido) {
            if (!confirm("Tem certeza que deseja cancelar este pedido?")) {
                return;
            }

            const resp = await fetch("cancelar_pedido.php", {
                method: "POST",
                body: new URLSearchParams({
                    id_pedido: idPedido
                })
            });

            const data = await resp.json();

            if (data.ok) {
                alert("Pedido cancelado com sucesso!");
                location.reload();
            } else {
                alert("Erro ao cancelar: " + data.msg);
            }
        }
    </script>

    <?php include 'footer.php'; ?>
</body>

</html>