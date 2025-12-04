<?php
require 'config.php';
session_start(); //para pegar o id do usuario 
$query1 = $pdo->prepare('select id_carrinho from carrinho where id_usuario = ?');
$query1->execute([$_SESSION['id_usuario']]);
$resultado = $query1->fetch(PDO::FETCH_ASSOC);
$id_carrinho = $resultado ? $resultado['id_carrinho'] : 'null';

// Buscar produtos
$sql = "SELECT p.*, c.nm_categoria 
        FROM produto p 
        LEFT JOIN categoria c ON c.id_categoria = p.id_categoria";

$stmt = $pdo->prepare($sql);
$stmt->execute();

$produtos = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

    if (!empty($row["foto_produto"])) {
        $row["foto"] = $row["foto_produto"];
    } else {
        $row["foto"] = "https://abd.org.br/wp-content/uploads/2023/09/placeholder-284.png";
    }

    $produtos[] = $row;
}

// Paginação
$produtosPorPagina = 9;
$totalProdutos = count($produtos);
$totalPaginas = ceil($totalProdutos / $produtosPorPagina);

$currentPage = isset($_GET["page"]) ? intval($_GET["page"]) : 1;
$currentPage = max(1, min($currentPage, $totalPaginas));

$inicio = ($currentPage - 1) * $produtosPorPagina;
$produtosAtuais = array_slice($produtos, $inicio, $produtosPorPagina);

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="./assets/styles/pedirProduto.css">
    <link rel="icon" type="image/svg+xml" href="./assets/imagotipo.svg" sizes="any" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <title>Produtos</title>
</head>

<body>

    <?php include 'header.php'; ?>

    <!-- FILTRO -->
    <div class="filtro">

        <div class="btncategorias">
            <button class="btn-cat ativo" data-cat="Todos">Todos</button>

            <?php
            $cat = $pdo->query("SELECT * FROM categoria ORDER BY nm_categoria");
            while ($c = $cat->fetch(PDO::FETCH_ASSOC)):
            ?>
                <button class="btn-cat" data-cat="<?= $c['nm_categoria'] ?>">
                    <?= $c['nm_categoria'] ?>
                </button>
            <?php endwhile; ?>
        </div>

        <div class="categorias">
            <div class="escolhas">
                <select id="ordenacao">
                    <option value="Recentes">Recentes</option>
                    <option value="Antigos">Antigos</option>
                    <option value="Mais baratos">Mais baratos</option>
                    <option value="Mais caros">Mais caros</option>
                </select>

                <div class="toggle">
                    <button id="btnGrid" class="ativo" data-view="grid">
                        <img src="./assets/produtos/grid.svg">
                    </button>

                    <button id="btnList" data-view="list">
                        <img src="./assets/produtos/list.svg">
                    </button>
                </div>
            </div>
        </div>
    </div>


    <main class="galeriaProdutos">

        <section class="gridProdutos">

            <?php foreach ($produtosAtuais as $p): ?>
                <div class="produtoAdm"
                    data-cat="<?= $p["nm_categoria"] ?>"
                    data-preco="<?= $p["preco"] ?>"
                    data-id="<?= $p["id_produto"] ?>">

                    <img
                        src="<?= $p['foto'] ?>"
                        class="foto-produto"
                        alt="<?= $p['nm_produto'] ?>">

                    <div class="info">
                        <h2><?= $p["nm_produto"] ?></h2>
                        <p><?= $p["descricao"] ?></p>

                        <div class="ajusteCardProduto">
                            <p><b>Categoria:</b> <?= $p["nm_categoria"] ?></p>
                            <p><b>R$ <?= $p["preco"] ?></b></p>
                        </div>
                    </div>

                    <div class="acoesAdm">
                        <button class="btnFav" data-id="<?= $p['id_produto'] ?>">
                            <i class="fa-regular fa-heart"></i>
                        </button>
                        <button class="btnVerProduto"
                            onclick='abrirModalProduto(<?= json_encode($p) ?>)'>
                            Ver produto
                        </button>
                    </div>

                </div>
            <?php endforeach; ?>
        </section>

        <!-- MODAL PRINCIPAL -->
        <div id="modalProduto" class="modalProduto" style="display:none;">
            <div class="modal-conteudo">

                <div class="modal-esquerda">
                    <img id="modalFoto" src="" alt="Foto do produto">
                    <!-- <canvas id="modalFoto" style="background: #0f0f0f"></canvas> pra testar o tamanho da imagem -->
                </div>

                <div class="modal-direita">
                    <div>
                        <br>
                        <h2 id="modalNome"></h2>
                        <p id="modalDescricao"></p>
                    </div>

                    <!-- <input type="number" id="modalQuantidade" min="1" value="1"> -->
                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        <label>Quantidade:</label>
                        <div class="number-wrapper">
                            <button class="btn-number" data-type="minus"><i class="fa-solid fa-minus"></i></button>
                            <input type="number" id="modalQuantidade" min="1" value="1">
                            <button class="btn-number" data-type="plus"><i class="fa-solid fa-plus"></i></button>
                        </div>
                    </div>
                    <div>
                        <p>Tamanho: <span id="produtoTamanho"></span></p>
                    </div>
                    <div>
                        <p>Produto: <span id="produtoTipo"></span></p>
                    </div>
                    <p>Valor unitário: <span id="modalPreco"></span></p>
                    <p>Valor total: <span id="modalPrecoTotal"></span></p>
                    <div class="acoesModal">
                        <button id="btnPedirAgora" class="btnComprar">Pedir agora</button>
                        <!-- PARA ENVIAR AS INFORMAÇÕES PRO POST DE ADD NO CARRINHO!!! -->

                        <input type="hidden" id="id_carrinho" name="id_carrinho">
                        <input type="hidden" id="id_produto" name="id_produto">
                        <input type="hidden" id="quantidade" name="quantidade">
                        <input type="hidden" id="cor_item" name="cor_item">
                        <input type="hidden" id="nm_personagem" name="nm_personagem">

                        <button id="btnCarrinho" class="btnCarrinho">Adicionar ao carrinho</button>
                    </div>
                </div>

                <button class="btnFechar" onclick="fecharModalProduto()">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        </div>

        <!-- MODAL ENCOMENDA -->
        <div id="modalEncomenda" class="modalProduto" style="display:none;">
            <div class="modal-conteudo">

                <h2>Informações da Encomenda</h2>

                <label>Cor da bolsa:</label>
                <input type="text" id="corBolsa" placeholder="Ex: rosa, preto...">

                <label>Nome do personagem:</label>
                <input type="text" id="nomePersonagem" placeholder="Ex: Barbie...">

                <div class="acoesModal">
                    <button class="btnComprar" onclick="confirmarEncomenda()">Confirmar</button>
                </div>

                <button class="btnFechar" onclick="fecharModalEncomenda()">
                    <i class="fa-solid fa-xmark"></i>
                </button>

            </div>
        </div>

        <!-- PAGINAÇÃO -->
        <div class="paginacao">
            <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                <a href="?page=<?= $i ?>">
                    <button class="<?= $i == $currentPage ? 'ativo' : '' ?>">
                        <?= $i ?>
                    </button>
                </a>
            <?php endfor; ?>
        </div>
        <?php include "footer.php"?>
    </main>

    <script>
        let iscarrinho = false;
        let produtoAtual = null;

        function formatarReal(valor) {
            return new Intl.NumberFormat("pt-BR", {
                style: "currency",
                currency: "BRL"
            }).format(valor);
        }
        // ABRIR MODAL PRODUTO
        function abrirModalProduto(p) {
            p.preco = Number(p.preco);
            produtoAtual = p;

            document.getElementById("modalFoto").src = p.foto;
            document.getElementById("modalNome").textContent = p.nm_produto;
            document.getElementById("modalPreco").textContent = formatarReal(p.preco);
            document.getElementById("modalDescricao").textContent = p.descricao;
            document.getElementById("produtoTamanho").textContent = p.qt_tamanho;
            document.getElementById("produtoTipo").textContent = p.tipo;
            document.getElementById("id_produto").value = p.id_produto; //hiddem p pegar no form do post do item_carrinho
            document.getElementById("id_carrinho").value = <?php echo $id_carrinho ?? 0 ?>; //hidden tmb
            //isso daqui n pega fio

            const qtd = document.getElementById("modalQuantidade");
            qtd.value = 1;

            document.getElementById("modalPrecoTotal").textContent = formatarReal(p.preco * Number(qtd.value));

            qtd.disabled = (p.tipo === "Pronta entrega");
            document.getElementById("modalProduto").style.display = "flex";
        }

        document.querySelectorAll(".btn-number").forEach(btn => {
            btn.addEventListener("click", () => {
                const input = document.getElementById("modalQuantidade");
                let current = parseInt(input.value) || 1;

                if (btn.dataset.type === "plus") {
                    current = current + 1;
                } else {
                    if (current > parseInt(input.min)) {
                        current = current - 1;
                    }
                }

                input.value = current;
                const total = Number(produtoAtual.preco) * current;
                document.getElementById("modalPrecoTotal").textContent = formatarReal(total);
                document.getElementById("quantidade").value = current;
            });
        });

        function fecharModalProduto() {
            document.getElementById("modalProduto").style.display = "none";
        }

        // BOTÃO PEDIR AGORA
        document.getElementById("btnPedirAgora").onclick = () => {
            if (produtoAtual.tipo === "Encomenda") {
                fecharModalProduto();
                document.getElementById("modalEncomenda").style.display = "flex";
            } else {
                criarPedido("Pronta entrega");
            }
        };

        // AQUI O FAVORITAR QUE N TÁ PEGANDO
        
        document.querySelectorAll(".btnFav").forEach(btn => {
            btn.addEventListener("click", async () => {
                const id_produto = btn.dataset.id;

                // Envia requisição para favoritar.php
                try {
                    const resp = await fetch("favoritar.php", {
                        method: "POST",
                        body: new URLSearchParams({ id_produto })
                    });

                    const data = await resp.json();

                    if (!data.ok) {
                        alert(data.message || "Você precisa estar logado para favoritar!");
                        return;
                    }

                    // Atualiza o ícone do coração
                    const icon = btn.querySelector("i");
                    if (data.favorito) {
                        icon.classList.remove("fa-regular");
                        icon.classList.add("fa-solid");
                    } else {
                        icon.classList.remove("fa-solid");
                        icon.classList.add("fa-regular");
                    }

                } catch (err) {
                    alert("Erro ao conectar com o servidor");
                    console.error(err);
                }
            });
        });

        //BOTAO PRA ADD CARRINHOWWWWWWWW  ENCOMENDAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA
        document.getElementById("btnCarrinho").onclick = () => {
            if (produtoAtual.tipo === "Encomenda") {
                fecharModalProduto();
                document.getElementById("modalEncomenda").style.display = "flex";
                iscarrinho = true;
            } else {
                // PRONTA ENTREGA: Fetch direto (sem recarregar)
                console.log("Aqui: " + document.getElementById("quantidade").value);
                const dados = {
                    id_carrinho: document.getElementById("id_carrinho").value,
                    id_produto: document.getElementById("id_produto").value,
                    quantidade: document.getElementById("quantidade").value,
                    cor_item: null,
                    nm_personagem: null
                };
                fetch('adicionarCarrinho.php', {
                        method: 'POST',
                        body: new URLSearchParams(dados)
                    })
                    .then(resposta => resposta.json()) // ← Converte resposta do PHP
                    .then(resultado => {
                        if (resultado.success) { // VERIFICA se PHP disse que deu certo via chave success!!!!!
                            alert('✅ Adicionado ao carrinho!');
                            fecharModalProduto();
                        } else {
                            alert('❌ Erro: ' + resultado.message);
                        }
                    })
                    .catch(erro => {
                        alert('❌ Erro de conexão');
                    });
            }
        };

        function fecharModalEncomenda() {
            document.getElementById("modalEncomenda").style.display = "none";
        }

        // FUNÇÃO ÚNICA PARA CRIAR O PEDIDO NO BANCO
        async function criarPedido(tipo) {
            const quantidade = document.getElementById("modalQuantidade").value;
            console.log("Aqui: " + quantidade);
            const dados = {
                id_produto: produtoAtual.id_produto,
                quantidade: quantidade,
                preco: produtoAtual.preco,
                tipo: tipo
            };

            if (tipo === "Encomenda") {
                dados.cor_bolsa = document.getElementById("corBolsa").value || null;
                dados.personagem = document.getElementById("nomePersonagem").value || null;
            }

            const resp = await fetch("criar_pedido.php", {
                method: "POST",
                body: new URLSearchParams(dados)
            });

            const data = await resp.json();

            if (data.ok) {
                alert("Pedido criado com sucesso!");
                fecharModalEncomenda();
                fecharModalProduto();
            } else {
                alert("Erro ao criar pedido.");
            }
        }

        // ENCOMENDA
        async function confirmarEncomenda() {
            if (iscarrinho === true) {
                const dados = {
                    id_carrinho: document.getElementById("id_carrinho").value,
                    id_produto: document.getElementById("id_produto").value,
                    quantidade: document.getElementById("modalQuantidade").value,
                    cor_item: document.getElementById("corBolsa").value || null,
                    nm_personagem: document.getElementById("nomePersonagem").value || null
                }
                fetch('adicionarCarrinho.php', {
                        method: 'POST',
                        body: new URLSearchParams(dados) // envia como formData
                    })
                    .then(resposta => resposta.json()) // ← CONVERTE resposta do PHP
                    .then(resultado => { // ← RECEBE o JSON convertido
                        console.log("DEBUG: PHP retornou:", resultado); // Opcional: ver no console

                        if (resultado.success) { // ← VERIFICA se deu certo
                            alert('✅ Adicionado ao carrinho!');
                            fecharModalProduto();
                        } else {
                            alert('❌ Erro: ' + (resultado.message || 'Erro desconhecido'));
                        }
                    })
                    .catch(erro => {
                        console.error("Erro no fetch:", erro);
                        alert('❌ Erro de conexão com o servidor');
                    });
            } else { //VI no gpt msm, tava cansado véi
                criarPedido("Encomenda");
            }
        }
        

    </script>


</body>

</html>