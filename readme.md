Use this to build active grids to manage multiple relations.

## Usage example
```php
<? use gustarus\activegrid\ActiveGrid; ?>

<? echo ActiveGrid::widget([
  'model' => new Model(),
  'form' => $form, // yii\widgets\ActiveForm
  'models' => $model->quizAnswers,
  'columns' => [
    [
      'class' => ActiveInputColumn::className(),
      'attribute' => 'content',
      'header' => 'Text',
      'headerOptions' => ['class' => 'text-center'],
      'fieldOptions' => ['class' => 'form-control'],
    ],

    [
      'class' => ActiveSelectColumn::className(),
      'attribute' => 'next_question_id',
      'header' => 'Next question',
      'fieldData' => $questionsList,
      'headerOptions' => ['class' => 'text-center'],
      'fieldOptions' => ['prompt' => '', 'class' => 'form-control'],
    ]
  ]
]); ?>
```
