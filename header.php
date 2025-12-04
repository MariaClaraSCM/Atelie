<?php
require "config.php";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$id_usuario    = $_SESSION['id_usuario'] ?? 0;
$logado        = $_SESSION['logado'] ?? false;
$nome_usuario  = $_SESSION['nm_usuario'] ?? '';
$tipo_usuario  = $_SESSION['tipo'] ?? '';
$foto_usuario  = $_SESSION['foto'] ?? 'default.png';

// Caminho da imagem
$foto_usuario = $_SESSION['foto'] ?? 'images/users/default.png';

$nome_primeiro = explode(" ", $nome_usuario)[0];

$query_id = $pdo->prepare("SELECT id_carrinho FROM carrinho WHERE id_usuario = ?");
$query_id->execute([$id_usuario]);
$id_carrinho = $query_id->fetchColumn();


$query = $pdo->prepare("
    SELECT 
        ic.id_item_carrinho,
        ic.quantidade,
        p.id_produto,
        p.nm_produto,
        p.preco,
        p.foto_produto
    FROM item_carrinho ic
    JOIN produto p ON p.id_produto = ic.id_produto
    WHERE ic.id_carrinho = ?
");
$query->execute([$id_carrinho]);
$itens = $query->fetchAll(PDO::FETCH_ASSOC);
?>
<header>
    <div class="ajustepicture">
        <a href="index.php">
            <img src="./assets/header/logo.svg" alt="Logo">
        </a>

        <ul>
            <li><button onclick="window.location.href='index.php'">Home</button></li>
            <li><button onclick="window.location.href='produtosPedir.php'">Produtos</button></li>
            <li><button onclick="window.location.href='index.php#porque_escolher'">Sobre</button></li>
            <li><button onclick="window.location.href='contato.php'">Contato</button></li>
        </ul>
    </div>

    <nav class="ajusteNav">

        <div class="procurar">
            <input type="search" placeholder="Pesquisar">
            <img src="./assets/header/lupa.svg" alt="Pesquisar">
        </div>

        <ul class="navUser">

            <?php if (!$logado): ?>
                <li><a href="login.php" class="auth">Login</a></li>
                <li><a href="create-user.php" class="auth">Cadastro</a></li>

            <?php else: ?>
                <li class="usuario-header">
                    <div class="userbox">
                        <?php
                        if (!empty($_SESSION['foto'])): ?>
                            <img src="<?php echo htmlspecialchars($foto_usuario); ?>"
                                alt="Foto de Usuário"
                                style="width: 35px; height: 35px; border-radius: 50%; object-fit: cover; margin-right: 10px; border: 2px solid #fff;"
                                class="shadow-sm">
                        <?php endif; ?>

                        <a href="<?= $tipo_usuario === 'admin' ? 'dashboard.php' : 'dashboard_user.php' ?>">
                            <?= htmlspecialchars($nome_primeiro) ?>
                        </a>

                        <button onclick="abrirCarrinho()">
                            <i class="fa-solid fa-cart-shopping"></i>
                        </button>
                    </div>
                </li>

                <?php if ($tipo_usuario === 'admin'): ?>
                    <li><a href="dashboard.php">Dashboard</a></li>
                <?php endif; ?>

            <?php endif; ?>

        </ul>
    </nav>
</header>

<!-- MODAL LATERAL DO CARRINHO -->
<div id="modalCarrinho" class="carrinho-modal">
    <div class="carrinho-conteudo">

        <h2>Meu carrinho</h2>

        <button class="btnFecharCarrinho" onclick="fecharCarrinho()">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <div id="listaCarrinho" class="lista-carrinho">
            <!-- Produtos serão carregados aqui via AJAX -->
            <div class="lista-itens">
                <?php
                $valorTotalPedido = 0;
                foreach ($itens as $item) {
                    $valorTotalProduto = $item['preco'] * $item['quantidade'];
                    $valorTotalPedido += $valorTotalProduto;
                    $foto = $item['foto_produto'] ?: "https://abd.org.br/wp-content/uploads/2023/09/placeholder-284.png"; // foto/sem foto
                    echo '
                        <div class="item-carrinho">
                            <div class="img-nome">
                                <img class="img-item-carrinho" src="' . $foto . '">
                                <p class="nome-produto">' . $item['nm_produto'] . '</p>
                            </div>
                            <p class="preco-total">R$ ' . number_format($valorTotalProduto, 2, ",", ".") . '</p>
                        </div>
                    ';
                }
                ?>
            </div>
            <div class="lista-itens" style="gap: 5px;">
                <?php echo 'Valor Total: R$ ' . number_format($valorTotalPedido, 2, ",", "."); ?>
                <!-- puxei a mesma classe só pra usar a estilização -->
                <button class="btnVerMais">Efetuar Pedido</button>
            </div>
            <!-- Produtos serão carregados aqui via AJAX -->
            <style>
                .lista-itens {
                    display: flex;
                    flex-direction: column;
                    justify-content: space-between;
                }

                .item-carrinho {
                    display: flex;
                    flex-direction: row;
                    justify-content: space-between;
                    align-items: center;
                }

                .img-nome {
                    display: flex;
                    flex-direction: row;
                    gap: 10px;
                    align-items: center;
                }

                .img-item-carrinho {
                    width: 32px;
                    height: 32px;
                    object-fit: cover;
                }
            </style>
        </div>
    </div>
</div>

<script>
    function abrirCarrinho() {
        document.getElementById("modalCarrinho").classList.add("ativo");
        carregarCarrinho();
    }

    function fecharCarrinho() {
        document.getElementById("modalCarrinho").classList.remove("ativo");
    }

    async function carregarCarrinho() {
        const resp = await fetch("get_carrinho.php");
        const itens = await resp.json();

        let html = "";

        if (itens.length === 0) {
            html = "<p class='vazio'>Seu carrinho está vazio.</p>";
        } else {
            itens.forEach(item => {
                html += `
                <div class="item-carrinho">
                    <input type="checkbox" class="checkCarrinho">

                    <img src="${item.foto_produto}" class="fotoItem">

                    <div class="infoItem">
                        <h4>${item.nm_produto}</h4>
                        <p>R$ ${item.preco}</p>

                        <button class="btnVerMais"
                            onclick="window.location.href='produtosPedir.php?id=${item.id_produto}'">
                            Ver mais
                        </button>
                    </div>
                </div>
            `;
            });
        }

        document.getElementById("listaCarrinho").innerHTML = html;
    }
</script>

<style>
    header {
        background: #f4d7f4;
        padding: 15px 30px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    /* ===== NAV PRINCIPAL ===== */
    nav {
        display: flex;
        align-items: center;
        gap: 40px;
    }

    .ajustepicture {
        display: flex;
        gap: 20px;
        align-items: center;
    }

    .ajustepicture a {
        display: flex;
        align-items: center;
    }

    .ajustepicture a img {
        width: 100%;
        height: 35px;
    }

    nav ul,
    .ajustepicture ul {
        display: flex;
        list-style: none;
        gap: 25px;
        align-items: center;
    }

    nav ul li button,
    .ajustepicture ul li button {
        text-decoration: none;
        color: #000;
        font-size: 15px;
        font-weight: 500;
        background-color: transparent;
        border: none;
    }

    .ajustepicture ul li button:hover {
        color: #d62882;
    }

    /* ===== AJUSTE DO BLOCO DIREITO ===== */
    .ajusteNav {
        display: flex;
        align-items: center;
        gap: 30px;
    }

    /* ===== CAMPO DE BUSCA ===== */
    .procurar {
        background: #fff;
        padding: 10px 10px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .procurar input {
        border: none;
        outline: none;
        background: none;
        font-size: 14px;
    }

    .procurar img {
        width: 16px;
        cursor: pointer;
    }

    .userbox {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .userbox a:hover,
    .userbox button:hover {
        color: #FF69B4;
    }

    .ajusteuserbox {
        display: flex;
        align-items: center;
    }

    /* ===== LINKS "ENTRAR" E "CADASTRO" ===== */
    .ajusteNav ul {
        display: flex;
        gap: 25px;
        list-style: none;
    }

    .ajusteNav ul li {
        display: flex;
    }

    .ajusteNav ul li a {
        text-decoration: none;
        color: #000;
        font-size: 15px;
        font-weight: 500;
    }

    /* Botão de cadastro com fundo escuro */
    .auth {
        background-color: #000;
        color: #fff !important;
        padding: 8px 16px;
        border-radius: 8px;
    }

    .foto-perfil {
        width: 45%;
        border-radius: 50%;
        height: 5vh;
    }

    /* Modal lateral */
    .carrinho-modal {
        position: fixed;
        top: 0;
        right: -100%;
        width: 350px;
        height: 100vh;
        background: #fff;
        box-shadow: -3px 0 10px rgba(0, 0, 0, 0.2);
        transition: 0.4s;
        z-index: 9999;
    }

    .carrinho-modal:hover {
        background: #fff;
        box-shadow: -3px 0 10px rgba(0, 0, 0, 0.2);
        color: black;
    }

    .carrinho-modal.ativo {
        right: 0;
    }

    .carrinho-conteudo {
        padding: 20px;
        height: 100%;
        overflow-y: auto;
        position: relative;
    }

    .btnFecharCarrinho {
        position: absolute;
        top: 15px;
        right: 15px;
        background: none;
        border: none;
        font-size: 22px;
        cursor: pointer;
    }

    /* Lista */
    .lista-carrinho {
        margin-top: 40px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 80%;
    }

    .item-carrinho {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 0;
        border-bottom: 1px solid #eee;
    }

    .fotoItem {
        width: 60px;
        height: 60px;
        border-radius: 6px;
        object-fit: cover;
    }

    .infoItem h4 {
        margin: 0;
        font-size: 15px;
    }

    .infoItem p {
        margin: 3px 0;
        color: #444;
    }

    .btnVerMais {
        margin-top: 5px;
        padding: 5px 10px;
        background: #d62882;
        border: none;
        color: #fff;
        border-radius: 6px;
        cursor: pointer;
        font-size: 13px;
    }

    .vazio {
        text-align: center;
        color: #777;
        margin-top: 20px;
    }
</style>