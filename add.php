<?php
/*******************************
 * User: nathan
 * Project: configurador
 * File: add.php
 * Created: 20/12/16 16:39
 * Modified:  
 *******************************/
define("_TMP_", "/tmp/config.tmp");

 if(!empty($_POST['info1']) && !empty($_POST['info2'])):

    switch ($_POST['config']){
        case "cameras":

            $read = $_POST['read'];
            $info_concat = $_POST['info1'] . "_" . $_POST['info2'];

            if(strpos($read, $info_concat) === false){

                $readNew = parse_ini_file(_TMP_);

                $readNew['cameras'] = $read . $info_concat . "|";
                $readNew['zona_' . $_POST['info2']] = "1";

                write_ini_file($readNew, _TMP_, false);

                echo "1*" . $readNew['cameras'];
            } else {
                echo "-1*Câmera e sensor já estão sendo utilizados!";
            }

            break;

        case "servidores":

            $read = $_POST['read'];
            $info_concat = $_POST['info1'] . ":" . $_POST['info2'];

            if(strpos($read, $info_concat) === false){

                $fp = @fsockopen($_POST['info1'], $_POST['info2'], $errno, $errstr, 10);

                if ($fp) {
                    // ok
                    fclose($fp);

                    $readNew = parse_ini_file(_TMP_);
                    $readNew['servidores'] = $read . $info_concat . "|";

                    $servidores = explode("|", $readNew['servidores']);

                    foreach ($servidores as $key => $servidor) {
                        if(count($servidores) != ($key + 1)) {
                            $server_port = explode(":", $servidor);
                            $readNew["servidor_" . ($key + 1)] = $server_port[0];
                            $readNew["porta_" . ($key + 1)] = $server_port[1];
                        }
                    }

                    write_ini_file($readNew, _TMP_, false);

                    echo "1*" . $read . $info_concat . "|";

                } else {
                    echo "-1*Servidor inserido não está respondendo!";
                }
            } else {
                echo "-1*Servidor e porta já estão sendo utilizados!";
            }

            break;

        case "save":
            require_once "banco.php";
            $bd = new DataBase();
            $bd->insertFree("UPDATE dev.stores SET exception = '" . $_POST["insert"] ."' WHERE " . "id = " . $_POST["id"]);
            copy(_TMP_, "config.conf");
            break;

        default:
    }


 else:
     echo "-1*Preencha todos os campos!";
 endif;

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
