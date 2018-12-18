<?php

use app\models\InstalationGenerator;
use dmstr\bootstrap\Tabs;
use yii\bootstrap\ButtonDropdown;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\DetailView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model InstalationGenerator */
?>
<div class="instalation-generator-view">

    <?=
    DetailView::widget([
        'model' => $model,
        'attributes' => [
            // generated by app\generator\crud\providers\RelationProvider::attributeFormat
            [
                'attribute' => 'subtype_id',
                'format' => 'html',
                'value' => ArrayHelper::getValue($model, 'subtype.name', '<span class="label label-warning">?</span>'),
            ],
            // generated by app\generator\crud\providers\RelationProvider::attributeFormat
            [
                'attribute' => 'fuel_id',
                'format' => 'html',
                'value' => ArrayHelper::getValue($model, 'fuel.name', '<span class="label label-warning">?</span>'),
            ],
            'module_quantity',
            'inverter_quantity',
            [
                'attribute' => 'calorific_value_file_id',
                'format' => 'raw',
                'value' => ($model->calorific_value_file_id ? Html::a(Yii::t('app', 'download'), ['/file', 'id' => $model->calorific_value_file_id], ['target' => '_blank']) : '<span class="label label-warning">?</span>'),
            ],
            [
                'attribute' => 'capacity',
                'format' => 'html',
                'value' => ($model->capacity ? $model->capacity : '-').' '.$model->capacity_unit,
            ],
            [
                'attribute' => 'test_capacity',
                'format' => 'html',
                'value' => ($model->test_capacity ? $model->test_capacity : '-').' '.$model->test_capacity_unit,
            ],
            'unit_number',
            'turbine_serial_number',
            'generator_serial_number',
            'each_module_capacity',
            'each_inverter_capacity',
            'calorific_value',
            'fuel_consumption_hhv',
            'fuel_consumption_lhv',
            'sfc',
            'unit',
        ],
    ]);
    ?>

</div>
