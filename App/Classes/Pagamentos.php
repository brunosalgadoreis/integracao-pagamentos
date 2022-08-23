<?php

namespace App\Classes;

include "Config.php";

use App\DB\Db;

class Pagamentos extends Db
{

    private $key = TOKEN_ECOMPLETO;

    //Busca dados do pedido
    public function getPedido($id)
    {
        $db = new Db;
        if (isset($id) && !is_null($id)) {
            $result = $db->selectPedido($id);
        }
        return $result;
    }

    //Busca dados do pagamento
    public function getPagamento($id)
    {
        $db = new Db;
        if (isset($id) && !is_null($id)) {
            $result = $db->selectPagamento($id);
        }
        return $result;
    }

    // Faz a transação com a API
    public function transacao($transacao)
    {
        $timestamp = strtotime($transacao['vencimento']);
        $venc = date('my', $timestamp);
        $inteiro = intval($venc);
        $venciment = str_pad($inteiro, 4, '0', STR_PAD_LEFT);

        $url = "https://api11.ecompleto.com.br/exams/processTransaction?accessToken=$this->key";

        $ch = curl_init();

        $conteudo = array(
            "external_order_id" => intval($transacao['id_pedido']),
            "amount" => floatval($transacao['total']),
            "card_number" => $transacao['cartao'],
            "card_cvv" => $transacao['cvv'],
            "card_expiration_date" => $venciment,
            "card_holder_name" => $transacao['portador'],
            "customer" => array(
                "external_id" => $transacao['id_cliente'],
                "name" => $transacao['nome_cliente'],
                "type" => "individual",
                "email" =>  $transacao['email'],
                "documents" =>  array(
                    "type" => "cpf",
                    "number" => $transacao['cpf']
                ),
                "birthday" => $transacao['data_nasc']
            ),
        );

        $data = json_encode($conteudo);

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $headers = array();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }

        $resultado = json_decode($result, true);
        curl_close($ch);

        $msg = $resultado['Message'];
        $data =  date('d/m/Y');

        if (isset($resultado['Transaction_code'])) {
            $tc = $resultado['Transaction_code'];
        };

        if ($resultado['Error'] == true) {
            $this->guardaRetorno($msg, $data, $transacao['cod_pag']);
            echo "<div class='alert alert-danger mt-3' role='alert'>'$msg'</div>";
        } else {
            $this->guardaRetorno($msg, $data, $transacao['cod_pag']);
            $this->atualizaSituacao($tc, $transacao['id_pedido']);
            echo "<div class='alert alert-success mt-3' role='alert'>'$msg'</div>";
        };
    }

    //Atualiza a situação do pedido
    public function atualizaSituacao($tc, $id)
    {
        $db = new Db;
        switch ($tc) {
            case "00":
                $db->updateStatus(2, $id);
                break;
            case "01":
                $db->updateStatus(1, $id);
                break;
            default:
                $db->updateStatus(3, $id);
        }
    }

    //Guarda o retorno e data de processamento da transação
    public function guardaRetorno($msg, $data, $id)
    {
        $db = new Db;
        if (!empty($msg) && !empty($data)) {
            $db->updatePagamento($msg, $data, $id);
        };
    }
}
