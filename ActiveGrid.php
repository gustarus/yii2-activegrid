<?php
/**
 * Created by PhpStorm.
 * User: supreme
 * Date: 02.05.14
 * Time: 1:02
 */

namespace gustarus\activegrid;

use Yii;
use gustarus\activegrid\data\UnlimitedArrayDataProvider;
use gustarus\activegrid\columns\ActiveHiddenColumn;
use gustarus\activegrid\columns\RowSelectColumn;
use gustarus\activegrid\columns\ActiveInputColumn;
use yii\data\ArrayDataProvider;
use yii\db\ActiveRecord;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

class ActiveGrid extends GridView {

  const TEMPLATE_KEY = '#key#';
  const TEMPLATE_INDEX = '#index#';

  private static $indexes = [];

  /**
   * Родительская модель для связей которой строится виджет.
   * @var ActiveRecord
   */
  public $model;

  /**
   * Экземпляр из списка @property $relations.
   * Под эту модель отведено отдельное свойство потому что @property $relations может быть пустым (ниодной связанной модели).
   * @var ActiveRecord
   */
  public $relation;

  /**
   * Список моделей для провайдера.
   * @var ActiveRecord[]
   */
  public $relations;

  /**
   * Модель формы.
   * @var ActiveForm
   */
  public $form;

  /**
   * @inheritdoc
   */
  public $dataColumnClass = 'gustarus\activegrid\columns\ActiveInputColumn';


  /**
   * Возможность добавления записей.
   * @var bool
   */
  public $allowAdd = true;

  /**
   * Возможность удаления записей.
   * @var bool
   */
  public $allowDelete = true;


  /**
   * Вывести скрытую колонку с id моделей.
   * @var bool
   */
  public $enablePrimaryColumn = true;

  /**
   * Вывести колонку с выбором строчек.
   * @var bool
   */
  public $enableSelectColumn = true;


  /**
   * @inheritdoc
   */
  public $options = [
    'class' => 'form-group',
  ];

  /**
   * @inheritdoc
   */
  public $tableOptions = ['class' => 'table table-bordered table-striped table-activegrid'];

  /**
   * @inheritdoc
   */
  public $layout = "{items}\n{buttons}";

  /**
   * @inheritdoc
   */
  public $emptyText = 'It Is Found Nothing';

  /**
   * @inheritdoc
   */
  public $emptyTextOptions = ['class' => 'text-center'];


  /**
   * Шаблон кнопок.
   * @var string
   */
  public $buttonsTemplate = '{add}&nbsp;{delete}';

  /**
   * Опции отображенгия кнопок.
   * @var array
   */
  public $buttonsOptions = ['class' => 'clearfix text-center'];

  /**
   * Прототип кнопки.
   * @var array
   */
  public $button = [
    'tag' => 'a',
    'content' => 'Link',
    'options' => []
  ];

  /**
   * Коллекция кнопок.
   * @var array
   */
  public $buttons = [
    'add' => [
      'tag' => 'a',
      'content' => 'Add new',
      'options' => [
        'class' => 'btn btn-success btn-sm',
        'href' => '#',
      ]
    ],

    'delete' => [
      'tag' => 'a',
      'content' => 'Delete selected',
      'options' => [
        'data-active-on-select' => true,
        'class' => 'btn btn-danger btn-sm',
        'href' => '#',
      ]
    ]
  ];

  /**
   * Коллекция кнопок которая будет добавлена к основным кнопкам.
   * @var array
   */
  public $extraButtons = [];

  /**
   * @inheritdoc
   * @throws \yii\base\InvalidConfigException
   */
  public function init() {
    // сливаем две коллекции кнопок
    $this->buttons = array_merge($this->buttons, $this->extraButtons);

    // инициализация моделей
    if (is_array($this->relations)) {
      $this->dataProvider = new UnlimitedArrayDataProvider([
        'allModels' => $this->relations,
        'pagination' => false,
      ]);
    }

    // добавляем скрытую колонку с id
    if ($this->enablePrimaryColumn) {
      foreach ($this->relation->primaryKey() as $key) {
        $this->columns[] = [
          'class' => ActiveHiddenColumn::className(),
          'attribute' => $key
        ];
      }
    }

    // добавляем колонку с чекбоксами
    if ($this->enableSelectColumn) {
      $this->columns[] = [
        'class' => RowSelectColumn::className(),
      ];
    }

    // инициализация кнопок
    if ($this->buttons) {
      foreach ($this->buttons as $name => &$button) {
        // id кнопки
        $button['options']['id'] = 'btn-' . $this->id . '-' . $name;

        // перевод кнопки
        if (is_string($button['content'])) {
          $button['content'] = Yii::t('app', $button['content']);
        }
      }
    }

    parent::init();
  }

  /**
   * @inheritdoc
   * @throws \yii\base\InvalidConfigException
   */
  public function run() {
    $id = $this->options['id'];

    ActiveGridAsset::register($this->getView());

    $options = [
      'relationFormName' => $this->relation->formName(),
      'emptyText' => Yii::t('app', $this->emptyText),
      'emptyTextOptions' => $this->emptyTextOptions,
      'rowTemplate' => $this->renderTableRowTemplate(),
    ];

    $script = 'var $grid = $("#' . $id . '").activeGrid(' . json_encode($options) . ');';

    // bind controls
    // we have two general controls
    // and there could be additional controls
    foreach ($this->buttons as $name => $button) {
      $bind = true;
      switch ($name) {
        case 'add':
          $bind = $this->allowAdd;
          break;

        case 'delete':
          $bind = $this->allowDelete;
          break;
      }

      if ($bind) {
        $script .= '$grid.activeGrid("bindControl", "#' . $button['options']['id'] . '", "' . $name . '");';
      }
    }

    $this->getView()->registerJs('(function(){' . $script . '})();');

    parent::run();
  }


  /**
   * @inheritdoc
   */
  public function renderSection($name) {
    switch ($name) {
      case '{items}':
        return $this->renderItems();
      case '{buttons}':
        return $this->renderButtons();
      default:
        return false;
    }
  }

  /**
   * @inheritdoc
   * @throws \yii\base\NotSupportedException
   */
  public function renderTableBody() {
    /** @var \gustarus\activerecord\ActiveRecord $relation */
    $relation = $this->relation;

    /** @var \gustarus\activerecord\ActiveRecord[] $models */
    $models = array_values($this->dataProvider->getModels());
    $keys = $this->dataProvider->getKeys();
    $rows = [];

    $relationFormName = $this->relation->formName();
    if (!isset(self::$indexes[$relationFormName])) {
      self::$indexes[$relationFormName] = 0;
    }

    foreach ($models as $index => $model) {
      $modelClassName = $model::className();
      $relationClassName = $relation::className();
      if ($modelClassName !== $relationClassName) {
        $msg = "Model #$model->primaryKey from table data has a class distinct from class on \$this->relation model. Model and \$this->relation should be the same classes.\n\nModel class: $modelClassName\nRelation class: $relationClassName";
        throw new \yii\base\NotSupportedException($msg);
      }

      $key = $keys[$index];
      $globalIndex = self::$indexes[$relationFormName];
      self::$indexes[$relationFormName]++;
      $rows[] = $this->renderTableRow($model, $key, $globalIndex);
    }

    $latestIndex = self::$indexes[$relationFormName];
    $script = "$.fn.activeGrid('setLatestIndex', '$relationFormName', $latestIndex);";
    $this->getView()->registerJs('(function(){' . $script . '})();');

    return "<tbody>\n" . implode("\n", $rows) . "\n</tbody>";
  }

  /**
   * Рендер шаблона строки таблицы.
   * @return string
   */
  public function renderTableRowTemplate() {
    return $this->renderTableRow($this->relation, self::TEMPLATE_KEY, self::TEMPLATE_INDEX);
  }

  /**
   * Выполняет компиляцию кнопок.
   * @return string
   */
  public function renderButtons() {
    $buttons = [];
    foreach ($this->buttons as $name => $button) {
      if ($name != 'add' && $name != 'delete'
        || $name == 'add' && $this->allowAdd
        || $name == 'delete' && $this->allowDelete
      ) {
        $button = array_merge($this->button, $button);
        $buttons['{' . $name . '}'] = Html::tag($button['tag'], $button['content'], $button['options']);
      }
    }

    return $buttons ? Html::tag('div', strtr($this->buttonsTemplate, $buttons), $this->buttonsOptions) : '';
  }
}
