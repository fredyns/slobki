<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/*
 * @var yii\web\View $this
 * @var schmunk42\giiant\generators\crud\Generator $generator
 */

/** @var \yii\db\ActiveRecord $model */
/** @var $generator \schmunk42\giiant\generators\crud\Generator */

## TODO: move to generator (?); cleanup
$model = new $generator->modelClass();
$model->setScenario('crud');
$safeAttributes = $model->safeAttributes();
if (empty($safeAttributes)) {
    $model->setScenario('default');
    $safeAttributes = $model->safeAttributes();
}
if (empty($safeAttributes)) {
    $safeAttributes = $model->getTableSchema()->columnNames;
}

$modelName = Inflector::camel2words(StringHelper::basename($model::className()));
$className = $model::className();
$urlParams = $generator->generateUrlParams();
$modelClass = StringHelper::basename($generator->modelClass);
$tableSchema = $generator->getTableSchema();
$haveID=($tableSchema->getColumn('id') !== null);
$softdelete = ($tableSchema->getColumn('is_deleted') !== null) && ($tableSchema->getColumn('deleted_at') !== null) && ($tableSchema->getColumn('deleted_by') !== null);

echo "<?php\n";
?>

use <?= ltrim($generator->modelClass, '\\') ?>;
use dmstr\bootstrap\Tabs;
use yii\bootstrap\ButtonDropdown;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\DetailView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model <?= $modelClass ?> */

$this->title = $model->aliasModel.' | '.$model-><?= $generator->getNameAttribute() ?>;
$this->params['breadcrumbs'][] = ['label' => $model->getAliasModel(TRUE), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => '#'.$model-><?= $haveID ? 'id' : $generator->getNameAttribute() ?>, 'url' => ['view', <?= $urlParams ?>]];
$this->params['breadcrumbs'][] = <?= $generator->generateString('View') ?>;
?>
<div class="giiant-crud <?= Inflector::camel2id(StringHelper::basename($generator->modelClass), '-', true) ?>-view">

    <div class="clearfix crud-navigation" style="padding-top: 30px;">
        <div class="pull-left">
            <h1 style="margin-top: 0;">
                <?= '<?=' ?> $model->aliasModel ?>
                <small>
<?php if($haveID):?>
                    #<?= '<?='?> $model->id ?>
<?php else:?>
                    <?= '<?= Html::encode($model->'.($generator->getModelNameAttribute($generator->modelClass)).") ?>\n" ?>
<?php endif;?>
<?php if($softdelete):?>
                    <?= '<?php' ?> if ($model->is_deleted): ?>
                        <span class="badge">deleted</span>
                    <?= '<?php' ?> endif; ?>
<?php endif;?>
                </small>
            </h1>
        </div>
        <!-- menu buttons -->
        <div class='pull-right'>
            <?="<?=\n"?>
            ButtonDropdown::widget([
                'label' => <?= $generator->generateString('Edit') ?>,
                'tagName' => 'a',
                'split' => true,
                'options' => [
                    'href' => ['update', <?= $urlParams ?>],
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
                            'label' => '<span class="glyphicon glyphicon-list"></span> '.<?= $generator->generateString('Full list') ?>,
                            'url' => ['index'],
                        ],
                        [
                            'label' => '<span class="glyphicon glyphicon-plus"></span> '.<?= $generator->generateString('New') ?>,
                            'url' => ['create'],
                        ],
                        '<li role="presentation" class="divider"></li>',
                        [
                            'label' => '<span class="glyphicon glyphicon-trash"></span> '.<?= $generator->generateString('Delete') ?>,
                            'url' => ['delete', <?= $urlParams ?>],
                            'linkOptions' => [
                                'data-confirm' => <?= $generator->generateString('Are you sure to delete this item?') ?>,
                                'data-method' => 'post',
                                'data-pjax' => FALSE,
                                'class' => 'label label-danger',
                            ],
<?php if($softdelete):?>
                            'visible' => ($model->is_deleted == FALSE),
                        ],
                        [
                            'label' => '<span class="glyphicon glyphicon-floppy-open"></span> '.<?= $generator->generateString('Restore') ?>,
                            'url' => ['delete', <?= $urlParams ?>],
                            'linkOptions' => [
                                'data-confirm' => <?= $generator->generateString('Are you sure to restore this item?') ?>,
                                'data-method' => 'post',
                                'data-pjax' => FALSE,
                                'class' => 'label label-info',
                            ],
                            'visible' => ($model->is_deleted),
<?php endif;?>
                        ],
                    ],
                ],
            ]);
            ?>
        </div>

    </div>

    <hr/>

    <?= $generator->partialView('detail_prepend', $model); ?>

    <?= "<?=\n" ?>
    DetailView::widget([
        'model' => $model,
        'attributes' => [
<?php
foreach ($safeAttributes as $attribute) {
    $format = $generator->attributeFormat($attribute);
    if (!$format) {
        continue;
    } else {
        echo "    {$format},\n";
    }
}
?>
        ],
    ]);
    ?>

    <?= $generator->partialView('detail_append', $model); ?>

    <hr/>
    <?php

    // get relation info $ prepare add button
    $model = new $generator->modelClass();

    $items = '';

    foreach ($generator->getModelRelations($generator->modelClass, ['has_many', 'has_one']) as $name => $relation) {
        echo "\n    <?php \$this->beginBlock('{$name}'); ?>\n";

        $showAllRecords = false;

        if ($relation->via !== null) {
            $pivotName = Inflector::pluralize($generator->getModelByTableName($relation->via->from[0]));
            $pivotRelation = $model->{'get'.$pivotName}();
            $pivotPk = key($pivotRelation->link);

            $addButton = "  <?= Html::a(
            '<span class=\"glyphicon glyphicon-link\"></span> '.".$generator->generateString('Attach').".' ".
                Inflector::singularize(Inflector::camel2words($name)).
                "', ['".$generator->createRelationRoute($pivotRelation, 'create')."', '".
                Inflector::singularize($pivotName)."' => ['".key(
                    $pivotRelation->link
                )."' => \$model->{$model->primaryKey()[0]}]],
            ['class' => 'btn btn-info btn-xs']
        ) ?>\n";
        } else {
            $addButton = '';
        }

        // relation list, add, create buttons
        echo "
    <div style='position: relative'>
        <div style='position:absolute; right: 0px; top: 0px;'>

            <?=
            Html::a(
                '<span class=\"glyphicon glyphicon-list\"></span> '.".$generator->generateString('List All').".' '.".$generator->generateString(Inflector::camel2words($name)).",
                ['".$generator->createRelationRoute($relation, 'index')."'],
                ['class' => 'btn text-muted btn-xs']
            );
            ?>


            <?=
            Html::a(
                '<span class=\"glyphicon glyphicon-plus\"></span> '.".$generator->generateString('New '.Inflector::singularize(Inflector::camel2words($name))).",
                ['".$generator->createRelationRoute($relation, 'create')."', '".Inflector::id2camel($generator->generateRelationTo($relation),'-',true)."' => ['".key($relation->link)."' => \$model->".$model->primaryKey()[0]."]],
                ['class' => 'btn btn-success btn-xs']
            );
            ?>

            {$addButton}

        </div>
    </div>

";

        // render pivot grid
        if ($relation->via !== null) {
            $pjaxId = "pjax-{$pivotName}";
            $gridRelation = $pivotRelation;
            $gridName = $pivotName;
        } else {
            $pjaxId = "pjax-{$name}";
            $gridRelation = $relation;
            $gridName = $name;
        }

        $output = $generator->relationGrid($gridName, $gridRelation, $showAllRecords);

        // render relation grid
        if (!empty($output)):
            echo  <<<PHP
    <?php
        Pjax::begin([
            'id' => 'pjax-{$name}',
            'enableReplaceState' => false,
            'linkSelector' => '#pjax-{$name} ul.pagination a, th a',
        ]);
    ?>
    <?=
$output
    ?>
    <?php Pjax::end(); ?>\n
PHP;
        endif;

        echo "    <?php \$this->endBlock() ?>\n\n";

        // build tab items
        $label = Inflector::camel2words($name);
        $label = $generator->generateString($label);
        $items .= <<<EOS
            [
                //'active' => false,
                'content' => \$this->blocks['$name'],
                'label' => '<small>'.{$label}.' <span class="badge badge-default">'. \$model->get{$name}()->count().'</span></small>',
            ],\n
EOS;
    }
    ?>

<?php
if ($items){
    echo "
    <?=
    Tabs::widget([
        'id' => 'relation-tabs',
        'encodeLabels' => false,
        'items' => [
{$items}
        ],
    ]);
    ?>\n
";

}
?>

    <hr/>
<?php if ($tableSchema->getColumn('created_at') !== null): ?>

    <div style="font-size: 75%; font-style: italic;">
        <?= '<?=' ?> Yii::t('timestamp', 'Created') ?>
        <?= '<?=' ?> Yii::$app->formatter->asDate($model->created_at, "d MMMM Y '".Yii::t('timestamp', 'at')."' HH:mm") ?>
<?php if ($tableSchema->getColumn('created_by') !== null): ?>
        <?= '<?=' ?> Yii::t('timestamp', 'by') ?>
        <?= '<?=' ?> ArrayHelper::getValue($model, 'createdBy.username', '-') ?>
<?php endif; ?>
<?php if ($tableSchema->getColumn('updated_at') !== null): ?>
        <br/>
        <?= '<?=' ?> Yii::t('timestamp', 'Updated') ?>
        <?= '<?=' ?> Yii::$app->formatter->asDate($model->updated_at, "d MMMM Y '".Yii::t('timestamp', 'at')."' HH:mm") ?>
<?php if ($tableSchema->getColumn('updated_by') !== null): ?>
        <?= '<?=' ?> Yii::t('timestamp', 'by') ?>
        <?= '<?=' ?> ArrayHelper::getValue($model, 'updatedBy.username', '-') ?>
<?php endif; ?>
<?php endif; ?>
<?php if ($tableSchema->getColumn('deleted_at') !== null): ?>
        <?='<?php'?> if ($model->is_deleted): ?>
            <br/>
            <?= '<?=' ?> Yii::t('timestamp', 'Deleted') ?>
            <?= '<?=' ?> Yii::$app->formatter->asDate($model->deleted_at, "d MMMM Y '".Yii::t('timestamp', 'at')."' HH:mm") ?>
<?php if ($tableSchema->getColumn('deleted_by') !== null): ?>
            <?= '<?=' ?> Yii::t('timestamp', 'by') ?>
            <?= '<?=' ?> ArrayHelper::getValue($model, 'deletedBy.username', '-') ?>
<?php endif; ?>
        <?='<?php'?> endif; ?>
<?php endif; ?>
    </div>
<?php endif; ?>

</div>
