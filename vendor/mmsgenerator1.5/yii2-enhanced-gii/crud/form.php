 <?php
/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $generator yii\gii\generators\crud\Generator */

app\assets\MMSCrudAsset::register($this);
use yii\helpers\Html;
?>
<style>
.default-view h1{
    margin-top: 0px;
}
</style>
<script>
$(document).ready(function(){

$("#generate-result").on('click','#check-all', function(){

	$('.answers').click()

});
$(".done").click(function(){
	event.preventDefault();
	var lastLi = $(this).closest('li');
	var this_li_ind = $(this).closest('li').index();
	if($('.payment-wizard li').hasClass("jump-here")){
		lastLi.removeClass("active").addClass("completed");
		$(this).closest(".wizard-content").slideUp();
		$('.payment-wizard li.jump-here').removeClass("jump-here");
	}else{
		lastLi.removeClass("active").addClass("completed");
		$(this).closest(".wizard-content").slideUp();
		lastLi.next("li:not('.completed')").addClass('active').children('.wizard-content').slideDown();
	}
});

$('.payment-wizard li .wizard-heading').click(function(){
	var lastLi = $(this).closest('li');
	if(lastLi.hasClass('completed')){
		var this_li_ind = lastLi.index();
		var li_ind = $('.payment-wizard li.active').index();
		if(this_li_ind < li_ind){
			$('.payment-wizard li.active').addClass("jump-here");
		}
		lastLi.addClass('active').removeClass('completed');
		$(this).siblings('.wizard-content').slideDown();
	}
});

})
</script>
<div class="container generator">

    	<div class="header-crud">
    		<div class="title">
            	<h1><?= Html::encode($generator->getName()) ?></h1>

                <p><?= $generator->description ?></p>
            </div>
        </div>

	<ul class="payment-wizard">
    	<li class="active">
        	<div class="wizard-heading">
            	1. Informações Básicas
                <span class="icon-user"></span>
            </div>
            <div id="finish-step" class="wizard-content">



            <div class="row row-crud">
             <?php
                        echo $form->errorSummary($generator,['header'=>'<b>Por favor corrija os seguintes erros:</b>']);
                        
                        echo $form->field($generator, 'module')
                             ->dropDownList($generator->getSelectAvailableModules());

                        echo $form->field($generator, 'dhtmlxLayout')
                            ->dropDownList([
                                 'emptyLayout' => 'Basic Layout - Tela de cadastro simples', 
                                 'gridLayout' => 'Grid Layout - Tela de cadastro com filtro'
                        ]);

                        echo $form->field($generator, 'tableName');

                        echo $form->field($generator, 'filedPrimaryKey')
                            ->dropDownList(['' => 'Selecione...']);
      
                        echo $form->field($generator, 'modelClass');
                        echo $form->field($generator, 'controllerClass');
      
                        echo $form->field($generator, 'oneTable')->checkbox();
                        echo $form->field($generator, 'exportExcel')->checkbox();
                        echo $form->field($generator, 'exportPdf')->checkbox();
                        echo $form->field($generator, 'enableI18N')->checkbox();
                        echo $form->field($generator, 'messageCategory');
                     ?>

	            <button class="btn-green done" type="submit">Continuar</button>

        	</div>
        </div>

        </li>
        <li>
        	<div class="wizard-heading">
            	2. Criação da Consulta
                <span class="icon-location"></span>
            </div>
            <div class="wizard-content">
	            <div class="panel panel-primary">
				    <div class="panel-heading">Relações e Colunas do Select:</div>
				    <div class="panel-body">
				         <div id="relacoes"></div>
			        </div>
			  	</div>

			  	 <div id="clauseWhere" class="panel panel-primary">
				    <div class="panel-heading">Clausula Where:</div>
				    <div class="panel-body">
				          <div id="conditions"></div>
			        </div>
			  	</div>

		  	  	<div id="query-genenerator" class="panel panel-primary">
				    <div class="panel-heading">Consulta:</div>
				    <div class="panel-body">
				    	  <div class="btn-query-generate">
				    		<button id="btn-qg" type="button" class="btn btn-xs btn-success dim"><i class="glyphicon glyphicon-play"></i> GERAR QUERY:</button>
				          </div>
				          <div class="bs-callout bs-callout-primary">
							  <h4>Query Gerada:</h4>
							 <div class="row">
							 	 <div class="row margin">
								   <code class="select "></code>
								 </div>
								 <div class="row margin">
								   <code class="from"></code>
								 </div>
								 <div class="row joins"></div>
								 <div class="row margin">
								    <code class="where"></code>
								 </div>
					        </div>
			        	</div>
			        </div>
			  	</div>


            	<button class="btn-green done" type="submit">Continuar</button>
            </div>
        </li>
        <li>
        	<div class="wizard-heading">
            	3. Configuração do Formulário
                <span class="icon-summary"></span>
            </div>
            <div class="wizard-content">


    		 	<div id="fieldsConfigCrud" class="panel panel-primary">
				    <div class="panel-heading">Formulário CRUD:</div>
				    <div class="panel-body">
			    	   <div class="btn-query-generate">
			    		<button id="btn-refresh-grid-form-crud" type="button" class="btn btn-xs btn-success dim">
			    			<i class="glyphicon glyphicon-play"></i> ATUALIZAR:
			    		</button>
			           </div>
				        <div id="grid-form-crud" style="width:900px;height:350px"></div>
			        </div>
			  	</div>

		  		<div id="fieldsConfigFilter" class="panel panel-primary">
				    <div class="panel-heading">Formulário Filtro:</div>
				    <div class="panel-body">
			    	   <div class="btn-query-generate">
			    		<button id="btn-refresh-grid-form-filter" type="button" class="btn btn-xs btn-success dim">
			    			<i class="glyphicon glyphicon-play"></i> ATUALIZAR:
			    		</button>
			           </div>
				        <div id="grid-form-filter" style="width:900px;height:350px"></div>
			        </div>
			  	</div>


		  		<div id="fieldsConfig" class="panel panel-primary">
				    <div class="panel-heading">Formulário Customizado:</div>
				    <div class="panel-body">
                    	<IFRAME src="http://dhtmlx.com/docs/products/visualDesigner/live3/" width="1113" height="548" scrolling="no" frameborder="0" align="center"></IFRAME>
			        </div>
			  	</div>

			  	 <div id="generate-result"></div>
            	 <div class="form-group action-buttom">
	                <?= Html::submitButton('Preview', ['id'=> 'preview-generate','name' => 'preview', 'class' => 'btn-blue']) ?>
	            	<button style="display:none;" id="generatebtn" class="btn-green" type="submit">Gerar</button>

	                <?php if (isset($files)): ?>
	                    <?= Html::submitButton('Generate', ['name' => 'generate', 'class' => 'btn-green']) ?>
	                <?php endif; ?>
         	    </div>

<!--             	<button class="btn-green done" type="submit">Continuar</button> -->
            </div>
        </li>
<!--         <li> -->
<!--         	<div class="wizard-heading"> -->
<!--             	4. Finalização -->
<!--                 <span class="icon-mode"></span> -->
<!--             </div> -->
<!--             <div class="wizard-content"> -->
<!--         		 <div id="generate-result"></div> -->
<!--             	 <div class="form-group action-buttom"> -->
	                <?//= Html::submitButton('Preview', ['id'=> 'preview-generate','name' => 'preview', 'class' => 'btn-blue']) ?>
<!-- 	            	<button class="btn-green" type="submit">Gerar</button> -->

	                <?// if (isset($files)): ?>
	                    <?//= Html::submitButton('Generate', ['name' => 'generate', 'class' => 'btn-green']) ?>
	                <?// endif; ?>
<!--          	     </div>	 -->
<!--             </div> -->
<!--         </li> -->
    </ul>
</div>
