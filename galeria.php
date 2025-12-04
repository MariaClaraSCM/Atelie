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
    <head>
    <meta charset="UTF-8" />
    <!-- Fav icon  -->
    <link rel="icon" type="image/svg+xml" href="./assets/imagotipo.svg" sizes="any" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <!-- Fontes do site: Inter, sans-serif e Instrument Serif, serif -->
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    <!-- Ícones: Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Ateliê Vó Egina</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="assets/styles/galeria.css">
    <style>
        body {
            font-family: "Inter", sans-serif;
            margin: 0;
            padding: 0;
        }

        .galeria-hero {
            /* max-width: 900px; */
            /* margin: 80px auto; */
            padding: 40px;
            text-align: center;
            background: url('./assets/autenticacao/fundo.svg');
        }

        .galeria-hero h1 {
            font-family: "Instrument Serif", serif;
            font-size: 40px;
            margin-bottom: 16px;
            font-weight: 400;
        }

        .galeria-hero p {
            font-size: 18px;
            color: #444;
            max-width: 650px;
            margin: 0 auto 50px;
        }

        #modais-container {
            position: relative;
            min-height: 260px;
        }

        .modal {
            display: none;
            background: #fff;
            border-radius: 14px;
            padding: 30px;
            max-width: 520px;
            margin: 0 auto;
            transition: 0.2s;
        }

        .modal.active {
            display: block;
        }
        
        .icone-modal i {
            font-size: 32px;
            color: #0A111A;
            margin-bottom: 18px;
        }

        .modal h2 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 14px;
        }
        
        .modal p {
            font-size: 14px;
            line-height: 1.5;
            color: #444;
            max-width: 420px;
            margin: 0 auto;
        }
        
        #modais-container::after {
            content: "⌄";
            font-size: 20px;
            display: block;
            margin-top: 25px;
        }

        .modal:hover {
            transform: translateY(-2px);
        }
        
        #breadcrumbs {
            margin-top: 35px;
            display: flex;
            justify-content: center;
            gap: 14px;
        }

        .breadcrumb {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 2px solid #bbb;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 15px;
            background: white;
            transition: 0.2s;
        }

        .breadcrumb.active {
            background: black;
            color: white;
            border-color: black;
        }

        .breadcrumb:not(.active):hover {
            border-color: #000;
        }
        /* GRID GERAL */
        .galeria-grid {
            padding: 40px;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 10px;
        }

        /* CARD */
        .galeria-card {
            background: #fff;
            border-radius: 14px;
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            box-shadow: 0 6px 16px rgba(0,0,0,0.10);
            border: 1px solid #e5e5e5;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
            cursor: default;
        }

        .galeria-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 22px rgba(0,0,0,0.15);
        }

        /* IMAGEM DO PRODUTO */
        .galeria-img {
            width: 250px;
            height: 250px;
            object-fit: cover;
            border-radius: 10px;
            border: 1px solid #ddd;
            margin: 0 auto;
        }

        /* INFORMAÇÕES */
        .galeria-info {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .galeria-info h2 {
            font-size: 18px;
            margin: 0;
            color: #222;
        }

        .galeria-info p {
            margin: 0;
            line-height: 1.4;
            color: #555;
        }

        /* META (categoria, preço, etc.) */
        .galeria-meta {
            display: flex;
            flex-direction: column;
            gap: 4px;
            margin-top: 6px;
        }

        .galeria-meta p b {
            font-weight: 600;
            color: #222;
        }
        
        .paginacao {
            display: flex;
            gap: 8px;
            justify-content: center;
            margin: 20px 0;
        }

        .paginacao a {
            text-decoration: none;
        }

        .paginacao button {
            padding: 8px 14px;
            border: 1px solid #ccc;
            background: #f2f2f2;
            cursor: pointer;
            border-radius: 6px;
            font-size: 14px;
            transition: 0.2s ease-in-out;
        }

        .paginacao button:hover {
            background: #e0e0e0;
        }

        .paginacao button.ativo {
            background: #333;
            color: #fff;
            border-color: #333;
        }

    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    <main>
        <section class="galeria-hero">
            <h1>Como seu produto fica do seu jeitinho?</h1>
            <p>Deixe-me guiar você por um processo simples e prático para transformar suas ideias em realidade.</p>
            <div id="modais-container"></div>
            <div id="breadcrumbs"></div>
        </section>
    </main>
    <script>
        const passos = [
            {
                icone: "<i class='fa-regular fa-face-laugh-beam'></i>",
                titulo: "Escolha um modelo",
                texto: "Você começa escolhendo o que quer: mochila, máscara, nécessaire, lembrancinha ou outro item feito à mão com muito capricho."
            },
            {
                icone: "<i class='fa-regular fa-message'></i>",
                titulo: "Escolha o personagem ou tema",
                texto: "Escolha o personagem ou tema que deseja. A arte é feita especialmente para você por R$15 e enviada para aprovação antes da produção."
            },
            {
                icone: "<i class='fa-regular fa-credit-card'></i>",
                titulo: "Entrada de 50% do valor",
                texto: "Com a arte aprovada, você paga 50% do valor do produto para que possamos começar a confecção personalizada."
            },
            {
                icone: "<i class='fa-regular fa-thumbs-up'></i>",
                titulo: "Confecção do produto",
                texto: "Com tudo aprovado, a arte é impressa, prensada no tecido e entramos em ação para confeccionar seu produto."
            },
            {
                icone: "<i class='fa-regular fa-truck'></i>",
                titulo: "Pagamento final e entrega",
                texto: "Produto finalizado! Basta pagar os 50% restantes e combinar a entrega. Você terá uma peça única feita para você!"
            }
        ];

        let passoAtual = 0;

        const breadcrumbsEl = document.getElementById("breadcrumbs");
        const modaisContainer = document.getElementById("modais-container");

        passos.forEach((_, i) => {
            const item = document.createElement("div");
            item.classList.add("breadcrumb");
            if (i === passoAtual) item.classList.add("active");
            item.textContent = i + 1;

            item.addEventListener("click", () => trocarPasso(i));
            breadcrumbsEl.appendChild(item);
        });

        passos.forEach((p, i) => {
            const modal = document.createElement("div");
            modal.classList.add("modal");
            if (i === passoAtual) modal.classList.add("active");

            modal.innerHTML = `
                <div class="icone-modal">${p.icone}</div>
                <h2>${p.titulo}</h2>
                <p>${p.texto}</p>
            `;

            modaisContainer.appendChild(modal);
        });

        function trocarPasso(novo) {
            passoAtual = novo;

            document.querySelectorAll(".breadcrumb").forEach((b, i) => {
                b.classList.toggle("active", i === passoAtual);
            });

            document.querySelectorAll(".modal").forEach((m, i) => {
                m.classList.toggle("active", i === passoAtual);
            });
        }
    </script>

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
        <section class="galeria-grid">

            <?php foreach ($produtosAtuais as $p): ?>
                <div class="galeria-card"
                    data-cat="<?= $p["nm_categoria"] ?>"
                    data-preco="<?= $p["preco"] ?>"
                    data-id="<?= $p["id_produto"] ?>">

                    <img src="<?= $p["foto"] ?>" class="galeria-img" alt="<?= $p["nm_produto"] ?>">

                    <div class="galeria-info">
                        <h2><?= $p["nm_produto"] ?></h2>
                        <p><?= $p["descricao"] ?></p>

                        <div class="galeria-meta">
                            <p><b>Categoria:</b> <?= $p["nm_categoria"] ?></p>
                        </div>
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