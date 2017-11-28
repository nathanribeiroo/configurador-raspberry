<?php
define("_TMP_", "/tmp/config.tmp");
@unlink(_TMP_);
copy("config.conf", _TMP_);
$read = parse_ini_file(_TMP_);

function haveId(){
 global $read;
    if(!empty($read['id_loja'])){
        return true;
    }
    return false;
}

if(haveId()) {

    require_once "banco.php";

    $select = new DataBase();

    $checkbox = json_encode($select->find("stores", "exception", ['id =' => $read['id_loja']]));

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
    <link href="css/bootstrap-toggle.css" rel="stylesheet">

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
                <li class="active"><a href="cameras.php">Câmeras</a></li>
                <li><a href="servidor.php">Servidor</a></li>
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
                    <div class="panel-heading">Configuração de Alertas</div>
                    <div class="panel-body">
                        <div id="row">
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label>Número da Câmera:</label>
                                    <input id="num_cam" class="numOnly" type="text" value="" name="num_cam">
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label>Número da Zona:</label>
                                    <input id="num_sensor" class="numOnly" type="text" value="" name="num_sensor">
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
                    <div class="panel-heading">Câmeras Registradas</div>
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
                    Para configurar os alertas, é necessário cadastrar a loja primeiro!
                </div>
            </div>
            <?php endif; ?>
        </div>
</div>

<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jquery.bootstrap-touchspin.min.js"></script>
<script src="js/bootstrap-notify.js"></script>
<script src="js/bootstrap-toggle.js"></script>

<script>

    $( document ).ready(function() {

        var camera = 0;
        var sensor = 0;
        var read = "<?php echo $read['cameras']; ?>";
        var separator = "_";
        var local = "/configurador-raspberry";
        var checkbox = "";
        var insert = "";
        var first = true;

        $("input[name='num_cam']").TouchSpin({
            verticalbuttons: true,
            min: 1,
            initval: 1,
            max: 32
        });

        $("input[name='num_sensor']").TouchSpin({
            verticalbuttons: true,
            min: 1,
            initval: 1,
            max: 8
        });

        $(".numOnly").on("keypress", function(e){
            if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                return false;
            } else {

            }
        });

        $(".numOnly").on("paste", function(){
            return false;
        });

        $("#add").on("click", function(){
            $("#add").html("Carregando...");
            camera = $("#num_cam").val();
            sensor = $("#num_sensor").val();

            $.ajax({
                method: "POST",
                url: "http://" + window.location.hostname + local + "/add.php",
                dataType: "html",
                data: {info1: camera, info2: sensor, read: read, config: "cameras"},
                success: function (data) {
                    data = data.split("*");

                    if(data[0] == "1"){
                        read = data[1];
                        createTable();
                        $.notify({icon: 'glyphicon glyphicon-ok',message: "<b>Configuração de alertas adicionado com sucesso!</b>"},{type: "success"});
                        $("#aplicar").show();

                        window.onbeforeunload = function() {
                            return "qqqqqq?";
                        };

                    } else if(data[0] == "-1"){
                        $.notify({
                            icon: 'glyphicon glyphicon-remove',
                            message: "<b>" + data[1] + "</b>"
                        },{type: "danger"});
                    }

                    $("#add").html("Adicionar");

                },
                error: function (err) {
                    $("#add").html("Adicionar");
                }
            });
        });

        var createTable = function () {
            var div;
            var html = '<div class="table-responsive"><table class="table table-bordered"><thead>' +
                '<tr><th>#</th><th>Câmera</th><th>Zona</th><th style="width: 130px; text-align: center;" >Alarme Exceção</th><th class="text-center" style="width: 10px;">Desativar</th></tr></thead><tbody>';

            if(read != "") {
                $.each(read.split("|"), function (key, val) {

                    if (val != "") {
                        div = val.split(separator);

                        html += "<tr>";
                        html += "<th class='row'>" + (key + 1) + "</th>";
                        html += "<td class='cam'>" + div[0] + "</td>";
                        html += "<td class='sen'>" + div[1] + "</td>";
                        html += "<td>" + '<input class="bbc" id="exc-' + (key + 1) + '" data-key="'+(div[0] + "_" + div[1])+'" type="checkbox" >' + "</td>";
                        html += "<td><button class='btn btn-danger btn-xs del'>Deletar</td>";
                        html += "</tr>";
                    }
                });

                html += "</tbody></table></div>";

                $(".tableLoad").html(html);
                insertException();
                loadCheckbox();
                loadDel();
            } else {
                $(".tableLoad").html('<div class="alert alert-danger text-center" role="alert"><b>Nenhum dado registrado!</b></div>');
            }
        };

        var insertException = function(){
            $(".bbc").click(function(){
                insert = clickCheckbox();
                $("#aplicar").show();
                window.onbeforeunload = function () {
                    return "qqqqqq?";
                };
            });
        };

        var clickCheckbox = function(){
            insert = "";
            $(".bbc").each(function(index, element){
                if($(element).is(":checked")) {
                    var $row = $(element).closest("tr");    // Find the row
                    var $cam = $row.find(".cam").text(); // Find the text
                    var $sen = $row.find(".sen").text();
                    insert += "|" + $cam + "_" + $sen + "|";
                }
            });

            return insert;


        };

        var loadCheckbox = function(){
            checkbox = JSON.parse('<?= json_encode($select->find("stores", "exception", ['id =' => $read['id_loja']])); ?>');
            if(first){
                insert = checkbox[0].exception;
                console.log(insert);
                first = false;
            }

            if(checkbox[0].exception != null && checkbox[0].exception != ""){
                var checked = arrChecked();
                $(".bbc").each(function(index, element) {
                    var key = $(element).data("key");
                    if($.inArray(key.toString(), checked) != -1){
                        $(element).attr('checked', true);
                    }
                });
            }
        };

        var arrChecked = function () {
            var ids = [];
            $.each(insert.split("|"),function(index, obj){
                ids.push(obj);
            });
            ids.pop();
            return ids;
        };

        var loadDel = function () {
            $(".del").on("click", function () {
                var $row = $(this).closest("tr");    // Find the row
                var $key = $row.find(".row").text(); // Find the key
                var $cam = $row.find(".cam").text(); // Find the text
                var $sen = $row.find(".sen").text(); // Find the text

                if(!$("#exc-" + $key).is(":checked")) {
                    $.ajax({
                        method: "POST",
                        url: "http://" + window.location.hostname + local + "/del.php",
                        dataType: "html",
                        data: {val1: $cam, val2: $sen, read: read, config: "cameras"},
                        success: function (data) {
                            data = data.split("*");

                            if (data[0] == "1") {
                                read = data[1];
                                createTable();
                                read == "" ? $(".tableLoad").html('<div class="alert alert-danger text-center" role="alert"><b>Nenhum dado registrado!</b></div>') : "";

                                $.notify({
                                    icon: 'glyphicon glyphicon-alert',
                                    message: "<b>Configuração removida com sucesso!</b>"
                                }, {type: "warning"});

                                $("#aplicar").show();

                                window.onbeforeunload = function () {
                                    return "qqqqqq?";
                                };

                            }
                        },
                        error: function (err) {

                        }
                    });
                } else {
                    $.notify({
                        icon: 'glyphicon glyphicon-info',
                        message: "<b>Para deletar, é necessário desativar o alarme exceção.</b>"
                    }, {type: "info"});
                }
            });
        };


        $("#aplicar").on("click", function() {
            if(confirm("Deseja salvar as alterações?")) {
                $.ajax({
                    method: "POST",
                    url: "http://" + window.location.hostname + local + "/add.php",
                    dataType: "html",
                    data: {info1: "u", info2: "u", insert: insert, id: <?= $read["id_loja"] ?>,  config: "save"},
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
        loadCheckbox();

    });



</script>
</body>
</html>
