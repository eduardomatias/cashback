<?php
namespace mmsgenerator15\enhancedgii\crud;

use Yii;
use yii\db\ActiveRecord;
use yii\db\ColumnSchema;
use yii\db\Schema;
use yii\db\TableSchema;
use yii\gii\CodeFile;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\base\Exception;

/**
 * Generates Relational CRUD
 *
 *
 * @author Yohanes Candrajaya <moo.tensai@gmail.com>
 * @since 2.0
 */
class Generator extends \mmsgenerator15\enhancedgii\BaseGenerator
{
    const TYPE_COMBO = 'combo';
    const TYPE_CALENDAR= 'calendar';
    const TYPE_INPUT_TEXT= 'input';
    const TYPE_AUTOCOMPLETE= 'autocomplete';

    public $nameAttribute = 'name, title, username';
    public $hiddenColumns = 'id, lock';
    public $skippedColumns = 'created_at, updated_at, created_by, updated_by, deleted_at, deleted_by, created, modified, deleted';
    public $nsModel = 'app\models';
    public $nsModules = [];
    public $nsSearchModel = 'app\models';
    public $generateSearchModel = false;
    public $searchModelClass;
    public $generateQuery = false;
    public $queryNs = 'app\models';
    public $queryClass;
    public $queryBaseClass = 'yii\db\ActiveQuery';
    public $generateLabelsFromComments = false;
    public $useTablePrefix = false;
    public $generateRelations = false;
    public $generateMigrations = false;
    public $optimisticLock = '';
    public $createdAt = 'created_at';
    public $updatedAt = 'updated_at';
    public $timestampValue = "new Expression('NOW()')";
    public $createdBy = 'created_by';
    public $updatedBy = 'updated_by';
    public $blameableValue = 'Yii::\$app->user->id';
    public $UUIDColumn = 'id';
    public $deletedBy = 'deleted_by';
    public $deletedAt = 'deleted_at';
    public $nsController = 'app\controllers';
    public $controllerNameUrl;
    public $controllerName;
    public $controllerClass;
    public $pluralize;
    public $loggedUserOnly;
    public $expandable;
    public $cancelable;
    public $saveAsNew;
    public $pdf;
    public $viewPath = '@app/views';
    public $nsView;
    public $baseControllerClass = 'yii\web\Controller';
    public $indexWidgetType = 'grid';
    public $relations;
    public $module;
    public $query;
    public $columnsArray;
    public $controllerUrl;
    public $controllerAction;
    public $pathViewForm;
    public $columnsType = [];
    public $columnsTypeJs = [];
    public $formSettings = [];
    public $oneTable;
    public $exportExcel = true;
    public $exportPdf = true;
    public $hasFormFilter = false;
    public $dhtmlxLayout;
    public $windowName = 'Teste';
    public $filedPrimaryKey = '';
    public $owner = 'MMS';

    const ACTION_COMBO = 'combo';
    const ACTION_AUTOCOMPLETE = 'autocomplete';
    const ACTION_COMBO_ECM21 = 'comboEcm21';

    public function __construct()
    {
        $this->owner  = strtoupper($this->getDbConnection()->getSchema()->db->username);
        
        $this->getModulesNs();
        
        parent::__construct();    
    }
    
    public function getModulesNs()
    {
        if (!count($this->nsModules)) {
            $modules = \Yii::$app->getModules();
            foreach ($modules as $module => $class) {
                 
//                $ctrl = "app\\frontend\\$module\\controllers";
//                $model = "app\\frontend\\$module\\models";
//                $view = "app\\frontend\\$module\\views";
                
                $ctrl = "app\\frontend\\controllers";
                $model = "app\\common\\models";
                $view = "app\\frontend\\views";
                 
                $this->nsModules[$module] = [
                    'ctrl' => $ctrl,
                    'model' => $model,
                    'view' => $view,
                ];
            }
        }
        
    }
    
    public function getSelectAvailableModules()
    {
        $m = array_keys($this->nsModules);
        
        return array_combine($m, $m);
    }
    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'MMS-DHTMLX CRUD GENERATOR 1.5';
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return 'Extensão responsável por gerar implementações CRUD e geração do formulário das telas automaticamente.';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['db', 'nsModel', 'viewPath', 'queryNs', 'nsController', 'nsSearchModel', 'tableName', 'modelClass', 'searchModelClass', 'baseControllerClass'], 'filter', 'filter' => 'trim'],
            //[['filedPrimaryKey',], 'required', 'message' => 'O campo {attribute} não pode ser vazio.'],
            //[['filedPrimaryKey','tableName','modelClass', 'controllerClass','baseControllerClass', 'indexWidgetType', 'db'], 'required', 'message' => 'O campo {attribute} não pode ser vazio.'],
            [['modelClass', 'controllerClass'], 'required', 'message' => 'O campo {attribute} não pode ser vazio.'],
            //[['tableName'], 'match', 'pattern' => '/^(\w+\.)?([\w\*]+)$/', 'message' => 'Caracteres especiais não são permitidos.'],
            //[['tableName'], 'validateTableName'],
//            [['searchModelClass'], 'compare', 'compareAttribute' => 'modelClass', 'operator' => '!==', 'message' => 'Search Model Class must not be equal to Model Class.'],
            [['modelClass'], 'match', 'pattern' => '/^[\w\\\\]*$/', 'message' => 'caracteres especiais não serão permitidos.'],
//            [['modelClass'], 'validateClass', 'params' => ['extends' => BaseActiveRecord::className()]],
            //[['baseControllerClass'], 'validateClass', 'params' => ['extends' => Controller::className()]],
           // [['db'], 'validateDb'],
            [['controllerClass'], 'match', 'pattern' => '/Controller$/', 'message' => 'O nome do controller deve terminar com a palavra "Controller".'],
         //   [['controllerClass'], 'match', 'pattern' => '/(^|\\\\)[A-Z][^\\\\]+Controller$/', 'message' => 'O nome do controller deve iniciar com letra maiúscula.'],
//            [['searchModelClass'], 'validateNewClass'],
         //   [['indexWidgetType'], 'in', 'range' => ['grid', 'list']],
//            [['modelClass'], 'validateModelClass'],
           // [['enableI18N', 'generateRelations', 'generateSearchModel', 'pluralize', 'expandable', 'cancelable', 'pdf', 'loggedUserOnly'], 'boolean'],
             [['enableI18N'], 'boolean'],
           // [['messageCategory'], 'validateMessageCategory', 'skipOnEmpty' => false],
          //  [['viewPath', 'module','skippedRelations', 'skippedColumns',
           //     'controllerClass', 'blameableValue', 'nameAttribute',
            //    'hiddenColumns', 'createdAt', 'updatedAt', 'createdBy', 'updatedBy',
             //   'UUIDColumn', 'saveAsNew'], 'safe'],
              [['filedPrimaryKey','windowName','exportExcel','exportPdf','dhtmlxLayout','oneTable', 'module', 'query', 'columnsArray','controllerUrl', 'pathViewForm', 'columnsType','formSettings', 'columnsTypeJs','owner'], 'safe'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'module' => 'Escolha o módulo',
            'db' => 'Database Connection ID',
            'modelClass' => 'Model Class',
            'generateQuery' => 'Generate ActiveQuery',
            'queryNs' => 'ActiveQuery Namespace',
            'queryClass' => 'ActiveQuery Class',
            'nsModel' => 'Model Namespace',
            'nsSearchModel' => 'Search Model Namespace',
            'UUIDColumn' => 'UUID Column',
            'nsController' => 'Controller Namespace',
            'viewPath' => 'View Path',
            'baseControllerClass' => 'Base Controller Class',
            'indexWidgetType' => 'Widget Used in Index Page',
            'searchModelClass' => 'Search Model Class',
            'expandable' => 'Expandable Index Grid View',
            'cancelable' => 'Add Cancel Button On Form',
            'pdf' => 'PDF Printable View',
            'tableName' => 'Nome da Tabela/View',
            'modelClass' => 'Nome do Modelo',
            'controllerClass' => 'Nome do Controler',
            'enableI18N' => 'Habilitar tradução',
            'oneTable' => 'Consulta do grid com apenas uma tabela?',
            'exportExcel' => 'Exportar o grid em Excel?',
            'exportPdf' => 'Exportar o grid em Pdf?',
            'messageCategory' => 'Categoria tradução',
            'dhtmlxLayout' => 'Escolha o Layout',
            'windowName' => 'Informe o nome da tela.',
            'filedPrimaryKey' => 'Escolha a chave primária da view.',
            'owner' => 'OWNER'

        ]);
    }

    /**
     * @inheritdoc
     */
    public function hints()
    {
        return array_merge(parent::hints(), [
            'enableI18N' => 'Esta opção indica que o gerador gerará strings usando o método <code>Yii::t()</code>.',

            'messageCategory' => 'Esta opção indica a categoria usada por <code>Yii::t()</code>.',

            'skippedColumns' => 'Preencha esse campo com o nome das colunas que você não deseja obter separado por vírgulas ex: campo1,campo2',

            'modelClass' => 'Gentileza informar o nome do modelo no seguinte padrão <code>NomeDoArquivoModel</code>',

            'controllerClass' => 'Gentileza informar o nome do controller no seguinte padrão <code>NomedoarquivoController</code>',

            'oneTable' => 'Ao marcar essa opção ,no próximo passo, você poderá usar somente uma tabela na consula ex:
             <code>SELECT COL FROM TB</code>
            ',

            'windowName' => 'Esse nome aparecerá no cabeçalho da tela. Posteriormente poderá ser alterado na view <code>INDEX</code>',

            'filedPrimaryKey' => 'Use essa opção caso esteja gerando a tela de uma view. Por definição interna, nossas views não tem primary key. Por isso, é obrigatório eleger uma coluna da view escolhida para esse papel.',

        ]);
    }

    /**
     * @inheritdoc
     */
    public function stickyAttributes()
    {
        return array_merge(parent::stickyAttributes(), [
            'db',
            'skippedColumns',
            'hiddenColumns',
            'nameAttribute',
            'nsModel',
            'nsSearchModel',
            'nsController',
            'baseModelClass',
            'queryNs',
            'queryBaseClass',
            'optimisticLock',
            'createdBy',
            'updatedBy',
            'deletedBy',
            'createdAt',
            'timestampValue',
            'updatedAt',
            'deletedAt',
            'blameableValue',
            'UUIDColumn',
            'baseControllerClass',
            'indexWidgetType',
            'viewPath']);
    }

    /**
     * @inheritdoc
     */
    public function requiredTemplates()
    {
        return ['controller.php'];
    }

    /**
     * Returns the message to be displayed when the newly generated code is saved successfully.
     * Child classes may override this method to customize the message.
     * @return string the message to be displayed when the newly generated code is saved successfully.
     */
    public function successMessage($link = '')
    {
        return 'O código foi gerado com sucesso. <a href='.$link.' target="_blank">Clique aqui para acessar o controller</a>';
    }

   public function getTableNameSelect()
   {
      $tables = [];

      $connection = $this->getDbConnection();
        $database = Yii::$app->db->createCommand("SELECT DATABASE()")->queryScalar();

       $command = $connection->createCommand("
          SELECT TABLE_NAME, '- Tabela' as TIPO FROM information_schema.tables WHERE table_schema='$database' and TABLE_TYPE = 'BASE TABLE' 
          UNION
          SELECT TABLE_NAME, '- View' as TIPO FROM information_schema.tables WHERE table_schema='$database' and TABLE_TYPE = 'VIEW' 
       ");

       $reader = $command->query()->readAll();

       foreach ($reader as $k => $value) {
           $tables[$k]['value'] = $value['TABLE_NAME'];
       	   $tables[$k]['text'] = $value['TABLE_NAME'];
       	   $tables[$k]['htmlOptions']['data-subtext'] = $value['TIPO'];
       }

       return $tables;
   }

   public function getColumnNamesByTable($tableName)
   {       
     if (empty($tableName)) {
   		return false;
   	 }
   	  $columns = [];

      $connection = $this->getDbConnection();

       $command = $connection->createCommand("
           	SHOW COLUMNS FROM $tableName
       ");

       $reader = $command->query()->readAll();

       foreach ($reader as $k => $value) {
           
                $columns[$k]['value'] = $value['Type'];
       		$columns[$k]['text'] = $value['Field'];
       		$columns['nativeBuilder'][$k]['id'] = $value['Field'];
       }

       return $columns;
   }

    /**
     * @inheritdoc
     */
    public function generate()
    {
        $files = [];
        $relations = $this->generateRelations();
        $this->relations = $relations;
        $db = $this->getDbConnection();
        $this->nameAttribute = ($this->nameAttribute) ? explode(',', str_replace(' ', '', $this->nameAttribute)) : [$this->nameAttribute];
        $this->hiddenColumns = ($this->hiddenColumns) ? explode(',', str_replace(' ', '', $this->hiddenColumns)) : [$this->hiddenColumns];
        $this->skippedColumns = ($this->skippedColumns) ? explode(',', str_replace(' ', '', $this->skippedColumns)) : [$this->skippedColumns];
        $this->skippedRelations = ($this->skippedRelations) ? explode(',', str_replace(' ', '', $this->skippedRelations)) : [$this->skippedRelations];
        $this->skippedColumns = array_filter($this->skippedColumns);
        $this->skippedRelations = array_filter($this->skippedRelations);
        foreach ($this->getTableNames() as $tableName) {
            // model :
            if (strpos($this->tableName, '*') !== false) {
                $modelClassName = $this->generateClassName($tableName);
                $controllerClassName = $modelClassName . 'Controller';
            } else {
                $modelClassName = (!empty($this->modelClass)) ? $this->modelClass : Inflector::id2camel($tableName, '_');
                $controllerClassName = (!empty($this->controllerClass)) ? $this->controllerClass : $modelClassName . 'Controller';
            }
//            $queryClassName = ($this->generateQuery) ? $this->generateQueryClassName($modelClassName) : false;
            $tableSchema = $db->getTableSchema($tableName);
            $this->modelClass = "{$this->nsModel}\\{$modelClassName}";
            $this->tableSchema = $tableSchema;
//            $this->relations = isset($relations[$tableName]) ? $relations[$tableName] : [];
            $this->controllerClass = $this->nsController . '\\' . $controllerClassName;
            $isTree = !array_diff(self::getTreeColumns(), $tableSchema->columnNames);

            // search model :
            if ($this->generateSearchModel && !$isTree) {
                if (empty($this->searchModelClass) || strpos($this->tableName, '*') !== false) {
                    $searchModelClassName = $modelClassName . 'Search';
                } else {
                    if ($this->nsSearchModel === $this->nsModel && $this->searchModelClass === $modelClassName) {
                        $searchModelClassName = $this->searchModelClass . 'Search';
                    } else {
                        $searchModelClassName = $this->searchModelClass;
                    }
                }
                $this->searchModelClass = $this->nsSearchModel . '\\' . $searchModelClassName;
                $searchModel = Yii::getAlias('@' . str_replace('\\', '/', ltrim($this->searchModelClass, '\\') . '.php'));
                $files[] = new CodeFile($searchModel, $this->render('search.php',
                    ['relations' => isset($relations[$tableName]) ? $relations[$tableName] : []]));
            }

            //controller
            $files[] = new CodeFile(
                Yii::getAlias('@' . str_replace('\\', '/', $this->nsController)) . '/' . $controllerClassName . '.php',
                ($isTree) ?
                    $this->render('controllerNested.php', [
                        'relations' => isset($relations[$tableName]) ? $relations[$tableName] : [],
                    ])
                    :
                    $this->render('controller.php', [
                        'relations' => isset($relations[$tableName]) ? $relations[$tableName] : [],
                    ])
            );

            // views :
            $viewPath = $this->getViewPath();
            $templatePath = $this->getTemplatePath() . '/views';
            foreach (scandir($templatePath) as $file) {
//                if($file === '_formNested.php')
//                    echo  $file;
                if (empty($this->searchModelClass) && $file === '_search.php') {
                    continue;
                }
                if ($file === '_formrefone.php' || $file === '_formrefmany.php' || $file === '_datarefone.php'
                    || $file === '_datarefmany.php' || $file === '_expand.php' || $file === '_data.php') {
                    continue;
                }
                if($this->indexWidgetType != 'list' && $file === '_index.php') {
                    continue;
                }
                if($isTree && ($file === 'index.php' || $file === 'view.php' || $file === '_detail.php' || $file === '_form.php'
                    || $file === '_pdf.php' || $file === 'create.php' || $file === 'saveAsNew.php' || $file === 'update.php'
                    )){
                    continue;
                }
                if(!$isTree && ($file === 'indexNested.php' || $file === '_formNested.php')){
                    continue;
                }
                if (is_file($templatePath . '/' . $file) && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                    $fileName = ($isTree) ? str_replace('Nested','',$file) : $file;
                    $files[] = new CodeFile("$viewPath/$fileName", $this->render("views/$file", [
                        'relations' => isset($relations[$tableName]) ? $relations[$tableName] : [],
                        'isTree' => $isTree
                    ]));
                }
            }
            if ($this->expandable) {
                $files[] = new CodeFile("$viewPath/_expand.php", $this->render("views/_expand.php", [
                    'relations' => isset($relations[$tableName]) ? $relations[$tableName] : [],
                ]));
            }

            if (isset($relations[$tableName]) && !$isTree) {
                if ($this->expandable) {
                    $files[] = new CodeFile("$viewPath/_detail.php", $this->render("views/_detail.php", [
                        'relations' => isset($relations[$tableName]) ? $relations[$tableName] : [],
                    ]));
                }
                foreach ($relations[$tableName] as $name => $rel) {
                    if ($rel[self::REL_IS_MULTIPLE] && isset($rel[self::REL_TABLE]) && !in_array($name, $this->skippedRelations)) {
                        $files[] = new CodeFile("$viewPath/_form{$rel[self::REL_CLASS]}.php", $this->render("views/_formrefmany.php", [
                            'relations' => isset($relations[$tableName]) ? $relations[$tableName][$name] : [],
                        ]));
                        if ($this->expandable) {
                            $files[] = new CodeFile("$viewPath/_data{$rel[self::REL_CLASS]}.php", $this->render("views/_datarefmany.php", [
                                'relName' => $name,
                                'relations' => isset($relations[$tableName]) ? $relations[$tableName][$name] : [],
                            ]));
                        }
                    }else if(isset($rel[self::REL_IS_MASTER]) && !$rel[self::REL_IS_MASTER] && !in_array($name, $this->skippedRelations)){
                        $files[] = new CodeFile("$viewPath/_form{$rel[self::REL_CLASS]}.php", $this->render("views/_formrefone.php", [
                            'relName' => $name,
                            'relations' => isset($relations[$tableName]) ? $relations[$tableName][$name] : [],
                        ]));
                        if ($this->expandable) {
                            $files[] = new CodeFile("$viewPath/_data{$rel[self::REL_CLASS]}.php", $this->render("views/_datarefone.php", [
                                'relName' => $name,
                                'relations' => isset($relations[$tableName]) ? $relations[$tableName][$name] : [],
                            ]));
                        }
                    }
                }
            }

            if (strpos($this->tableName, '*') !== false) {
                $this->modelClass = '';
                $this->controllerClass = '';
                $this->searchModelClass = '';
            } else {
                $this->modelClass = $modelClassName;
                $this->controllerClass = $controllerClassName;
                if ($this->generateSearchModel) {
                    $this->searchModelClass = $searchModelClassName;
                }
            }
        }
        $this->nameAttribute = (is_array($this->nameAttribute)) ? implode(', ', $this->nameAttribute) : '';
        $this->hiddenColumns = (is_array($this->hiddenColumns)) ? implode(', ', $this->hiddenColumns) : '';
        $this->skippedColumns = (is_array($this->skippedColumns)) ? implode(', ', $this->skippedColumns) : '';
        $this->skippedRelations = (is_array($this->skippedRelations)) ? implode(', ', $this->skippedRelations) : '';

        return $files;
    }

    public function getfile()
    {
    	return new CodeFile('teste.php', $this->render('controller.php', ['relations' => []]));
    }

    /*
    * @inheritdoc
    */
    private function validateModelGeneratorAttributes(&$modelGenerator)
    {
        if (empty($modelGenerator->tableSchema->primaryKey) && empty($this->filedPrimaryKey)) {
            throw new \yii\base\UserException(
                'A view <strong> ' . $modelGenerator->tableSchema->name . '</strong> não possui chave primária.
                Na primeira etapa da geração defina-a e tente novamente.
            ');
        }

        if (!isset($modelGenerator->tableSchema->primaryKey[0])) {
            $modelGenerator->tableSchema->primaryKey[0] = $this->filedPrimaryKey;
        }

    }
 /**
     * @inheritdoc
     */
    public function generateWithQueryCustom()
    {

          $files = [];
           $tableName = '';

           $this->relations = [];
           $db = $this->getDbConnection();

           $modelGenerator = new \mmsgenerator15\enhancedgii\model\Generator();

           $modelGenerator->nsModel = $this->nsModules[$this->module]['model'];
           $modelClassName = $this->modelClass;
           $modelGenerator->modelClass = "{$modelGenerator->nsModel}\\{$modelClassName}";
           $modelGenerator->tableName = $this->getTableNames()[0];
           $modelGenerator->tableSchema = $db->getTableSchema($modelGenerator->tableName);
           $modelGenerator->nameAttribute = ($this->nameAttribute) ? explode(',', str_replace(' ', '', $this->nameAttribute)) : [$this->nameAttribute];
           $modelGenerator->hiddenColumns = ($this->hiddenColumns) ? explode(',', str_replace(' ', '', $this->hiddenColumns)) : [$this->hiddenColumns];
           $modelGenerator->skippedColumns = ($this->skippedColumns) ? explode(',', str_replace(' ', '', $this->skippedColumns)) : [$this->skippedColumns];
           $modelGenerator->skippedRelations = ($this->skippedRelations) ? explode(',', str_replace(' ', '', $this->skippedRelations)) : [$this->skippedRelations];
           $modelGenerator->skippedColumns     = array_filter($modelGenerator->skippedColumns);
           $modelGenerator->skippedRelations = array_filter($modelGenerator->skippedRelations);
           $modelGenerator->query = $this->query;
           $modelGenerator->columnsArray = $this->columnsArray;
           $modelGenerator->filedPrimaryKey = $this->filedPrimaryKey;
           $modelGenerator->dhtmlxLayout = $this->dhtmlxLayout;

           $this->validateModelGeneratorAttributes($modelGenerator);

           $controllerClassName = ucfirst($this->controllerClass);
           $this->nsController = 'frontend\controllers';
           $this->nsView = 'frontend\views';
           $this->nsModel = 'common\models';
           $this->controllerClass =  "{$this->nsController}\\{$controllerClassName}";


           $queryClassName = ($this->generateQuery) ? $this->generateQueryClassName($modelClassName) : false;


    		$params = [
                'className' => $modelClassName,
                'queryClassName' => $queryClassName,
                'labels' => $modelGenerator->generateLabels($modelGenerator->tableSchema),
                'labelsQueryCustom' => $modelGenerator->generateLabelsQueryCustom($this->query[0]),
    		    'formSettings' => $this->formSettings,
                'rules' => $modelGenerator->generateRules($modelGenerator->tableSchema),
    		                  // 'relations' => isset($relations[$tableName]) ? $relations[$tableName] : [],
            ];

            // model :

            $files[] = new CodeFile(
                   Yii::getAlias('@' . str_replace('\\', '/', $modelGenerator->nsModel)) . '/base/' . $modelClassName . '.php', $modelGenerator->render('model.php', $params)
            );


            $files[] = new CodeFile(
                    Yii::getAlias('@' . str_replace('\\', '/', $modelGenerator->nsModel)) . '/' . $modelClassName . '.php', $modelGenerator->render('model-extended.php', $params)
            );


            // search model :
            if ($this->generateSearchModel) {
                if (empty($this->searchModelClass) || strpos($this->tableName, '*') !== false) {
                    $searchModelClassName = $modelClassName . 'Search';
                } else {
                    if ($this->nsSearchModel === $this->nsModel && $this->searchModelClass === $modelClassName) {
                        $searchModelClassName = $this->searchModelClass . 'Search';
                    } else {
                        $searchModelClassName = $this->searchModelClass;
                    }
                }
                $this->searchModelClass = $this->nsSearchModel . '\\' . $searchModelClassName;
                $searchModel = Yii::getAlias('@' . str_replace('\\', '/', ltrim($this->searchModelClass, '\\') . '.php'));
                $files[] = new CodeFile($searchModel, $this->render('search.php',
                    ['relations' => isset($relations[$tableName]) ? $relations[$tableName] : []]));
            }



            // views :
            $this->controllerName = str_ireplace('controller','',strtolower($controllerClassName));
            $this->controllerNameUrl = strtolower(preg_replace('/([a-zA-Z])(?=[A-Z])/', '$1-', str_ireplace('controller','',$controllerClassName)));
            $this->controllerAction = $this->module."/".$this->controllerName;
            $this->controllerUrl = "./index.php?c=" . $this->controllerAction;

            $this->generateForm();
            $templatePath = $this->getTemplatePath() . '/views';
            $viewPath = $this->getViewPathNsModules();
            foreach (scandir($templatePath) as $file) {
                if (is_file($templatePath . '/' . $file) && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                    $fileName = $file;
                    $files[] = new CodeFile("$viewPath/$fileName", $this->render("views/$file", [
                        'relations' => isset($relations[$tableName]) ? $relations[$tableName] : [],
                        //'isTree' => $isTree
                    ]));
                }
            }



            // controller :

            $this->pathViewForm = '@' . str_replace('\\', '/', $this->nsView). '/'. str_replace('\\', '/', $this->getControllerID()) . '/_form.php';
            $files[] = new CodeFile(
                Yii::getAlias(
                	'@' . str_replace('\\', '/', $this->controllerClass)). '.php',
                    $this->render('controller.php', [
                    'relations' => [],
                     'modelClassName' => $modelClassName
                    ])
                );

//             Descomente as linhas a baixo e crie o arquivo controller-extended.php para gerar o base controller
//             $files[] = new CodeFile(
//                 Yii::getAlias(
//                     '@' . str_replace('\\', '/', $this->controllerClass)) . '/base/' . $this->controllerClass . '.php',
//                     $this->render('controller-extended.php', [
//                     'relations' => [],
//                      'modelClassName' => $modelClassName
//                     ])
//                 );




// 		$this->modelClass = $modelClassName;
//         $this->controllerClass = $controllerClassName;

//         if ($this->generateSearchModel) {
//          	$this->searchModelClass = $searchModelClassName;
// 		}


//         $this->nameAttribute = (is_array($this->nameAttribute)) ? implode(', ', $this->nameAttribute) : '';
//         $this->hiddenColumns = (is_array($this->hiddenColumns)) ? implode(', ', $this->hiddenColumns) : '';
//         $this->skippedColumns = (is_array($this->skippedColumns)) ? implode(', ', $this->skippedColumns) : '';
//         $this->skippedRelations = (is_array($this->skippedRelations)) ? implode(', ', $this->skippedRelations) : '';

        return $files;
    }

    /**
     * @return string the controller ID (without the module ID prefix)
     */
    public function getControllerID()
    {
        $pos = strrpos($this->controllerClass, '\\');
        $class = substr(substr($this->controllerClass, $pos + 1), 0, -10);

        return Inflector::camel2id($class);
    }

    /**
     * @return string the controller view path
     */
    public function getViewPath()
    {
        if (empty($this->viewPath)) {
            return Yii::getAlias('@app/views/' . $this->getControllerID());
        } else {
            return Yii::getAlias($this->viewPath . '/' . $this->getControllerID());
        }
    }

    /**
    * @return string the  view path for modules
    */
    public function getViewPathNsModules()
    {
       return Yii::getAlias('@' . str_replace('\\', '/', $this->nsView)  .'/'.$this->getControllerID());
    }

    public function getNameAttribute()
    {
        foreach ($this->tableSchema->getColumnNames() as $name) {
            foreach ($this->nameAttribute as $nameAttr) {
                if (!strcasecmp($name, $nameAttr) || !strcasecmp($name, $this->tableSchema->fullName)) {
                    return $name;
                }
            }
        }
        /* @var $class ActiveRecord */
//        $class = $this->modelClass;
        $pk = empty($this->tableSchema->primaryKey) ? $this->tableSchema->getColumnNames()[0] : $this->tableSchema->primaryKey[0];

        return $pk;
    }

    public function getNameAttributeFK($tableName)
    {
        $tableSchema = $this->getDbConnection()->getTableSchema($tableName);
        foreach ($tableSchema->getColumnNames() as $name) {
            if (in_array($name, $this->nameAttribute) || $name === $tableName) {
                return $name;
            }
        }
        $pk = empty($tableSchema->primaryKey) ? $tableSchema->getColumnNames()[0] : $tableSchema->primaryKey[0];

        return $pk;
    }

    public function generateFK($tableSchema = null)
    {
        if (is_null($tableSchema)) {
            $tableSchema = $this->getTableSchema();
        }
        $fk = [];
        if (isset($this->relations[$tableSchema->fullName])) {
            foreach ($this->relations[$tableSchema->fullName] as $name => $relations) {
                foreach ($tableSchema->foreignKeys as $value) {
                    if (isset($relations[self::REL_FOREIGN_KEY]) && $relations[self::REL_TABLE] == $value[self::FK_TABLE_NAME]) {
                        if ($tableSchema->fullName == $value[self::FK_TABLE_NAME] && $relations[self::REL_IS_MULTIPLE]) { // In case of self-referenced tables (credit to : github.com/iurijacob)

                        } else {
                            $fk[$relations[5]] = $relations;
                            $fk[$relations[5]][] = $name;
                        }

                    }
                }
            }
        }
        return $fk;
    }



    /**
     * Generates code for Grid View field
     * @param string $attribute
     * @param TableSchema $tableSchema
     * @return string
     */
    public function generateDetailViewField($attribute, $fk, $tableSchema = null)
    {
        if (is_null($tableSchema)) {
            $tableSchema = $this->getTableSchema();
        }
        if (in_array($attribute, $this->hiddenColumns)) {
            return "['attribute' => '$attribute', 'hidden' => true],\n";
        }
        $humanize = Inflector::humanize($attribute, true);
        if ($tableSchema === false || !isset($tableSchema->columns[$attribute])) {
            if (preg_match('/^(password|pass|passwd|passcode)$/i', $attribute)) {
                return "";
            } else {
                return "'$attribute',\n";
            }
        }
        $column = $tableSchema->columns[$attribute];
        $format = $this->generateColumnFormat($column);
//        if($column->autoIncrement){
//            return "";
//        } else
        if (array_key_exists($attribute, $fk)) {
            $rel = $fk[$attribute];
            $labelCol = $this->getNameAttributeFK($rel[3]);
//            $humanize = Inflector::humanize($rel[3]);
//            $id = 'grid-' . Inflector::camel2id(StringHelper::basename($this->searchModelClass)) . '-' . $attribute;
//            $modelRel = $rel[2] ? lcfirst(Inflector::pluralize($rel[1])) : lcfirst($rel[1]);
            $output = "[
            'attribute' => '$rel[7].$labelCol',
            'label' => " . $this->generateString(ucwords(Inflector::humanize($rel[5]))) . ",
        ],\n";
            return $output;
        } else {
            return "'$attribute" . ($format === 'text' ? "" : ":" . $format) . "',\n";
        }
    }

    /**
     * Generates code for Grid View field
     * @param string $attribute
     * @param array $fk
     * @param TableSchema $tableSchema
     * @return string
     */
    public function generateGridViewField($attribute, $fk, $tableSchema = null)
    {
        if (is_null($tableSchema)) {
            $tableSchema = $this->getTableSchema();
        }

        if (in_array($attribute, $this->hiddenColumns)) {
//            return "['attribute' => '$attribute', 'hidden' => true],\n";
            return "";
        }
        if ($tableSchema === false || !isset($tableSchema->columns[$attribute])) {
            if (preg_match('/^(password|pass|passwd|passcode)$/i', $attribute)) {
                return "";
            } else {
                return "'$attribute',\n";
            }
        }
        $column = $tableSchema->columns[$attribute];
        $format = $this->generateColumnFormat($column);
        $baseClass = StringHelper::basename($this->modelClass);

        if (array_key_exists($attribute, $fk)) {
            $rel = $fk[$attribute];
            if ($rel[self::REL_CLASS] == $baseClass) {
                return "";
            }
            $labelCol = $this->getNameAttributeFK($rel[3]);
//            $modelRel = $rel[2] ? lcfirst(Inflector::pluralize($rel[1])) : lcfirst($rel[1]);
            $output = "[
                'attribute' => '$rel[7].$labelCol',
                'label' => " . $this->generateString(ucwords(Inflector::humanize($rel[5]))) . "
            ],\n";
            return $output;
        } else {
            return "'$attribute" . ($format === 'text' ? "" : ":" . $format) . "',\n";
        }
    }

    /**
     * Generates code for Grid View field
     * @param string $attribute
     * @param array $fk
     * @param TableSchema $tableSchema
     * @return string
     */
    public function generateGridViewFieldIndex($attribute, $fk, $tableSchema = null)
    {
        if (is_null($tableSchema)) {
            $tableSchema = $this->getTableSchema();
        }
        if (in_array($attribute, $this->hiddenColumns)) {
            return "['attribute' => '$attribute', 'hidden' => true],\n";
        }
//        $humanize = Inflector::humanize($attribute, true);
        if ($tableSchema === false || !isset($tableSchema->columns[$attribute])) {
            if (preg_match('/^(password|pass|passwd|passcode)$/i', $attribute)) {
                return "";
            } else {
                return "'$attribute',\n";
            }
        }
        $column = $tableSchema->columns[$attribute];
        $format = $this->generateColumnFormat($column);
//        if($column->autoIncrement){
//            return "";
//        } else
        if (array_key_exists($attribute, $fk) && $attribute) {
            $rel = $fk[$attribute];
            $labelCol = $this->getNameAttributeFK($rel[3]);
            $humanize = Inflector::humanize($rel[3]);
            $id = 'grid-' . Inflector::camel2id(StringHelper::basename($this->searchModelClass)) . '-' . $attribute;
//            $modelRel = $rel[2] ? lcfirst(Inflector::pluralize($rel[1])) : lcfirst($rel[1]);
            $output = "[
                'attribute' => '$attribute',
                'label' => " . $this->generateString(ucwords(Inflector::humanize($rel[5]))) . ",
                'value' => function(\$model){
                    return \$model->$rel[7]->$labelCol;
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => \\yii\\helpers\\ArrayHelper::map(\\$this->nsModel\\$rel[1]::find()->asArray()->all(), '{$rel[self::REL_PRIMARY_KEY]}', '$labelCol'),
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => '$humanize', 'id' => '$id']
            ],\n";
            return $output;
        } else {
            return "'$attribute" . ($format === 'text' ? "" : ":" . $format) . "',\n";
        }
    }

    /**
     * Generates code for Kartik Tabular Form field
     * @param string $attribute
     * @return string
     */
    public function generateTabularFormField($attribute, $fk, $tableSchema = null)
    {
        if (is_null($tableSchema)) {
            $tableSchema = $this->getTableSchema();
        }
        if (in_array($attribute, $this->hiddenColumns)) {
            return "\"$attribute\" => ['type' => TabularForm::INPUT_HIDDEN, 'columnOptions'=>['hidden'=>true]]";
        }
        $humanize = Inflector::humanize($attribute, true);
        if ($tableSchema === false || !isset($tableSchema->columns[$attribute])) {
            if (preg_match('/^(password|pass|passwd|passcode)$/i', $attribute)) {
                return "\"$attribute\" => ['type' => TabularForm::INPUT_PASSWORD]";
            } else {
                return "\"$attribute\" => ['type' => TabularForm::INPUT_TEXT]";
            }
        }
        $column = $tableSchema->columns[$attribute];
        if ($column->autoIncrement) {
            return "'$attribute' => ['type' => TabularForm::INPUT_HIDDEN, 'columnOptions' => ['hidden' => true]]";
        } elseif ($column->phpType === 'boolean' || $column->dbType === 'tinyint(1)') {
            return "'$attribute' => ['type' => TabularForm::INPUT_CHECKBOX]";
        } elseif ($column->type === 'text' || $column->dbType === 'tinytext') {
            return "'$attribute' => ['type' => TabularForm::INPUT_TEXTAREA]";
        } elseif ($column->dbType === 'date') {
            return "'$attribute' => ['type' => TabularForm::INPUT_WIDGET,
            'widgetClass' => \\kartik\\datecontrol\\DateControl::classname(),
            'options' => [
                'type' => \\kartik\\datecontrol\\DateControl::FORMAT_DATE,
                'saveFormat' => 'php:Y-m-d',
                'ajaxConversion' => true,
                'options' => [
                    'pluginOptions' => [
                        'placeholder' => " . $this->generateString('Choose ' . $humanize) . ",
                        'autoclose' => true
                    ]
                ],
            ]
        ]";
        } elseif ($column->dbType === 'time') {
            return "'$attribute' => ['type' => TabularForm::INPUT_WIDGET,
            'widgetClass' => \\kartik\\datecontrol\\DateControl::classname(),
            'options' => [
                'type' => \\kartik\\datecontrol\\DateControl::FORMAT_TIME,
                'saveFormat' => 'php:H:i:s',
                'ajaxConversion' => true,
                'options' => [
                    'pluginOptions' => [
                        'placeholder' => " . $this->generateString('Choose ' . $humanize) . ",
                        'autoclose' => true
                    ]
                ]
            ]
        ]";
        } elseif ($column->dbType === 'datetime') {
            return "'$attribute' => ['type' => TabularForm::INPUT_WIDGET,
            'widgetClass' => \\kartik\\datecontrol\\DateControl::classname(),
            'options' => [
                'type' => \\kartik\\datecontrol\\DateControl::FORMAT_DATETIME,
                'saveFormat' => 'php:Y-m-d H:i:s',
                'ajaxConversion' => true,
                'options' => [
                    'pluginOptions' => [
                        'placeholder' => " . $this->generateString('Choose ' . $humanize) . ",
                        'autoclose' => true,
                    ]
                ],
            ]
        ]";
        } elseif (array_key_exists($column->name, $fk)) {
            $rel = $fk[$column->name];
            $labelCol = $this->getNameAttributeFK($rel[self::REL_TABLE]);
            $humanize = Inflector::humanize($rel[self::REL_TABLE]);
//            $pk = empty($this->tableSchema->primaryKey) ? $this->tableSchema->getColumnNames()[0] : $this->tableSchema->primaryKey[0];
            $fkClassFQ = "\\" . $this->nsModel . "\\" . $rel[self::REL_CLASS];
            $output = "'$attribute' => [
            'label' => '$humanize',
            'type' => TabularForm::INPUT_WIDGET,
            'widgetClass' => \\kartik\\widgets\\Select2::className(),
            'options' => [
                'data' => \\yii\\helpers\\ArrayHelper::map($fkClassFQ::find()->orderBy('$labelCol')->asArray()->all(), '{$rel[self::REL_PRIMARY_KEY]}', '$labelCol'),
                'options' => ['placeholder' => " . $this->generateString('Choose ' . $humanize) . "],
            ],
            'columnOptions' => ['width' => '200px']
        ]";
            return $output;
        } else {
            if (preg_match('/^(password|pass|passwd|passcode)$/i', $column->name)) {
                $input = 'INPUT_PASSWORD';
            } else {
                $input = 'INPUT_TEXT';
            }
            if (is_array($column->enumValues) && count($column->enumValues) > 0) {
                $dropDownOptions = [];
                foreach ($column->enumValues as $enumValue) {
                    $dropDownOptions[$enumValue] = Inflector::humanize($enumValue);
                }
                return "'$attribute' => ['type' => TabularForm::INPUT_DROPDOWN_LIST,
                    'items' => " . preg_replace("/\n\s*/", ' ', VarDumper::export($dropDownOptions)) . ",
                    'options' => [
                        'columnOptions' => ['width' => '185px'],
                        'options' => ['placeholder' => " . $this->generateString('Choose ' . $humanize) . "],
                    ]
        ]";
            } elseif ($column->phpType !== 'string' || $column->size === null) {
                return "'$attribute' => ['type' => TabularForm::$input]";
            } else {
                return "'$attribute' => ['type' => TabularForm::$input]"; //max length??
            }
        }
    }

    /**
     * Generates code for active field
     * @param string $attribute
     * @return string
     */
    public function generateActiveField($attribute, $fk, $tableSchema = null, $relations = null, $isTree = false)
    {
        if ($isTree){
            $model = "\$node";
        } else if (is_null($relations)){
            $model = "\$model";
        }else{
            $model = '$'.$relations[self::REL_CLASS];
        }

        if (is_null($tableSchema)) {
            $tableSchema = $this->getTableSchema();
        }
        if (in_array($attribute, $this->hiddenColumns)) {
            return "\$form->field($model, '$attribute', ['template' => '{input}'])->textInput(['style' => 'display:none']);";
        }
        $placeholder = Inflector::humanize($attribute, true);
        if ($tableSchema === false || !isset($tableSchema->columns[$attribute])) {
            if (preg_match('/^(password|pass|passwd|passcode)$/i', $attribute)) {
                return "\$form->field($model, '$attribute')->passwordInput()";
            } else if (in_array($attribute, $this->hiddenColumns)) {
                return "\$form->field($model, '$attribute')->hiddenInput()";
            } else {
                return "\$form->field($model, '$attribute')";
            }
        }
        $column = $tableSchema->columns[$attribute];
        if ($column->phpType === 'boolean' || $column->dbType === 'tinyint(1)') {
            return "\$form->field($model, '$attribute')->checkbox()";
        } elseif ($column->type === 'text' || $column->dbType === 'tinytext') {
            return "\$form->field($model, '$attribute')->textarea(['rows' => 6])";
        } elseif ($column->dbType === 'date') {
            return "\$form->field($model, '$attribute')->widget(\\kartik\\datecontrol\\DateControl::classname(), [
        'type' => \\kartik\\datecontrol\\DateControl::FORMAT_DATE,
        'saveFormat' => 'php:Y-m-d',
        'ajaxConversion' => true,
        'options' => [
            'pluginOptions' => [
                'placeholder' => " . $this->generateString('Choose ' . $placeholder) . ",
                'autoclose' => true
            ]
        ],
    ]);";
        } elseif ($column->dbType === 'time') {
            return "\$form->field($model, '$attribute')->widget(\\kartik\\datecontrol\\DateControl::className(), [
        'type' => \\kartik\\datecontrol\\DateControl::FORMAT_TIME,
        'saveFormat' => 'php:H:i:s',
        'ajaxConversion' => true,
        'options' => [
            'pluginOptions' => [
                'placeholder' => " . $this->generateString('Choose ' . $placeholder) . ",
                'autoclose' => true
            ]
        ]
    ]);";
        } elseif ($column->dbType === 'datetime') {
            return "\$form->field($model, '$attribute')->widget(\\kartik\\datecontrol\\DateControl::classname(), [
        'type' => \\kartik\\datecontrol\\DateControl::FORMAT_DATETIME,
        'saveFormat' => 'php:Y-m-d H:i:s',
        'ajaxConversion' => true,
        'options' => [
            'pluginOptions' => [
                'placeholder' => " . $this->generateString('Choose ' . $placeholder) . ",
                'autoclose' => true,
            ]
        ],
    ]);";
        } elseif (array_key_exists($column->name, $fk)) {
            $rel = $fk[$column->name];
            $labelCol = $this->getNameAttributeFK($rel[3]);
            $humanize = Inflector::humanize($rel[3]);
//            $pk = empty($this->tableSchema->primaryKey) ? $this->tableSchema->getColumnNames()[0] : $this->tableSchema->primaryKey[0];
            $fkClassFQ = "\\" . $this->nsModel . "\\" . $rel[1];
            $output = "\$form->field($model, '$attribute')->widget(\\kartik\\widgets\\Select2::classname(), [
        'data' => \\yii\\helpers\\ArrayHelper::map($fkClassFQ::find()->orderBy('$rel[4]')->asArray()->all(), '$rel[4]', '$labelCol'),
        'options' => ['placeholder' => " . $this->generateString('Choose ' . $humanize) . "],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]);";
            return $output;
        } else {
            if (preg_match('/^(password|pass|passwd|passcode)$/i', $column->name)) {
                $input = 'passwordInput';
            } else {
                $input = 'textInput';
            }
            if (is_array($column->enumValues) && count($column->enumValues) > 0) {
                $dropDownOptions = [];
                foreach ($column->enumValues as $enumValue) {
                    $dropDownOptions[$enumValue] = Inflector::humanize($enumValue);
                }
                return "\$form->field($model, '$attribute')->dropDownList("
                . preg_replace("/\n\s*/", ' ', VarDumper::export($dropDownOptions)) . ", ['prompt' => ''])";
            } elseif ($column->phpType !== 'string' || $column->size === null) {
                return "\$form->field($model, '$attribute')->$input(['placeholder' => '$placeholder'])";
            } else {
                return "\$form->field($model, '$attribute')->$input(['maxlength' => true, 'placeholder' => '$placeholder'])";
            }
        }
    }

    /**
     * Generates column format
     * @param ColumnSchema $column
     * @return string
     */
    public function generateColumnFormat($column)
    {
        if ($column->phpType === 'boolean') {
            return 'boolean';
        } elseif ($column->type === 'text') {
            return 'ntext';
        } elseif (stripos($column->name, 'time') !== false && $column->phpType === 'integer') {
            return 'datetime';
        } elseif (stripos($column->name, 'email') !== false) {
            return 'email';
        } elseif (stripos($column->name, 'url') !== false) {
            return 'url';
        } else {
            return 'text';
        }
    }

    /**
     * Generates URL parameters
     * @return string
     */
    public function generateUrlParams()
    {
   	  if (empty($pks)) {
           	 return '';
       }

        $pks = $this->tableSchema->primaryKey;
        if (count($pks) === 1) {
            if (is_subclass_of($this->modelClass, 'yii\mongodb\ActiveRecord')) {
                return "'id' => (string)\$model->{$pks[0]}";
            } else {
                return "'id' => \$model->{$pks[0]}";
            }
        } else {
            $params = [];
            foreach ($pks as $pk) {
                if (is_subclass_of($this->modelClass, 'yii\mongodb\ActiveRecord')) {
                    $params[] = "'$pk' => (string)\$model->$pk";
                } else {
                    $params[] = "'$pk' => \$model->$pk";
                }
            }

            return implode(', ', $params);
        }
    }

    /**
     * Generates action parameters
     * @return string
     */
    public function generateActionParams()
    {
        $pks = $this->tableSchema->primaryKey;
        if (count($pks) === 1) {
            return '$id';
        } else {
            return '$' . implode(', $', $pks);
        }
    }

    /**
     * Generates parameter tags for phpdoc
     * @return array parameter tags for phpdoc
     */
    public function generateActionParamComments()
    {
        /* @var $class ActiveRecord */
        $pks = $this->tableSchema->primaryKey;
        if (($table = $this->getTableSchema()) === false) {
            $params = [];
            foreach ($pks as $pk) {
                $params[] = '@param ' . (substr(strtolower($pk), -2) == 'id' ? 'integer' : 'string') . ' $' . $pk;
            }

            return $params;
        }
        if (count($pks) === 1) {
            return ['@param ' . $table->columns[$pks[0]]->phpType . ' $id'];
        } else {
            $params = [];
            foreach ($pks as $pk) {
                $params[] = '@param ' . $table->columns[$pk]->phpType . ' $' . $pk;
            }

            return $params;
        }
    }

    /**
     * Generates validation rules for the search model.
     * @return array the generated validation rules
     */
    public function generateSearchRules() {
        if (($table = $this->getTableSchema()) === false) {
            return ["[['" . implode("', '", $this->getColumnNames()) . "'], 'safe']"];
        }
        $types = [];
        foreach ($table->columns as $column) {
            switch ($column->type) {
                case Schema::TYPE_SMALLINT:
                case Schema::TYPE_INTEGER:
                case Schema::TYPE_BIGINT:
                    $types['integer'][] = $column->name;
                    break;
                case Schema::TYPE_BOOLEAN:
                    $types['boolean'][] = $column->name;
                    break;
                case Schema::TYPE_FLOAT:
                case Schema::TYPE_DOUBLE:
                case Schema::TYPE_DECIMAL:
                case Schema::TYPE_MONEY:
                    $types['number'][] = $column->name;
                    break;
                case Schema::TYPE_DATE:
                case Schema::TYPE_TIME:
                case Schema::TYPE_DATETIME:
                case Schema::TYPE_TIMESTAMP:
                default:
                    $types['safe'][] = $column->name;
                    break;
            }
        }

        $rules = [];
        foreach ($types as $type => $columns) {
            $rules[] = "[['" . implode("', '", $columns) . "'], '$type']";
        }

        return $rules;
    }

    public function getPrimaryKey($tableName)
    {
        if (empty($tableName)) {
            return false;
        }

        $tableSchema = $this->getDbConnection()->getTableSchema($tableName);

        if (empty($tableSchema->primaryKey)) {
           $prefixTable = explode('_', $tableName)[0];

           return $prefixTable . '_ID';
        }
    }

    private function callMethodDynamically($method, $data, $returnThowException = true)
    {
        $methodExists = method_exists($this, $method);
        Yii::$app->v->isFalse(['methodExists' => $methodExists],'','app', $returnThowException);

        call_user_func_array([$this, $method], [$data]);
    }


    private function hasFormFilter()
    {
        if (count($this->formSettings) > 1) {
            return true;
        }

        return false;
    }

    /**
     * Gera o formulário
     * @return - Alimenta a propriedade $this->columnsType
     */
    public function generateForm() {
        if ($this->hasFormFilter()) {
                $this->hasFormFilter = true;
        }

        foreach ($this->formSettings as $keyForm => $formSettings) {

            if (empty($formSettings[0]['typeFields'])) {
                return false;
            }

            $prefixComponentsMethod = 'get';
            $types = [];
            $typeFields = $formSettings[0]['typeFields'];
            $totalFields = count($typeFields);

            for ($k = 0; $k < $totalFields; $k++) {

               $currentType = $typeFields[$k];
               $method = $prefixComponentsMethod . ucfirst(strtolower(preg_replace('/\s+/', '', $currentType['type'])));

               $this->callMethodDynamically($method, ['currentType' => $currentType, 'k' => $k, 'keyForm' => $keyForm ]);
            }
        }
    }



    private function getInputtext($data)
    {
        $currentType = $data['currentType'];
        $k = $data['k'];
        $keyForm = $data['keyForm'];
        $label = '<?= $al["'.$currentType["label"].'"] ?>';
        
        $this->columnsTypeJs[$keyForm][self::TYPE_INPUT_TEXT][$k]['obj'] = '{type:"'.self::TYPE_INPUT_TEXT.'",  name:"'. $currentType['name'] . '", label:"'. $label .'", inputWidth: 289,},';
    }

    private function getCombo($data)
    {
        $currentType = $data['currentType'];
        $k = $data['k'];
        $keyForm = $data['keyForm'];
        
        $urlSeg = '$this->seg()->urlEncode("'. $this->module. '/'. $this->controllerNameUrl .'/' . self::ACTION_COMBO  .'")';
        $urlCombo = "./index.php?c=<?= $urlSeg ?>";
            
        $varTb = "var tb = '" . $currentType['table']. "',";
        $varVal = "     val = '" . $currentType['value'] . "',";
        $varText = "     text = '" . $currentType['label']. "',";
        $varParms = "     params" . $k ." = '&table=' + tb + '&columnId=' + val + '&columnText=' + text;";
        $label = '<?= $al["'.$currentType["label"].'"] ?>';
        
        $this->columnsTypeJs[$keyForm][self::TYPE_COMBO][$k]['varTable'] = $varTb . "\n";
        $this->columnsTypeJs[$keyForm][self::TYPE_COMBO][$k]['varVal'] = $varVal . "\n";
        $this->columnsTypeJs[$keyForm][self::TYPE_COMBO][$k]['varText'] = $varText . "\n";
        $this->columnsTypeJs[$keyForm][self::TYPE_COMBO][$k]['varParam'] = $varParms . "\n";
        $this->columnsTypeJs[$keyForm][self::TYPE_COMBO][$k]['obj'] = '
            {
                type: "'.self::TYPE_COMBO.'", connector:"' . $urlCombo . '" + params'.$k.',
                name: "' . $currentType['name']  . '", label: "'. $label .'", width:289, readonly:true
            },
        ';
    }

    private function getAutocomplete($data)
    {
        $currentType = $data['currentType'];
        $k = $data['k'];
        $keyForm = $data['keyForm'];

        $urlSeg = '$this->seg()->urlEncode("'. $this->controllerAction .'/' . self::ACTION_AUTOCOMPLETE  .'")';
        $urlAutocomplete = "./index.php?c=<?= $urlSeg ?>";


        $varTb = "var tb = '" . $currentType['table']. "';";
        $varVal = "var val = '" . $currentType['value'] . "';";
        $varText = "var text = '" . $currentType['label']. "';";
        $varParms = "var params" . $k ." = '&table=' + tb + '&columnId=' + val + '&columnText=' + text;";
        $label = '<?= $al["'.$currentType["label"].'"] ?>';
         
        $this->columnsTypeJs[$keyForm][self::TYPE_COMBO][$k]['varTable'] = $varTb . "\n";
        $this->columnsTypeJs[$keyForm][self::TYPE_COMBO][$k]['varVal'] = $varVal . "\n";
        $this->columnsTypeJs[$keyForm][self::TYPE_COMBO][$k]['varText'] = $varText . "\n";
        $this->columnsTypeJs[$keyForm][self::TYPE_COMBO][$k]['varParam'] = $varParms . "\n";
        $this->columnsTypeJs[$keyForm][self::TYPE_COMBO][$k]['obj'] = '
            {
                type: "'.self::TYPE_AUTOCOMPLETE.'", connector:"' . $urlAutocomplete . '" + params'.$k.',
                name: "' . $currentType['name']  . '", label: "'. $label .'", filtering: true, width:289, minchar: 1
            },
        ';

    }

    private function getCalendar($data)
    {
        $currentType = $data['currentType'];
        $k = $data['k'];
        $keyForm = $data['keyForm'];
      
        $label = '<?= $al["'.$currentType["label"].'"] ?>';
        
        $this->columnsTypeJs[$keyForm][self::TYPE_CALENDAR][$k]['obj'] = '
            {type:"'.self::TYPE_CALENDAR.'",  name:"'. $currentType['name'] . '", label:"'. $label .'", inputWidth: 289,},
        ';

    }


    private function getComboEcm21($data)
    {
       $currentType = $data['currentType'];
        $k = $data['k'];
        $keyForm = $data['keyForm'];

        $urlSeg = '$this->seg()->urlEncode("'. $this->controllerAction .'/' . self::ACTION_COMBO  .'")';
        $urlCombo = "./index.php?c=<?= $urlSeg ?>";

        $varTb = "var tb = '" . $currentType['table']. "';";
        $varVal = "var val = '" . $currentType['value'] . "';";
        $varText = "var text = '" . $currentType['name']. "';";
        $varWhere = "var where = '" . $currentType['where']. "';";
        $varParms = "var params" . $k ." = '&table=' + tb + '&columnId=' + val + '&columnText=' + text + '&where=' + where;";
        $label = '<?= $al["'.$currentType["label"].'"] ?>';
        
        $this->columnsTypeJs[$keyForm][self::TYPE_COMBO][$k]['varTable'] = $varTb . "\n";
        $this->columnsTypeJs[$keyForm][self::TYPE_COMBO][$k]['varVal'] = $varVal . "\n";
        $this->columnsTypeJs[$keyForm][self::TYPE_COMBO][$k]['varText'] = $varText . "\n";
        $this->columnsTypeJs[$keyForm][self::TYPE_COMBO][$k]['varWhere'] = $varWhere . "\n";
        $this->columnsTypeJs[$keyForm][self::TYPE_COMBO][$k]['varParam'] = $varParms . "\n";
        $this->columnsTypeJs[$keyForm][self::TYPE_COMBO][$k]['obj'] = '
            {
                type: "'.self::TYPE_COMBO.'", connector:"' . $urlCombo . '" + params'.$k.',
                name: "' . $currentType['cupdate']  . '", label: "'. $label .'", width:289,
            },
        ';

    }

    /**
     * Retorna uma string com as variaveis relacionadas com o combo usado no form
     * @return string
     */
    public function getTextVarsComboForm($keyForm) {
       $varsText = '';
       if (isset($this->columnsTypeJs[$keyForm][self::TYPE_COMBO])) {

      	 	foreach ($this->columnsTypeJs[$keyForm] as $k => $component) {
      	 	    if ($k != 'input' ) {
                        foreach ($component as $k2 => $js) {
      	 	               if (isset($component[$k2]['varTable']) &&
      	 	                   isset($component[$k2]['varVal']) &&
      	 	                   isset($component[$k2]['varText']) &&
      	 	                   isset($component[$k2]['varParam'])) {
                        	       $varsText .= $component[$k2]['varTable'];
                                   $varsText .= $component[$k2]['varVal'];
                             	   $varsText .= $component[$k2]['varText'];

                             	   if (isset($component[$k2]['varWhere'])) {
                                        $varsText .= $component[$k2]['varWhere'];
                             	   }

                                   $varsText .= $component[$k2]['varParam'];
              	           }
      	 	            }
      	 	    }

            }

       }

       return $varsText;
    }
    /**
     * Generates the attribute labels for the search model.
     * @return array the generated attribute labels (name => label)
     */
    public function generateSearchLabels() {
        /* @var $model Model */
        $model = new $this->modelClass();
        $attributeLabels = $model->attributeLabels();
        $labels = [];
        foreach ($this->getColumnNames() as $name) {
            if (isset($attributeLabels[$name])) {
                $labels[$name] = $attributeLabels[$name];
            } else {
                if (!strcasecmp($name, 'id')) {
                    $labels[$name] = 'ID';
                } else {
                    $label = Inflector::camel2words($name);
                    if (!empty($label) && substr_compare($label, ' id', -3, 3, true) === 0) {
                        $label = substr($label, 0, -3) . ' ID';
                    }
                    $labels[$name] = $label;
                }
            }
        }

        return $labels;
    }

    /**
     * @return array searchable attributes
     */
    public function getSearchAttributes() {
        return $this->getColumnNames();
    }

    /**
     * Generates search conditions
     * @return array
     */
    public function generateSearchConditions() {
        $columns = [];
        if (($table = $this->getTableSchema()) === false) {
            $class = $this->modelClass;
            /* @var $model Model */
            $model = new $class();
            foreach ($model->attributes() as $attribute) {
                $columns[$attribute] = 'unknown';
            }
        } else {
            foreach ($table->columns as $column) {
                $columns[$column->name] = $column->type;
            }
        }

        $likeConditions = [];
        $hashConditions = [];
        foreach ($columns as $column => $type) {
            switch ($type) {
                case Schema::TYPE_SMALLINT:
                case Schema::TYPE_INTEGER:
                case Schema::TYPE_BIGINT:
                case Schema::TYPE_BOOLEAN:
                case Schema::TYPE_FLOAT:
                case Schema::TYPE_DOUBLE:
                case Schema::TYPE_DECIMAL:
                case Schema::TYPE_MONEY:
                case Schema::TYPE_DATE:
                case Schema::TYPE_TIME:
                case Schema::TYPE_DATETIME:
                case Schema::TYPE_TIMESTAMP:
                    $hashConditions[] = "'{$column}' => \$this->{$column},";
                    break;
                default:
                    $likeConditions[] = "->andFilterWhere(['like', '{$column}', \$this->{$column}])";
                    break;
            }
        }

        $conditions = [];
        if (!empty($hashConditions)) {
            $conditions[] = "\$query->andFilterWhere([\n"
                . str_repeat(' ', 12) . implode("\n" . str_repeat(' ', 12), $hashConditions)
                . "\n" . str_repeat(' ', 8) . "]);\n";
        }
        if (!empty($likeConditions)) {
            $conditions[] = "\$query" . implode("\n" . str_repeat(' ', 12), $likeConditions) . ";\n";
        }

        return $conditions;
    }

}
