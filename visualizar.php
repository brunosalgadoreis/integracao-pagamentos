<?php
require_once __DIR__ . '/vendor/autoload.php';
include __DIR__ . '/includes/header.php';

use App\Classes\Pagamentos;

$objPedido = new Pagamentos;

$id = $_GET['id'];
$pedidos = $objPedido->getPedido($id);
$pagamentos = $objPedido->getPagamento($id);

if (isset($_POST['processar'])) {

    $id_pedido = $_POST['codigo'];
    $total = $_POST['valor_total'];
    $cartao = $_POST['num_cartao'];
    $cvv = $_POST['codigo_verificacao'];
    $vencimento = $_POST['vencimento'];
    $portador = $_POST['nome_portador'];
    $id_cliente = $_POST['cod_cliente'];
    $nome_cliente = $_POST['nome'];
    $email = $_POST['email'];
    $cpf = $_POST['cpf_cnpj'];
    $data_nasc = $_POST['data_nasc'];
    $cod_pag = $_POST['cod_pag'];

    $transacao = array(
        "id_pedido" => $id_pedido,
        "total" => $total,
        "cartao" => $cartao,
        "cvv" => $cvv,
        "vencimento" => $vencimento,
        "portador" => $portador,
        "id_cliente" => $id_cliente,
        "nome_cliente" => $nome_cliente,
        "email" => $email,
        "cpf" => $cpf,
        "data_nasc" => $data_nasc,
        "cod_pag" => $cod_pag
    );

    $objPedido->transacao($transacao);

    header("refresh: 2; url=index.php");
    exit;
}

?>
<div class="mb-3 text-center">
    <h2>Dados do Pagamento</h2>
</div>

<div class="container mt-3">
    <?php foreach ($pedidos as $pedido) { ?>
        <div class="row align-items-start">
            <div class="col">
                <p><b>Código:</b> <?php echo $pedido['codigo']; ?></p>
            </div>
            <div class="col">
                <p><b>Total:</b> <?php echo $pedido['valor_total']; ?></p>
            </div>
            <div class="col">
                <p><b>Frete:</b> <?php echo $pedido['valor_frete']; ?></p>
            </div>
            <div class="col">
                <p><b>Data:</b> <?php echo $pedido['data']; ?></p>
            </div>
        </div>
        <div class="row align-items-center">
            <div class="col">
                <p><b>Cliente:</b> <?php echo $pedido['nome']; ?></p>
            </div>
            <div class="col">
                <p><b>Loja:</b> <?php echo $pedido['id_loja']; ?></p>
            </div>
            <div class="col">
                <p><b>Situação:</b> <?php echo $pedido['descricao']; ?></p>
            </div>

        </div>
        <div class="row align-items-end">
            <div class="col">
                <p><b>Email:</b> <?php echo $pedido['email']; ?></p>
            </div>
        </div>

        <hr>
        <?php foreach ($pagamentos as $pagamento) { ?>
            <div class="row align-items-start">
                <div class="col">
                    <p><b>Pagamento:</b> <?php echo $pagamento['cod_pag']; ?></p>
                </div>
                <div class="col">
                    <p><b>Forma de Pagamento:</b> <?php echo $pagamento['descricao']; ?></p>
                </div>
                <div class="col">
                    <p><b>Parcelas:</b> <?php echo $pagamento['qtd_parcelas']; ?></p>
                </div>
            </div>
            <div class="row align-items-center">
                <div class="col">
                    <p><b>Retorno:</b> </b> <?php echo $pagamento['retorno_intermediador']; ?></p>
                </div>
                <div class="col">
                    <p><b>Data Proc:</b> </b> <?php echo $pagamento['data_processamento']; ?></p>
                </div>
                <div class="col">
                    <p><b>Cartão:</b> </b> <?php echo $pagamento['num_cartao']; ?></p>
                </div>
            </div>
            <div class="row align-items-end">
                <div class="col">
                    <p><b>Portador:</b> </b> <?php echo $pagamento['nome_portador']; ?></p>
                </div>
                <div class="col">
                    <p><b>Códig Verif:</b> </b> <?php echo $pagamento['codigo_verificacao']; ?></p>
                </div>
                <div class="col">
                    <p><b>Vencimento:</b> </b> <?php echo $pagamento['vencimento']; ?></p>
                </div>
            </div>
            <hr>

            <form method="POST" id="form_processar">
                <input type="hidden" name="codigo" value="<?php echo $pedido['codigo']; ?>">
                <input type="hidden" name="valor_total" value="<?php echo $pedido['valor_total']; ?>">
                <input type="hidden" name="num_cartao" value="<?php echo $pagamento['num_cartao']; ?>">
                <input type="hidden" name="codigo_verificacao" value="<?php echo $pagamento['codigo_verificacao']; ?>">
                <input type="hidden" name="vencimento" value="<?php echo $pagamento['vencimento']; ?>">
                <input type="hidden" name="nome_portador" value="<?php echo $pagamento['nome_portador']; ?>">
                <input type="hidden" name="cod_cliente" value="<?php echo $pedido['cod_cliente']; ?>">
                <input type="hidden" name="nome" value="<?php echo $pedido['nome']; ?>">
                <input type="hidden" name="email" value="<?php echo $pedido['email']; ?>">
                <input type="hidden" name="cpf_cnpj" value="<?php echo $pedido['cpf_cnpj']; ?>">
                <input type="hidden" name="data_nasc" value="<?php echo $pedido['data_nasc']; ?>">
                <input type="hidden" name="cod_pag" value="<?php echo $pagamento['cod_pag']; ?>">
            </form>

            <?php
            if ($pagamento['descricao'] == 'Cartão de Crédito' && $pedido['descricao'] == 'Aguardando Pagamento') {
                echo "<button type='submit' form='form_processar' class='btn btn-success' name='processar'>Processar Pagamento</button>";
            } else {
                echo "<div class='alert alert-danger' role='alert'>Este pagamento não pode ser processado.</div>";
            };
            ?>
        <?php } ?>
    <?php } ?>

</div>

<?php
include __DIR__ . '/includes/footer.php';
?>