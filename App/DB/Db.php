<?php

namespace App\DB;

use PDO;
use PDOException;

class Db
{
    private $db;
    //Conexão com o banco de dados
    public function __construct()
    {

        try {
            $this->db = new PDO('pgsql:host=localhost;dbname=ecompleto', 'postgres', '123');
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            $log = "log.txt";
            $arquivo = fopen($log, "a+");
            $msgErro = date('d/m/Y H:i:s')." - Ocorreu um erro ao tenter se conectar ao banco de dados. Log de erro: ". $e->getMessage() . "\r\n\n";
            fwrite($arquivo, $msgErro);
            fclose($arquivo);
            header("Location: erro.php");
        }

    }

    //Listar Pedidos
    public function list()
    {
        $sql = $this->db->prepare("SELECT *, pedidos.id as codigo FROM pedidos, clientes, pedido_situacao 
        WHERE pedidos.id_cliente = clientes.id AND pedidos.id_situacao = pedido_situacao.id 
        AND pedidos.id_loja IN (SELECT id_loja FROM lojas_gateway WHERE id_gateway = 1) ORDER BY pedidos.id ASC");
        $sql->execute();
        if ($sql->rowCount() > 0) {
            $result = $sql->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        }
    }

    //Visualizaçao do Pedido 1
    public function selectPedido($id)
    {
        $sql = $this->db->prepare("SELECT *, pedidos.id as codigo, clientes.id as cod_cliente FROM pedidos, clientes, pedido_situacao  
                WHERE pedidos.id_cliente = clientes.id AND pedidos.id_situacao = pedido_situacao.id 
                AND pedidos.id = $id");
        $sql->execute();
        if ($sql->rowCount() > 0) {
            $result = $sql->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        }
    }

    //Visualizaçao do Pedido 2
    public function selectPagamento($id)
    {
        $sql = $this->db->prepare("SELECT *, pedidos_pagamentos.id as cod_pag FROM pedidos_pagamentos, formas_pagamento 
            WHERE pedidos_pagamentos.id_formapagto = formas_pagamento.id AND id_pedido = $id");
        $sql->execute();
        if ($sql->rowCount() > 0) {
            $result = $sql->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        }
    }

    //Update status do pedido
    public function updateStatus($tc, $id)
    {
        $sql = $this->db->prepare("UPDATE pedidos SET id_situacao=? WHERE id = $id");
        $sql->execute(array($tc));
    }

    //Update retorno do pagamento
    public function updatePagamento($msg, $data, $id)
    {
        $sql = $this->db->prepare("UPDATE pedidos_pagamentos SET retorno_intermediador=?, data_processamento=? WHERE id = $id");
        $sql->execute(array($msg, $data));
    }
}
