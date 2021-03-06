<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\assets\MMSAsset;
use yii\helpers\Html;

MMSAsset::register($this);

?>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
	<noscript>
		<meta http-equiv="Refresh" content="1;erroJavascript.php">
	</noscript>

    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<?php $this->endBody() ?>
<script>

	<?php
	/*
	Referência do script "layout toolbar+filtro+grid ":

	Todo o script desenvolvido, deve ser armazenado dentro do objeto SYSTEM.

	Para inicializar a tela utilize o comando:
	* SYSTEM.boot();


	Para acessar os objetos renderizados basta utilizar os seguintes caminhos dentro do objeto:
	Layout:
	* SYSTEM.Layout.outerLayout => para o layout externo onde se renderiza a toolbar e o InnerLayout.
	* SYSTEM.Layout.innerLayout => para o layout interno, onde é renderizado o filtro ( cell id = 'a' ) e o grid ( cell id = 'b' )
	* SYSTEM.Layout.innerLayout.telaCima => para a celula do filtro.
	* SYSTEM.Layout.innerLayout.telaBaixo => para a celula do grid.

	Filtro:
	* SYSTEM.Filtro => para o form renderizado dentro da aba filtro.

	Toolbar:
	* SYSTEM.Toolbar.core => para o objeto DHTMLx da toolbar

	Ferramentas do SYSTEM:

	Layout:
	* SYSTEM.Layout.t1("string") => para mudar o titulo da primeira celula, a do filro.
	* SYSTEM.Layout.t2("string") => para mudar o titulo da segunda celula, a do grid.

	Toolbar:
	* SYSTEM.Toolbar.icones( [icondeId1,iconeid2,...] ) => mostra os icones cujo ids estão na array passada como parâmetro
	A lista de icones disponível está descrita no aquivo dhxtoolbar.xml que se encontra em /libs/layoutMask/dhxtoolbar.xml
	* SYSTEM.Toolbar.titulo('teste') => modifica o titulo da toolbar

	*/
	?>

	var SYSTEM = (function(){

		var cesta = {

		};

		cesta.boot = function(){
			SYSTEM.Layout = loadLayout();
            dhtmlx.image_path = "<?=\Yii::getAlias('@assetsPath');?>/dhtmlx/terrace/imgs/";
			loadGrid();
			loadFiltro();
			SYSTEM.Toolbar  = loadToolbar();
		}

		return cesta;
	})();

	function loadLayout(){
		var outerLayout = new dhtmlXLayoutObject(document.body, "1C");
		var innerLayout = outerLayout.cells("a").attachLayout("2E");

		// setando o titulo do bar de filtro
        innerLayout.cells("a").setText("Filtro<img src='<?=\Yii::getAlias('@assetsPath');?>/layoutMask/imgs/filtro_icon.png' text='Filtro' alt='Filtro'/>");
        innerLayout.setCollapsedText("a", "Filtro<img src='<?=\Yii::getAlias('@assetsPath');?>/layoutMask/imgs/filtro_icon.png' text='Filtro' alt='Filtro'/>");

		outerLayout.cells("a").hideHeader();
		innerLayout.cells("b").hideHeader();
		return{
			innerLayout : innerLayout,
			outerLayout : outerLayout,
			telaCima : innerLayout.cells("a"),
			telaBaixo : innerLayout.cells("b"),
			t1: function(titulo){
				innerLayout.cells("a").setText(titulo);
			},
			t2: function(titulo){
				innerLayout.cells("b").showHeader();
				innerLayout.cells("b").setText(titulo);
				innerLayout.cells("b").hideHeader();
			},
		    t3: function(titulo){
		    	innerLayout.cells("b").showHeader();
		        innerLayout.cells("b").setText(titulo);
		        innerLayout.cells("b").hideArrow();
		    }
		}
	}

	function loadFiltro(){
			SYSTEM.Filtro = SYSTEM.Layout.innerLayout.cells("a").attachForm();
	}

	function loadToolbar(){
		var toolbar = SYSTEM.Layout.outerLayout.cells("a").attachToolbar();
		//Declarando um método apto a ser sobrescrito sob necessidade, para trabalhar com os ícones da toolbar
		toolbar.doWithItem = function(itemId){};
		toolbar.setIconsPath("<?=\Yii::getAlias('@assetsPath');?>/layoutMask/imgs/");
		toolbar.loadXML("<?=\Yii::getAlias('@assetsPath');?>/layoutMask/dhxtoolbar.xml?etc=" + new Date().getTime());
		toolbar.attachEvent("onXLE", function(){
			toolbar.addSpacer("titulo");
			toolbar.forEachItem(function(itemId){
				toolbar.hideItem(itemId);
				//Chamando o método genérico para cada item
				toolbar.doWithItem(itemId);
			});

		});
		return {
			core: toolbar,
			icones: function(iconsIds){
				setTimeout(function(){
					for(var i = 0; iconsIds.length > i ;i++){
						toolbar.showItem(iconsIds[i]);
					}
				}, 1000);
			},
			titulo: function (titulo){
	            setTimeout(function(){
					toolbar.showItem('titulo');
					toolbar.setItemText('titulo', titulo);
				}, 1000);
			}
		}
	}

	function loadGrid(){
		SYSTEM.Grid = SYSTEM.Layout.innerLayout.cells("b").attachGrid();
		SYSTEM.Grid.enableRowsHover(true,'hover');
	}

</script>
<?= $content ?>
</body>
</html>
<?php $this->endPage() ?>
