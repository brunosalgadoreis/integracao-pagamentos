<?php

use App\DB\Db;

$select = new Db;
$pedidos = $select->list();

?>
<div class="mb-3 text-center">
  <h2>Pedidos</h2>
</div>
<table class="table">

  <thead>
    <tr>
      <th scope="col">Id</th>
      <th scope="col">Total</th>
      <th scope="col">Frete</th>
      <th scope="col">Data</th>
      <th scope="col">Nome Cliente</th>
      <th scope="col">Loja</th>
      <th scope="col">Situação</th>
    </tr>
  </thead>

  <tbody>
    <?php
    if (!is_null($pedidos)) {
      foreach ($pedidos as $pedido) { ?>
        <tr>
          <th scope="row"><?php echo $pedido['codigo']; ?></th>
          <td><?php echo $pedido['valor_total']; ?></td>
          <td><?php echo $pedido['valor_frete']; ?></td>
          <td><?php echo $pedido['data']; ?></td>
          <td><?php echo $pedido['nome']; ?></td>
          <td><?php echo $pedido['id_loja']; ?></td>
          <td><?php echo $pedido['descricao']; ?></td>
          <td>
            <a href="visualizar.php?id=<?php echo $pedido['codigo']; ?>"><input type="button" class="btn btn-outline-success" value="Processar"></a>
            </a>
          </td>

        </tr>
    <?php }
  }; ?>
  </tbody>
</table>