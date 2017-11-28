<?php
/*******************************
 * User: nathan
 * Project: configurador
 * File: insert.php
 * Created: 12/01/17 16:50
 * Modified:  
 *******************************/
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "banco.php";

$banco = new DataBase();


$banco->insert("stores", [
        "name" => ["'".$_POST['loja']."'"],
        "street" => ["'".$_POST['endereco']."'"],
        "number" => ["'".$_POST['numero']."'"],
        "district" => ["'".$_POST['bairro']."'"],
        "city" =>  ["'".$_POST['cidade']."'"],
        "UF" =>  ["'".$_POST['uf']."'"],
        "CEP" =>  ["'".$_POST['cep']."'"],
        "group_name" =>  ["'".$_POST['grupo']."'"],
        "tel" =>  ["'".$_POST['telefone']."'"],
        "nick" =>  ["'".$_POST['apelido']."'"],
        "leader" =>  ["'".$_POST['responsavel']."'"],
        "leader_tel" =>  ["'".$_POST['telefoneResponsavel']."'"],
        "opening" =>  ["'".$_POST['horarioEntrada']."'"],
        "ending" =>  ["'".$_POST['horarioSaida']."'"]
//        "exception" =>  ["'".$_POST['alarmeExcecao']."'"]
    ]
);

$readNew = parse_ini_file("config.conf");


$readNew['id_loja'] = $banco->lastId();

if($readNew['id_loja'] != 0) {
    write_ini_file($readNew, "config.conf", false);

    header("Location: index.php");
} else {
    echo "Erro ao salvar no banco";
}

function write_ini_file($assoc_arr, $path, $has_sections=FALSE) {
    $content = "";
        foreach ($assoc_arr as $key=>$elem) {
            if(is_array($elem))
            {
                for($i=0;$i<count($elem);$i++)
                {
                    $content .= $key."[] = \"".$elem[$i]."\"\n";
                }
            }
            else if($elem=="") $content .= $key." = \"\"\n";
            else $content .= $key." = \"".$elem."\"\n";
        }

    if (!$handle = fopen($path, 'w')) {
        return false;
    }

    $success = fwrite($handle, $content);
    fclose($handle);

    return $success;
}