<?php
define("_TMP_", "/tmp/config.tmp");
@unlink(_TMP_);
copy("config.conf", _TMP_);
$read = parseServer(parse_ini_file(_TMP_));
$read = parse_ini_file(_TMP_);


function parseServer($read){
   $read["servidores"] = findArray($read, "servidor_", "porta_");
   write_ini_file($read, "/tmp/config.conf");
   return $read;
}

function findArray($read, $str1, $str2){
   $filtred = "";
   foreach($read as $key => $value) {
       if (preg_match("/$str1\d/", $key))
           $filtred .= $value;
       if(preg_match("/$str2\d/",$key))
           $filtred .= ":" . $value . "|";
   }
   return $filtred;
}

function haveId(){
    global $read;
    if(!empty($read['id_loja'])){
        return true;
    }
    return false;
}

function write_ini_file($assoc_arr, $path) {
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

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Configuração Sensores</title>

    <link href="css/bootstrap.css" rel="stylesheet">
    <link href="css/index.css" rel="stylesheet">
    <link href="css/jquery.bootstrap-touchspin.min.css" rel="stylesheet">

</head>

<body>

<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="index.php">CMC</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <li><a href="cameras.php">Câmeras</a></li>
                <li class="active"><a href="servidor.php">Servidor</a></li>
            </ul>
        </div><!--/.nav-collapse -->
    </div>
</nav>

<div class="container">
    <hr />
    <div id="row">
        <?php if(haveId()): ?>
        <div class="col-lg-12">
            <div class="panel panel-black">
                <div class="panel-heading">Configuração de Servidores</div>
                <div class="panel-body">
                    <div id="row">
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <div class="form-group">
                                <label>Servidor:</label>
                                <input id="num_ip" class="form-control" type="text" value="" name="num_ip">
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <div class="form-group">
                                <label>Porta de acesso:</label>
                                <input id="num_port" class="form-control numOnly" type="text" value="" name="num_port">
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <button type="button" id="add" class="btn btn-black pull-right">Adicionar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="panel panel-black">
                <div class="panel-heading">Servidores Registrados</div>
                <div class="panel-body tableLoad">

                </div>
            </div>
        </div>

        <div class="col-lg-12 text-center">
            <hr />
            <button type="button" id="aplicar" style="display: none;" class="btn btn-black">Aplicar Alterações</button>
        </div>
        <?php else: ?>
            <div class="col-lg-12 text-center">
                <div class="alert alert-danger" role="alert">
                    Para configurar os servidres, é necessário cadastrar a loja primeiro!
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jquery.bootstrap-touchspin.min.js"></script>
<script src="js/jquery.mask.js"></script>
<script src="js/bootstrap-notify.js"></script>

<script>

    $( document ).ready(function() {

        var servidor = 0;
        var porta = 0;
        var read = "<?php echo $read['servidores']; ?>";
        var separator = ":";
        var local = "/configurador-raspberry";

        $('#num_ip').mask("http://AAA", {
            placeholder: "http://",
            translation: {
                'A': {pattern: /[a-zA-Z0-9-./]/, recursive: true}
            }
        });

        $("input[name='num_port']").TouchSpin({
            verticalbuttons: true,
            min: 1,
            initval: 80,
            max: 65535
        });

        $("#add").on("click", function(){

            var qntRead = read.split("|");

            if(qntRead.length <= 2) {

                servidor = $("#num_ip").val().replace('http://', '');
                porta = $("#num_port").val();
                $("#add").html("Carregando...");
                $.ajax({
                    method: "POST",
                    url: "http://" + window.location.hostname + local + "/add.php",
                    dataType: "html",
                    data: {info1: servidor, info2: porta, read: read, config: "servidores"},
                    success: function (data) {
                        data = data.split("*");

                        if (data[0] == "1") {
                            read = data[1];
                            createTable();
                            $.notify({
                                icon: 'glyphicon glyphicon-ok',
                                message: "<b>Configuração adicionada com sucesso!</b>"
                            }, {type: "success"});
                            $("#aplicar").show();

                            window.onbeforeunload = function() {
                                return "qqqqqq?";
                            };

                        } else if (data[0] == "-1") {
                            $.notify({
                                icon: 'glyphicon glyphicon-remove',
                                message: "<b>" + data[1] + "</b>"
                            }, {type: "danger"});
                        }

                        $("#add").html("Adicionar");


                    },
                    error: function (err) {

                        $("#add").html("Adicionar");
                    }
                });
            } else {
                $.notify({
                    icon: 'glyphicon glyphicon-alert',
                    message: "<b>É possível cadastrar no máximo dois servidores!</b>"
                }, {type: "danger"});
            }
        });

        var createTable = function () {
            var div;
            var html = '<div class="table-responsive"><table class="table table-bordered"><thead>' +
                '<tr><th>#</th><th>Servidor</th><th>Porta</th><th style="width: 10px;">Opção</th></tr></thead><tbody>';

            if(read != "") {
                $.each(read.split("|"), function (key, val) {

                    if (val != "") {
                        div = val.split(separator);

                        html += "<tr>";
                        html += "<th class='row'>" + (key + 1) + "</th>";
                        html += "<td class='cam'>" + div[0] + "</td>";
                        html += "<td class='sen'>" + div[1] + "</td>";
                        html += "<td><button class='btn btn-danger btn-xs del'>Deletar</td>";
                        html += "</tr>";
                    }
                });

                html += "</tbody></table></div>";

                $(".tableLoad").html(html);

                loadDel();
            } else {
                $(".tableLoad").html('<div class="alert alert-danger text-center" role="alert"><b>Nenhum dado registrado!</b></div>');
            }
        };


        var loadDel = function () {
            $(".del").on("click", function () {
                var $row = $(this).closest("tr");    // Find the row
                var $server = $row.find(".cam").text(); // Find the text
                var $port = $row.find(".sen").text(); // Find the text

                $.ajax({
                    method: "POST",
                    url: "http://" + window.location.hostname + local + "/del.php",
                    dataType: "html",
                    data: {val1: $server, val2: $port, read: read, config: "servidores"},
                    success: function (data) {
                        data = data.split("*");

                        if (data[0] == "1") {
                            read = data[1];
                            createTable();
                            read == "" ? $(".tableLoad").html('<div class="alert alert-danger text-center" role="alert"><b>Nenhum dado registrado!</b></div>') : "";
                            $.notify({
                                icon: 'glyphicon glyphicon-alert',
                                message: "<b>Configuração removida com sucesso!</b>"
                            },{type: "warning"});

                            $("#aplicar").show();

                            window.onbeforeunload = function() {
                                return "qqqqqq?";
                            };
                        }

                    },
                    error: function (err) {

                    }
                });
            });
        };



        $("#aplicar").on("click", function() {
            if(confirm("Deseja salvar as alterações?")) {
                $.ajax({
                    method: "POST",
                    url: "http://" + window.location.hostname + local + "/add.php",
                    dataType: "html",
                    data: {info1: "u", info2: "u", config: "save"},
                    success: function (data) {
                        $.notify({
                            icon: 'glyphicon glyphicon-ok',
                            message: "<b>Alterações realizadas com sucesso!</b>"
                        }, {type: "success"});
                        window.onbeforeunload = null;
                        window.location.reload();
                    },
                    error: function (err) {

                    }
                });
            }
        });

        createTable();


    });



</script>
</body>
</html>
