<?php
session_start();
require 'config.php';


$query = $pdo->query("SELECT id_produto, nm_produto, preco, descricao, foto_produto,tipo,qt_tamanho, id_categoria FROM produto ORDER BY id_produto ASC");
$servicos = $query->fetchAll(PDO::FETCH_ASSOC);

// Array de depoimentos (equivalente ao testemonials do React)
$testemonials = [
  [
    'photo'  => 'https://images.unsplash.com/photo-1607746882042-944635dfe10e',
    'name'   => 'Ana',
    'rating' => 5,
    'message' => 'Recebi minha encomenda exatamente como pedi. Acabamento impecável. Valeu muito a pena!'
  ],
  [
    'photo'  => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2',
    'name'   => 'Brenda',
    'rating' => 5,
    'message' => 'A qualidade superou minhas expectativas. Atendimento rápido e muito profissional.'
  ],
  [
    'photo'  => 'https://images.unsplash.com/photo-1502685104226-ee32379fefbe',
    'name'   => 'Clara',
    'rating' => 5,
    'message' => 'Produto lindo e muito bem feito. Com certeza comprarei novamente.'
  ],
];
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8" />
  <!-- Fav icon  -->
  <link rel="icon" type="image/svg+xml" href="/imagotipo.svg" sizes="any" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <!-- Fontes do site: Inter, sans-serfic e Instrument Serif, serif -->
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
  <!-- Ícones: Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <title>Ateliê Vó Egina</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="assets/styles/home.css">
</head>

<body>
  <?php include 'header.php'; ?>

  <div class="home">
    <!-- HERO -->
    <section class="hero">
      <div class="hero-text">
        <h1>Bolsas feitas à mão com amor.</h1>
        <p>
          Aqui, cada peça é feita com amor, cuidado e um toque especial de carinho de vó.
          Produzimos bolsas, lancheiras e muito mais — tudo personalizado para encantar
          e facilitar o dia a dia da sua família.
        </p>
        <button class="hero-btn pronta-entrega" onclick="window.location.href='galeria.php'">Ver produtos</button>
        <button class="hero-btn encomendar" onclick="window.location.href='produtosPedir.php'">Encomendar produto</button>
      </div>
    </section>

    <!-- POR QUE ESCOLHER -->
    <section class="why-choose-us">
      <h1>Por que escolher nosso ateliê?</h1>
      <p>Porque entregamos qualidade e confiança com carinho, feito especialmente para você.</p>
      <div class="motives">
        <div class="motive-card">
          <div class="icon-margin">
            <img src="./assets//home/iconArtesanal.svg"
              alt="Artesanal — peça feita à mão, representando cuidado artesanal">
          </div>
          <h2>Artesanal</h2>
          <p>Cada peça do Ateliê Vó Egina é feita à mão, com atenção minuciosa aos detalhes.</p>
        </div>
        <div class="motive-card">
          <div class="icon-margin">
            <img src="./assets/home/iconBoaQualidade.svg"
              alt="Boa Qualidade — durabilidade, beleza e acabamento impecável">
          </div>
          <h2>Boa Qualidade</h2>
          <p>Garantimos durabilidade, beleza e acabamento impecável em todos os nossos produtos.</p>
        </div>
        <div class="motive-card">
          <div class="icon-margin">
            <img src="./assets/home/iconPensadoComAmor.svg"
              alt="Pensado com Amor — peça única, criada com atenção às preferências do cliente">
          </div>
          <h2>Pensado com Amor</h2>
          <p>
            Aqui, cada peça é única. Egina ouve o cliente e transforma ideias em produtos
            que aquecem o coração.
          </p>
        </div>
      </div>
    </section>

    <!-- NOSSOS PRODUTOS -->
    <section class="our-products">
      <h1>Nossos Produtos</h1>
      <p>Prático para o dia-a-dia e belo para um evento especial. Fazemos do seu jeitinho!</p>
      <div class="products">
        <div class="product">
          <img src="assets/home/imgProdutos.png" alt="">
          <h3>Bolsas e Mais</h3>
          <p>
            Explore: linha escolar, lembrancinhas, maternidade e muito mais,
            prontos para você escolher e se encantar.
          </p>
          <a href="/verprodutos">
            <button class="product-btn pronta-entrega">Ver todos os produtos</button>
          </a>
        </div>
        <div class="product">
          <img src="assets/home/imgEncomendas.png" alt="">
          <h3>Galeria de Encomendas</h3>
          <p>
            Inspire-se com peças personalizadas por nossos clientes e peça a sua
            do jeitinho que quiser.
          </p>
          <button class="product-btn galeria">Ir para a Galeria</button>
        </div>
      </div>
    </section>

    <!-- DEPOIMENTOS -->
    <section class="testemonials">
      <h1>Depoimentos</h1>
      <div class="container-carrossel">
        <button id="btn-voltar">
          <i class="fa-solid fa-chevron-left"></i>
        </button>

        <div class="carrossel">
          <div id="track"
            class="testemonial-messages"
            data-total-cards="<?php echo count($testemonials); ?>">
            <!-- Primeira sequência de cards -->
            <?php foreach ($testemonials as $index => $t): ?>
              <div class="testemonial-card">
                <div class="info-user">
                  <img class="placeholder"
                    src="<?php echo htmlspecialchars($t['photo'], ENT_QUOTES, 'UTF-8'); ?>"
                    alt="User photo">
                  <span><?php echo htmlspecialchars($t['name'], ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
                <ul class="review-stars">
                  <?php for ($i = 0; $i < (int)$t['rating']; $i++): ?>
                    <li><i class="fa-solid fa-star"></i></li>
                  <?php endfor; ?>
                </ul>
                <p><?php echo htmlspecialchars($t['message'], ENT_QUOTES, 'UTF-8'); ?></p>
              </div>
            <?php endforeach; ?>

            <!-- Segunda sequência (duplicada) para efeito de loop -->
            <?php foreach ($testemonials as $index => $t): ?>
              <div class="testemonial-card" aria-hidden="true">
                <div class="info-user">
                  <img class="placeholder"
                    src="<?php echo htmlspecialchars($t['photo'], ENT_QUOTES, 'UTF-8'); ?>"
                    alt="User photo">
                  <span><?php echo htmlspecialchars($t['name'], ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
                <ul class="review-stars">
                  <?php for ($i = 0; $i < (int)$t['rating']; $i++): ?>
                    <li><i class="fa-solid fa-star"></i></li>
                  <?php endfor; ?>
                </ul>
                <p><?php echo htmlspecialchars($t['message'], ENT_QUOTES, 'UTF-8'); ?></p>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

        <button id="btn-proximo">
          <i class="fa-solid fa-chevron-right"></i>
        </button>
      </div>
    </section>

    <!-- CONTATO -->
    <section class="contato">
      <div class="contato-cta">
        <h2>Deseja encomendar?</h2>
        <p>Entre em contato pelo Whatsapp</p>
        <button>
          <i class="fa-brands fa-whatsapp"></i>Whatsapp
        </button>
      </div>
    </section>
  </div>

  <!-- JS do carrossel (equivalente ao useState/useEffect do React) -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const track = document.getElementById('track');
      if (!track) return;

      const cardWidth = 380; // mesma largura aproximada
      const totalCards = parseInt(track.getAttribute('data-total-cards')) || 0;
      let index = 0;
      let timeoutId = null;

      function atualizarCarrossel() {
        track.style.transform = 'translateX(' + (-index * cardWidth) + 'px)';
        clearTimeout(timeoutId);
        timeoutId = setTimeout(function() {
          track.style.animation = 'scrollInfinito 20s infinite linear';
        }, 4000);
      }

      function mover(direcao) {
        if (!track) return;

        index += direcao;
        if (index < 0) index = totalCards - 1;
        if (index >= totalCards) index = 0;

        // Pausa animação
        track.style.animation = 'none';
        atualizarCarrossel();
      }

      const btnVoltar = document.getElementById('btn-voltar');
      const btnProximo = document.getElementById('btn-proximo');

      if (btnVoltar) {
        btnVoltar.addEventListener('click', function() {
          mover(-1);
        });
      }

      if (btnProximo) {
        btnProximo.addEventListener('click', function() {
          mover(1);
        });
      }
    });
  </script>

      <?php include 'footer.php'; ?>
</body>

</html>