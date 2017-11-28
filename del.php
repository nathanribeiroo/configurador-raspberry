<?php
/*******************************
 * User: nathan
 * Project: configurador
 * File: del.php
 * Created: 21/12/16 13:15
 * Modified:  
 *******************************/


define("_TMP_", "/tmp/config.tmp");

if(!empty($_POST['val1']) && !empty($_POST['val2'])):

    switch ($_POST['config']){
        case "cameras":

            $read = $_POST['read'];
            $read = str_replace($_POST['val1'] . "_" . $_POST['val2'] . "|", "", $read);

            $readNew = parse_ini_file(_TMP_);
            $readNew['cameras'] = $read;

            if(strpos($read, "_" . $_POST['val2']) === false){
                $readNew['zona_' . $_POST['val2']] = "0";
            }


            write_ini_file($readNew, _TMP_, false);

            echo "1*" . $read;
            break;

        case "servidores":


            $read = str_replace($_POST['val1'] . ":" . $_POST['val2'] . "|", "", $_POST['read']);

            $readNew = parse_ini_file(_TMP_);

            $limpaServidores = explode("|", $_POST['read']);

            foreach ($limpaServidores as $key => $servidor) {
                if(count($limpaServidores) != ($key + 1)) {
                    unset($readNew["servidor_" . ($key + 1)]);
                    unset($readNew["porta_" . ($key + 1)]);
                }
            }

            $readNew['servidores'] = $read;

            $servidores = explode("|", $readNew['servidores']);

            foreach ($servidores as $key => $servidor) {
                if(count($servidores) != ($key + 1)) {
                    $server_port = explode(":", $servidor);
                    $readNew["servidor_" . ($key + 1)] = $server_port[0];
                    $readNew["porta_" . ($key + 1)] = $server_port[1];
                }
            }

            write_ini_file($readNew, _TMP_, false);

            echo "1*" . $readNew['servidores'];

            break;

        default:
            die();

    }
else:

    echo "err";

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