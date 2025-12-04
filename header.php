<?php
require "config.php";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$logado        = $_SESSION['logado'] ?? false;
$nome_usuario  = $_SESSION['nm_usuario'] ?? '';
$tipo_usuario  = $_SESSION['tipo'] ?? '';
$foto_usuario  = $_SESSION['foto'] ?? 'default.png';

if (!isset($_SESSION['id_usuario'])) {
    $_SESSION['id_usuario'] = null;

    // echo '
    // <div class="aviso-sem-conta">
    //     Você não está cadastrado.
    //     <a href="login.php">Faça login</a>
    // </div>

    // <style>
    //     .aviso-sem-conta {
    //         width: 100%;
    //         background: #ffe5ea;
    //         color: #b40039;
    //         padding: 10px 15px;
    //         border-radius: 6px;
    //         font-size: 14px;
    //         // border-left: 4px solid #b40039;
    //         font-weight: 500;
    //         z-index: 999999;
    //     }
    // </style>
    // ';
    // return;
}

$id_usuario = $logado ? ($_SESSION['id_usuario'] ?? 0) : 0;

// Caminho da imagem
$caminho_foto = "images/users/" . $foto_usuario;
if (!file_exists($caminho_foto)) {
    $caminho_foto = "images/users/default.png";
}

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
                            <img src="<?php echo htmlspecialchars($_SESSION['foto']); ?>"
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
                    <?php echo 'Valor Total: R$ ' . number_format($valorTotalPedido, 2, ",", ".");?>
                    <!-- puxei a mesma classe só pra usar a estilização -->
                    <button class="btnVerMais" onclick="abrirModal('modalNotaFiscal')"">Efetuar Pedido</button>
                </div>
            <!-- Produtos serão carregados aqui via AJAX -->
        </div>
    </div>
</div>
<!-- MODAL 1: Nota Fiscal / Resumo do Carrinho -->
<div id="modalNotaFiscal" class="modal" style="display:none;">
    <div class="modal-conteudo">
        <h2>Resumo do Carrinho</h2>
        <div id="itensNotaFiscal">
            <!-- Aqui serão listados os produtos do carrinho -->
        </div>
        <p><b>Valor total:</b> <span id="valorTotalNotaFiscal">R$ 0,00</span></p>
        <div class="acoesModal">
            <button id="btnConfirmarNota" class="btnComprar">Confirmar</button>
        </div>
        <button class="btnFechar" onclick="fecharModal('modalNotaFiscal')">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
</div>
<!-- MODAL 2: Endereço -->
<div id="modalEndereco" class="modal" style="display:none;">
    <div class="modal-conteudo">
        <h2>Informe o Endereço</h2>
        <label>Rua:</label>
        <input type="text" id="ruaEndereco" placeholder="Rua">
        <label>Número:</label>
        <input type="text" id="numeroEndereco" placeholder="Número">
        <label>Cidade:</label>
        <input type="text" id="cidadeEndereco" placeholder="Cidade">
        <label>CEP:</label>
        <input type="text" id="cepEndereco" placeholder="CEP">
        <div class="acoesModal">
            <button id="btnConfirmarEndereco" class="btnComprar">Confirmar</button>
        </div>
        <button class="btnFechar" onclick="fecharModal('modalEndereco')">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
</div>

<!-- MODAL 3: Confirmação Final -->
<div id="modalConfirmacao" class="modal" style="display:none;">
    <div class="modal-conteudo">
        <h2>Confirmação do Pedido</h2>
        <p><b>Quantidade:</b> <span id="quantidadeFinal"></span></p>
        <p><b>Endereço:</b> <span id="enderecoFinal"></span></p>
        <p><b>Valor total:</b> <span id="valorFinal"></span></p>
        <p><b>Método de pagamento:</b></p>
        <div class="metodosPagamento">
            <button class="btnPagamento" data-pag="Cartão">Cartão</button>
            <button class="btnPagamento" data-pag="Pix">Pix</button>
            <button class="btnPagamento" data-pag="Dinheiro">Dinheiro</button>
        </div>
        <div class="acoesModal">
            <button id="btnFinalizarPedido" class="btnComprar" onclick="abrirNotaFiscal()">Finalizar Pedido</button>
        </div>
        <button class="btnFechar" onclick="fecharModal('modalConfirmacao')">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
</div>

<script>
    function formatarReal(valor) {
        return valor.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    }

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
    let carrinho = <?php echo json_encode($itens); ?>;

    function abrirModal(id) { document.getElementById(id).style.display = 'flex'; 
        fecharCarrinho();
    }
    function fecharModal(id) { document.getElementById(id).style.display = 'none'; }

    document.getElementById('btnConfirmarNota').onclick = () => {
        fecharModal('modalNotaFiscal');
        abrirModal('modalEndereco');
    };


    function preencherNotaFiscal() {
        const container = document.getElementById('itensNotaFiscal');
        container.innerHTML = '';
        let total = 0;
        carrinho.forEach(item => {
            total += item.quantidade * item.preco;
            const div = document.createElement('div');
            div.textContent = `${item.nome} - ${item.quantidade} x ${formatarReal(item.preco)} = ${formatarReal(item.quantidade * item.preco)}`;
            container.appendChild(div);
        });
        document.getElementById('valorTotalNotaFiscal').textContent = formatarReal(total);
    }


    document.getElementById('btnConfirmarEndereco').onclick = () => {
        fecharModal('modalEndereco');
        const rua = document.getElementById('ruaEndereco').value;
        const numero = document.getElementById('numeroEndereco').value;
        const cidade = document.getElementById('cidadeEndereco').value;
        const cep = document.getElementById('cepEndereco').value;
        document.getElementById('enderecoFinal').textContent = `${rua}, ${numero}, ${cidade}, CEP: ${cep}`;
        let total = carrinho.reduce((acc, i) => acc + i.quantidade * i.preco, 0);
        document.getElementById('valorFinal').textContent = formatarReal(total);
        document.getElementById('quantidadeFinal').textContent = carrinho.reduce((acc,i)=>acc+i.quantidade,0);
        abrirModal('modalConfirmacao');
    };

    document.querySelectorAll('.btnPagamento').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.btnPagamento').forEach(b=>b.style.background='#d62882');
            btn.style.background = '#28a745';
        });
    });

    document.getElementById('btnFinalizarPedido').onclick = () => {
        alert('Pedido finalizado com sucesso!');
        fecharModal('modalConfirmacao');
    };
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

    .userbox a:hover, .userbox button:hover {
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
        display:flex;
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
    .modal {
        position: fixed;
        top:0; left:0; right:0; bottom:0;
        background: rgba(0,0,0,0.6);
        display:flex;
        justify-content:center;
        align-items:center;
        z-index:1000;
    }
    .modal-conteudo {
        background:#fff;
        padding:20px;
        border-radius:8px;
        width:90%;
        max-width:500px;
        position:relative;
        display:flex;
        flex-direction:column;
        gap:12px;
    }
    .btnFechar {
        position:absolute;
        top:10px;
        right:10px;
        background:none;
        border:none;
        font-size:20px;
        cursor:pointer;
    }
    .acoesModal {
        margin-top:10px;
        display:flex;
        justify-content:flex-end;
        gap:8px;
    }
    .btnComprar, .btnPagamento {
        padding:8px 16px;
        border:none;
        background:#d62882;
        color:#fff;
        border-radius:4px;
        cursor:pointer;
    }
    .metodosPagamento {
        display:flex;
        gap:8px;
    }

    input {
        padding: 5px 10px;
        border-radius: 6px;
        border: 1px solid #ccc;
    }
</style>