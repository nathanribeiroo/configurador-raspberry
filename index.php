<?php

//$read = parse_ini_file("config.conf");

//function
//if(!empty($read['id_loja']))

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
    <link href="css/bootstrap-clockpicker.css" rel="stylesheet">
    <link href="css/bootstrap-tagsinput.css" rel="stylesheet">


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
            <a class="navbar-brand" href="index.php" style="color: #ffffff">CMC</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <li><a href="cameras.php">Câmeras</a></li>
                <li><a href="servidor.php">Servidor</a></li>
            </ul>
        </div><!--/.nav-collapse -->
    </div>
</nav>

<div class="container">
    <div class="page-header">
        <h1>Configuração da Loja</h1>
    </div>
    <div class="bs-example">
        <form method="post" class="form-horizontal" action="insert.php">
            <div class="form-group">
                <div class="col-md-12">
                    <input type="text" class="form-control" id="inputLoja" name="loja" placeholder="Nome da Loja">
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-12">
                 <input type="text" class="form-control" id="inputApelido" name="apelido" placeholder="Apelido">
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-12">
                    <input type="text" class="form-control" id="inputTel" name="telefone" placeholder="Telefone Loja">
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-12">
                    <select class="form-control" id="inputGrupo" name="grupo">
                        <option selected disabled>Grupo</option>
                        <option value="Drogasil">Drogasil</option>
                        <option value="Drogaraia">Drogaraia</option>
                    </select>
                </div>
            </div>
<!--            <div class="form-group">-->
<!--                <div class="col-md-12">-->
<!--                    <input type="text" class="form-control" id="inputAlarmeExcecao" name="alarmeExcecao" placeholder="Alarme Exceção" data-role="tagsinput">-->
<!--                </div>-->
<!--            </div>-->
            <div class="form-group">
                <div class="col-md-12">
                    <input type="text" class="form-control" id="inputHorarioEntrada" name="horarioEntrada" placeholder="Horário de Entrada">
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-12">
                    <input type="text" class="form-control" id="inputHorarioSaida" name="horarioSaida" placeholder="Horário de Saída">
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-12">
                    <input type="text" class="form-control" id="inputResponsavel" name="responsavel" placeholder="Responsável">
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-12">
                    <input type="text" class="form-control" id="inputTelResponsavel" name="telefoneResponsavel" placeholder="Telefone Responsável">
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-12">
                    <input type="text" class="form-control" id="inputCep" name="cep" placeholder="Cep">
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-12">
                    <input type="text" class="form-control" id="inputEndereco" name="endereco" placeholder="Endereço">
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-12">
                    <input type="text" class="form-control" id="inputNumero" name="numero" placeholder="Número">
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-12">
                    <input type="text" class="form-control" id="inputBairro" name="bairro" placeholder="Bairro">
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-12">
                    <input type="text" class="form-control" id="inputCidade" name="cidade" placeholder="Cidade">
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-12">
                    <input type="text" class="form-control" id="inputUf" name="uf" placeholder="UF">
                </div>
            </div>
<!--            <div class="col-md-12">-->
                <button type="submit" class="btn btn-primary btn-send">Salvar</button>
<!--            </div>-->
            </div>
        </form>
    </div>
</div>

<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jquery.bootstrap-touchspin.min.js"></script>
<script src="js/jquery.mask.js"></script>
<script src="js/bootstrap-notify.js"></script>
<script src="js/bootstrap-clockpicker.js"></script>
<script src="js/bootstrap-tagsinput.js"></script>

<script type="text/javascript">

    $(document).ready(function() {

        var loja = $("#inputLoja"),
            apelido = $("#inputApelido"),
            grupo = $("#inputGrupo"),
            tel = $("#inputTel"),
            telResponsavel = $("#inputTelResponsavel"),
            alarmeExcecao = $("#inputAlarmeExcecao"),
            horarioEntrada = $("#inputHorarioEntrada"),
            horarioSaida = $("#inputHorarioSaida"),
            cep = $("#inputCep"),
            endereco = $("#inputEndereco"),
            numero = $("#inputNumero"),
            bairro = $("#inputBairro"),
            cidade = $("#inputCidade"),
            uf = $("#inputUf"),
            btn = $(".btn-send");

        var limpa_logradouro = function(){
            cep.val("");
            endereco.val("");
            numero.val("");
            bairro.val("");
            cidade.val("");
            uf.val("");
        };

        cep.mask('00000-000');
        tel.mask('0000000000');
        telResponsavel.mask('0000000000');

        horarioEntrada.clockpicker({
            placement: 'top',
            align: 'left',
            donetext: 'Concluído'
        });

        horarioSaida.clockpicker({
            placement: 'top',
            align: 'left',
            donetext: 'Concluído'
        });

        cep.blur(function(){

            var valor_cep = $(this).val().replace(/\D/g, '');

            if(valor_cep != ""){

                var valida_cep = /^[0-9]{8}$/;

                if(valida_cep.test(valor_cep)){

                    endereco.val("carregando...");
                    bairro.val("carregando...");
                    cidade.val("carregando...");
                    uf.val("carregando...");

                    var jqXHR = $.getJSON("//viacep.com.br/ws/"+ valor_cep +"/json/?callback=?");

                    jqXHR.done(function(dados) {

                        if (!("erro" in dados)) {
                            endereco.val(dados.logradouro);
                            bairro.val(dados.bairro);
                            cidade.val(dados.localidade);
                            uf.val(dados.uf);

                            numero.focus();
                        }
                        else {
                            limpa_logradouro();
                            $.notify({icon: 'glyphicon glyphicon-alert',message: "<b>Cep não encontrado!</b>"},{type: "warning"});
                        }
                    });

                    jqXHR.fail(function(){
                        limpa_logradouro();
                        endereco.focus();
                    });

                } else {
                    limpa_logradouro();
                    $.notify({icon: 'glyphicon glyphicon-alert',message: "<b>Formato de cep inválido.</b>"},{type: "warning"});
                }
            }
        });

        btn.on("click", function(){
            console.log(alarmeExcecao.val());
        });

        $(window).keydown(function(event) {
            if (event.keyCode == 13) {
                event.preventDefault();
                return false;
            }
        });

    });

</script>
</body>