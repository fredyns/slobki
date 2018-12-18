<?php

use app\models\InstalationDistribution;
use dmstr\bootstrap\Tabs;
use yii\bootstrap\ButtonDropdown;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\DetailView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model InstalationDistribution */

$this->title = $model->aliasModel.' | '.$model->submission_id;
$this->params['breadcrumbs'][] = ['label' => $model->getAliasModel(TRUE), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => '#'.$model->submission_id, 'url' => ['view', 'submission_id' => $model->submission_id]];
$this->params['breadcrumbs'][] = Yii::t('cruds', 'View');
?>
<div class="giiant-crud instalation-distribution-view">

    <h1>
        <?= $model->aliasModel ?>
        <small>
            <?= Html::encode($model->submission_id) ?>
        </small>
    </h1>

    <div class="clearfix crud-navigation">

        <!-- menu buttons -->
        <div class='pull-right'>
            <?=
            ButtonDropdown::widget([
                'label' => Yii::t('cruds', 'Edit'),
                'tagName' => 'a',
                'split' => true,
                'options' => [
                    'href' => ['update', 'submission_id' => $model->submission_id],
                    'class' => 'btn btn-info',
                ],
                'dropdown' => [
                    'encodeLabels' => FALSE,
                    'options' => [
                        'class' => 'dropdown-menu-right',
                    ],
                    'items' => [
                        '<li role="presentation" class="divider"></li>',
                        [
                            'label' => '<span class="glyphicon glyphicon-list"></span> '.Yii::t('cruds', 'Full list'),
                            'url' => ['index'],
                        ],
                        [
                            'label' => '<span class="glyphicon glyphicon-plus"></span> '.Yii::t('cruds', 'New'),
                            'url' => ['create'],
                        ],
                        '<li role="presentation" class="divider"></li>',
                        [
                            'label' => '<span class="glyphicon glyphicon-trash"></span> '.Yii::t('cruds', 'Delete'),
                            'url' => ['delete', 'submission_id' => $model->submission_id],
                            'linkOptions' => [
                                'data-confirm' => Yii::t('cruds', 'Are you sure to delete this item?'),
                                'data-method' => 'post',
                                'data-pjax' => FALSE,
                                'class' => 'label label-danger',
                            ],
                        ],
                    ],
                ],
            ]);
            ?>
        </div>

    </div>

    <hr/>

    
    <?=
    DetailView::widget([
        'model' => $model,
        'attributes' => [
            // generated by app\generator\crud\providers\RelationProvider::attributeFormat
            [
                'attribute' => 'submission_id',
                'format' => 'html',
                'value' => ArrayHelper::getValue($model, 'submission.id', '<span class="label label-warning">?</span>'),
            ],
            // generated by app\generator\crud\providers\RelationProvider::attributeFormat
            [
                'attribute' => 'subtype_id',
                'format' => 'html',
                'value' => ArrayHelper::getValue($model, 'subtype.name', '<span class="label label-warning">?</span>'),
            ],
            'ownership_status',
            'voltage_id',
            'substation_quantity',
            'panel_quantity',
            'jtm_length_kms',
            'sktm_length_ms',
            'sutm_length_ms',
            'jtr_length_kms',
            'sktr_length_ms',
            'sutr_length_ms',
            'substation_capacity_kva',
            'short_circuit_capacity_a',
            'distribution_region',
        ],
    ]);
    ?>

    
    <hr/>
    

    <hr/>

</div>