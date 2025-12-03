<?php
require 'config.php';

// Buscar produtos
$sql = "SELECT p.*, c.nm_categoria 
        FROM produto p 
        LEFT JOIN categoria c ON c.id_categoria = p.id_categoria";

$stmt = $pdo->prepare($sql);
$stmt->execute();

$produtos = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

    // Tratar a foto única do produto
    if (!empty($row["foto_produto"])) {
        $row["foto"] = $row["foto_produto"];
    } else {
        $row["foto"] = "https://abd.org.br/wp-content/uploads/2023/09/placeholder-284.png"; // caso não tenha foto
    }

    $produtos[] = $row;
}

$sqlCat = "SELECT nm_categoria FROM categoria ORDER BY nm_categoria ASC";
$stmtCat = $pdo->prepare($sqlCat);
$stmtCat->execute();
$categorias = $stmtCat->fetchAll(PDO::FETCH_ASSOC);

// Paginação
$produtosPorPagina = 9;
$totalProdutos = count($produtos);
$totalPaginas = ceil($totalProdutos / $produtosPorPagina);

$currentPage = isset($_GET["page"]) ? intval($_GET["page"]) : 1;
if ($currentPage < 1) $currentPage = 1;
if ($currentPage > $totalPaginas) $currentPage = $totalPaginas;

$inicio = ($currentPage - 1) * $produtosPorPagina;
$produtosAtuais = array_slice($produtos, $inicio, $produtosPorPagina);

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="produtos.css">

    <!-- FONT AWESOME -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <title>Galeria de Produtos</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="./assets/styles/galeria.css">
</head>

<body>

    <?php include 'header.php'; ?>
    <div class="filtro">

        <!-- CATEGORIAS -->
        <div class="btncategorias">

            <!-- Botão padrão -->
            <button class="btn-cat ativo" data-cat="Todos">Todos</button>

            <!-- Categorias vindas do banco -->
            <?php foreach ($categorias as $c): ?>
                <button class="btn-cat" data-cat="<?= $c['nm_categoria'] ?>">
                    <?= $c['nm_categoria'] ?>
                </button>
            <?php endforeach; ?>

        </div>

        <!-- ORDENAÇÃO E VISUALIZAÇÃO -->
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
                        <img src="./assets/produtos/grid.png">
                    </button>

                    <button id="btnList" data-view="list">
                        <img src="./assets/produtos/list.svg">
                    </button>
                </div>

            </div>
        </div>

    </div>


    <main class="galeriaProdutos">

        <!-- ============================== -->
        <!--       GRID DE PRODUTOS         -->
        <!-- ============================== -->
        <section class="gridProdutos">

            <?php foreach ($produtosAtuais as $p): ?>
                <div class="produtoAdm"
                    data-cat="<?= $p["nm_categoria"] ?>"
                    data-preco="<?= $p["preco"] ?>"
                    data-id="<?= $p["id_produto"] ?>">

                    <!-- FOTO ÚNICA DO PRODUTO -->
                    <img src="<?= $p["foto"] ?>" class="foto-produto" alt="<?= $p["nm_produto"] ?>">

                    <div class="info">
                        <h2><?= $p["nm_produto"] ?></h2>
                        <p><?= $p["descricao"] ?></p>

                        <div class="ajusteCardProduto">
                            <p><b>R$ <?= $p["preco"] ?></b></p>
                            <p><b>Categoria:</b> <?= $p["nm_categoria"] ?></p>
                        </div>
                    </div>

                    <div class="acoesAdm">
                        <button class="favoritar"></button>

                        <button class="fav-carrinho">
                            <img src="./assets/produtos/fav.svg" alt="" class="fav">
                        </button>
                    </div>

                </div>
            <?php endforeach; ?>

        </section>


        <!-- ============================== -->
        <!--           PAGINAÇÃO            -->
        <!-- ============================== -->
        <div class="paginacao">
            <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                <a href="?page=<?= $i ?>">
                    <button class="<?= $i == $currentPage ? 'ativo' : '' ?>">
                        <?= $i ?>
                    </button>
                </a>
            <?php endfor; ?>
        </div>

    </main>

    <!-- ========================================== -->
    <!--              JAVASCRIPT                    -->
    <!-- ========================================== -->
    <script>
        let categoriaSelecionada = "Todos";
        let ordenacaoSelecionada = "Recentes";
        let visualizacaoSelecionada = "grid";

        const produtos = document.querySelectorAll(".produtoAdm");

        // -------------- FILTRAR POR CATEGORIA --------------
        document.querySelectorAll(".btn-cat").forEach(btn => {
            btn.addEventListener("click", () => {

                categoriaSelecionada = btn.dataset.cat;

                document.querySelectorAll(".btn-cat").forEach(b => b.classList.remove("ativo"));
                btn.classList.add("ativo");

                aplicarFiltros();
            });
        });

        // -------------- ORDENAR PRODUTOS --------------
        document.getElementById("ordenacao").addEventListener("change", (e) => {
            ordenacaoSelecionada = e.target.value;
            aplicarFiltros();
        });

        // -------------- VISUALIZAÇÃO GRID / LIST --------------
        document.getElementById("btnGrid").addEventListener("click", () => {
            visualizacaoSelecionada = "grid";
            document.getElementById("btnGrid").classList.add("ativo");
            document.getElementById("btnList").classList.remove("ativo");

            document.querySelector(".gridProdutos").classList.remove("lista");
        });

        document.getElementById("btnList").addEventListener("click", () => {
            visualizacaoSelecionada = "list";
            document.getElementById("btnList").classList.add("ativo");
            document.getElementById("btnGrid").classList.remove("ativo");

            document.querySelector(".gridProdutos").classList.add("lista");
        });

        // -------------- FUNÇÃO PRINCIPAL --------------
        function aplicarFiltros() {

            produtos.forEach(prod => {
                const categoria = prod.dataset.cat;

                // ---- FILTRO POR CATEGORIA ----
                if (categoriaSelecionada !== "Todos" && categoria !== categoriaSelecionada) {
                    prod.style.display = "none";
                } else {
                    prod.style.display = "flex";
                }
            });

            // ---- ORDENAR ----
            ordenarProdutos();

        }

        // -------------- FUNÇÃO DE ORDENAR --------------
        function ordenarProdutos() {
            const container = document.querySelector(".gridProdutos");
            const lista = Array.from(produtos);

            let ordenado = [];

            if (ordenacaoSelecionada === "Recentes") {
                ordenado = lista.sort((a, b) => b.dataset.id - a.dataset.id);
            }
            if (ordenacaoSelecionada === "Antigos") {
                ordenado = lista.sort((a, b) => a.dataset.id - b.dataset.id);
            }
            if (ordenacaoSelecionada === "Mais baratos") {
                ordenado = lista.sort((a, b) => a.dataset.preco - b.dataset.preco);
            }
            if (ordenacaoSelecionada === "Mais caros") {
                ordenado = lista.sort((a, b) => b.dataset.preco - a.dataset.preco);
            }

            ordenado.forEach(item => container.appendChild(item));
        }
    </script>
    <?php include 'footer.php'; ?>
</body>

</html>