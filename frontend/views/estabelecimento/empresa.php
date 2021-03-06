<?php
/* @var $this yii\web\View */

$this->title = '';
?>

<script type="text/javascript">

    var ultimoCEP = '',
            salvo = '<?= $salvo ?>',
            FormEmpresa = {},
            estabelecimento = JSON.parse('<?= json_encode($estabelecimento) ?>'),
            categorias = JSON.parse('<?= json_encode($categorias) ?>'),
            limitFotos = JSON.parse('<?= json_encode($limitFotos) ?>'),
            formaPagamento = JSON.parse('<?= json_encode($formaPagamento) ?>');

    function buscaCEP(v) {
        v = v.replace('X', '');
        if (v.length === 9 && ultimoCEP != v) {
            ultimoCEP = v;
            Util.getEnderecoByCEP(v, preencheEndereco);
        }
    }

    function preencheEndereco(data) {
        if (data.erro) {
            $.smallBox({
                title: "Busca de CEP",
                content: "<i class='fa fa-clock-o'></i> <i>O CEP digitado não foi encontrado, verifique o CEP ou preencha os dados do endereço...</i>",
                color: "#C46A69",
                iconSmall: "fa fa-times fa-2x fadeInRight animated",
                timeout: 8000
            });
            FormEmpresa.setFormData({
                CB04_END_LOGRADOURO: '',
                CB04_END_BAIRRO: '',
                CB04_END_CIDADE: '',
                CB04_END_UF: '',
                CB04_END_COMPLEMENTO: ''
            });

        } else {
            FormEmpresa.setFormData({
                CB04_END_LOGRADOURO: data.logradouro,
                CB04_END_BAIRRO: data.bairro,
                CB04_END_CIDADE: data.localidade,
                CB04_END_UF: data.uf,
                CB04_END_COMPLEMENTO: data.complemento
            });
        }
    }

    function loadGaleria() {
        var loadImgens = function (retorno) {
            fotos = JSON.parse(retorno.message);
            objFotos = [];  
            for (var i in fotos) {
                objFotos.push({
                    imgUrl: fotos[i].TEXTO,
                    imgDelete: 'excluirImg(' + fotos[i].ID + ')'
                });
            }
            Util.galeria('galeria', objFotos);
        }
        Util.ajaxGet('index.php?r=estabelecimento/global-crud', {action: 'fotoEmpresa', param: 'read'}, loadImgens);
    }
    
    function excluirImg(id) {
        Util.ajaxGet('index.php?r=estabelecimento/global-crud', {action: 'fotoEmpresa', param: 'delete', foto: id}, loadGaleria);
    }

    document.addEventListener("DOMContentLoaded", function (event) {

        function fix_height() {
            var h = $("#tray").height();
            $("#preview").attr("height", (($(window).height()) - h) + "px");
        }
        $(window).resize(function () {
            fix_height();
        }).resize();

        // obj form
        FormEmpresa = new Form('empresa-form');

        // add opcoes no select
        FormEmpresa.addOptionsSelect('CB04_CATEGORIA_ID', categorias);

        // cria checkbox com as formas de pagamento
        FormEmpresa.addCheckboxInLine("forma-pagamento", "FORMA-PAGAMENTO", formaPagamento);

        // Preenche o form com os dados da empresa
        FormEmpresa.setFormData(estabelecimento);

        // logomarca
        if (estabelecimento.CB04_URL_LOGOMARCA) {
            $('img#logo-empresa').attr('src', estabelecimento.CB04_URL_LOGOMARCA);
        }

        $("#btn-reset").click(function (e) {
            FormEmpresa.setFormData(estabelecimento);
        });

        $("#btn-salvar").click(function (e) {
            FormEmpresa.form.submit();
        });
        
        Util.dropZone('dropzone', {
            urlSave: "index.php?r=estabelecimento/global-crud&action=fotoEmpresa&param=save",
//            maxFiles: limitFotos,
            message: "Enviar fotos",
        }, loadGaleria);

        loadGaleria();
        
        $('#limitFotos').html("Permitido o envio de até <strong>" + limitFotos + "</strong> fotos.");

        pageSetUp();

        var pagefunction = function () {

            if (salvo) {
                $.smallBox({
                    title: "Dados atualizados",
                    //content: "<i class='fa fa-clock-o'></i> <i></i>",
                    color: "#739e73",
                    iconSmall: "fa fa-check-circle fadeInRight animated",
                    timeout: 4000
                });
            }

            var $empresaForm = FormEmpresa.form.validate({
                rules: {
                    CB04_NOME: {
                        required: true
                    },
                    CB04_CATEGORIA_ID: {
                        required: true
                    },
                    CB04_END_CEP: {
                        required: true
                    },
                    CB04_END_LOGRADOURO: {
                        required: true
                    },
                    CB04_END_BAIRRO: {
                        required: true
                    },
                    CB04_END_CIDADE: {
                        required: true
                    },
                    CB04_END_UF: {
                        required: true
                    }
                },
                messages: {
                    CB04_NOME: {
                        required: 'Campo obrigatório'
                    },
                    CB04_CATEGORIA_ID: {
                        required: 'Campo obrigatório'
                    },
                    CB04_END_CEP: {
                        required: 'Campo obrigatório'
                    },
                    CB04_END_LOGRADOURO: {
                        required: 'Campo obrigatório'
                    },
                    CB04_END_BAIRRO: {
                        required: 'Campo obrigatório'
                    },
                    CB04_END_CIDADE: {
                        required: 'Campo obrigatório'
                    },
                    CB04_END_UF: {
                        required: 'Campo obrigatório'
                    }
                },
                errorPlacement: function (error, element) {
                    error.insertAfter(element.parent());
                }
            });
        };

        // Load form valisation dependency 
        loadScript("js/plugin/jquery-form/jquery-form.min.js", pagefunction);
        
    });
    
</script>

<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <h1 class="page-title txt-color-blueDark">
            <i class="fa-fw fa fa-pencil-square-o"></i> 
            Empresa <span>&gt; edição</span>
        </h1>
    </div>
</div>

<div class="row">
    <article class="col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable">

        <div role="content">

            <div class="widget-body no-padding">

                <form action="#" id="empresa-form" class="smart-form" novalidate="novalidate" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="_csrf" value="<?= Yii::$app->request->getCsrfToken() ?>" />
                    <fieldset>
                        <h3>Sobre a empresa</h3>
                        <img src="img/sem_imagem.jpg" id="logo-empresa" class="thumbnail img-thumbnail air air-top-right" style="max-height: 120px;margin-right: 15px;" />
                        <div class="row padding-top-15">
                            <section class="col col-9"><?= $al['CB04_URL_LOGOMARCA'] ?>
                                <label class="input"> <i class="icon-prepend fa fa-image"></i>
                                    <input type="file" name="CB04_URL_LOGOMARCA" placeholder="">
                                </label>
                            </section>
                        </div>
                        <div class="row">
                            <section class="col col-6"><?= $al['CB04_NOME'] ?>
                                <label class="input"> <i class="icon-prepend fa fa-suitcase"></i>
                                    <input type="text" name="CB04_NOME" placeholder="">
                                </label>
                            </section>
                            <section class="col col-6"><?= $al['CB04_CATEGORIA_ID'] ?>
                                <label class="select">
                                    <select name="CB04_CATEGORIA_ID" disabled="">
                                        <option value="" selected="" disabled="">Categoria...</option>
                                    </select> <i></i> 
                                </label>
                            </section>
                        </div>
                        <div class="row">
                            <section class="col col-6"><?= $al['CB04_FUNCIONAMENTO'] ?>
                                <label class="textarea"> <i class="icon-prepend fa fa-suitcase"></i>
                                    <textarea rows="4" name="CB04_FUNCIONAMENTO" placeholder=""></textarea> 
                                </label>
                            </section>
                            <section class="col col-6"><?= $al['CB04_OBSERVACAO'] ?>
                                <label class="textarea"> <i class="icon-prepend fa fa-info-circle"></i>
                                    <textarea rows="4" name="CB04_OBSERVACAO" placeholder=""></textarea> 
                                </label>
                            </section>
                        </div>
                    </fieldset>

                    <fieldset>
                        <h3>Formas de pagamento</h3>
                        <section id="forma-pagamento" class="padding-top-15"></section>
                    </fieldset>

                    <fieldset>
                        <h3>Endereço</h3>
                        <div class="row padding-top-15">
                            <section class="col col-2"><?= $al['CB04_END_CEP'] ?>
                                <label class="input">
                                    <input type="text" name="CB04_END_CEP" placeholder="" data-mask="99999-999" onkeyup="buscaCEP(this.value)">
                                </label>
                            </section>
                            <section class="col col-8"><?= $al['CB04_END_LOGRADOURO'] ?>
                                <label class="input">
                                    <input type="text" name="CB04_END_LOGRADOURO" placeholder="">
                                </label>
                            </section>
                            <section class="col col-2"><?= $al['CB04_END_NUMERO'] ?>
                                <label class="input">
                                    <input type="text" name="CB04_END_NUMERO" placeholder="">
                                </label>
                            </section>
                        </div>
                        <div class="row">
                            <section class="col col-5"><?= $al['CB04_END_BAIRRO'] ?>
                                <label class="input">
                                    <input type="text" name="CB04_END_BAIRRO" placeholder="">
                                </label>
                            </section>
                            <section class="col col-5"><?= $al['CB04_END_CIDADE'] ?>
                                <label class="input">
                                    <input type="text" name="CB04_END_CIDADE" placeholder="">
                                </label>
                            </section>
                            <section class="col col-2"><?= $al['CB04_END_UF'] ?>
                                <label class="input">
                                    <input type="text" name="CB04_END_UF" placeholder="">
                                </label>
                            </section>
                        </div>
                        <section>
                            <label class="input"><?= $al['CB04_END_COMPLEMENTO'] ?>
                                <input type="text" name="CB04_END_COMPLEMENTO" placeholder="">
                            </label>
                        </section>
                    </fieldset>

                    <fieldset>
                        <h3>Fotos</h3>
                        <div class="row no-margin padding-top-15">
                            <div id="dropzone"></div>
                        </div>
                        <div id="galeria"></div>
                        <small id="limitFotos"></small>
                    </fieldset>

                    <footer>
                        <button id="btn-salvar" type="button" class="btn btn-primary">
                            Salvar
                        </button>
                        <button id="btn-reset" type="button" class="btn btn-default">
                            Restaurar informações
                        </button>
                    </footer>

                </form>

            </div>

        </div>

    </article>
</div>