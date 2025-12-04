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
            <button id="btnFinalizarPedido" class="btnComprar">Finalizar Pedido</button>
        </div>
        <button class="btnFechar" onclick="fecharModal('modalConfirmacao')">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
</div>

<style>
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
    background:#007BFF;
    color:#fff;
    border-radius:4px;
    cursor:pointer;
}
.metodosPagamento {
    display:flex;
    gap:8px;
}
</style>

<script>
// Abrir e fechar modais
function abrirModal(id) { document.getElementById(id).style.display = 'flex'; }
function fecharModal(id) { document.getElementById(id).style.display = 'none'; }

// Exemplo: preencher modal nota fiscal com itens do carrinho
let carrinho = [
    { nome: 'Produto 1', quantidade: 2, preco: 50 },
    { nome: 'Produto 2', quantidade: 1, preco: 80 },
]; // substituir por fetch do PHP

function formatarReal(valor) {
    return new Intl.NumberFormat("pt-BR", { style: "currency", currency: "BRL" }).format(valor);
}

// Preenche Nota Fiscal
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

document.getElementById('btnConfirmarNota').onclick = () => {
    fecharModal('modalNotaFiscal');
    abrirModal('modalEndereco');
};

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
        document.querySelectorAll('.btnPagamento').forEach(b=>b.style.background='#007BFF');
        btn.style.background = '#28a745';
    });
});

document.getElementById('btnFinalizarPedido').onclick = () => {
    alert('Pedido finalizado com sucesso!');
    fecharModal('modalConfirmacao');
};

// Abrir o primeiro modal para teste
// preencherNotaFiscal();
// abrirModal('modalNotaFiscal');
</script>
