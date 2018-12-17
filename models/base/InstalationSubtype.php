<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace app\models\base;

use Yii;
use app\models\User;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the base-model class for table "instalation_subtype".
 *
 * @property integer $id
 * @property integer $created_at
 * @property integer $created_by
 * @property integer $updated_at
 * @property integer $updated_by
 * @property integer $is_deleted
 * @property integer $deleted_at
 * @property integer $deleted_by
 * @property string $name
 *
 * @property User $createdBy
 * @property User $updatedBy
 * @property User $deletedBy
 *
 * @property \app\models\InstalationDistribution[] $instalationDistributions
 * @property \app\models\InstalationGenerator[] $instalationGenerators
 * @property \app\models\InstalationTransmission[] $instalationTransmissions
 * @property \app\models\InstalationUtilization[] $instalationUtilizations
 *
 *
 * @method void softDelete() move to trash
 * @method void restore() pick up form trash
 */
abstract class InstalationSubtype extends \yii\db\ActiveRecord
{
    const ALIAS_CREATEDBY = 'createdBy';
    const ALIAS_UPDATEDBY = 'updatedBy';
    const ALIAS_DELETEDBY = 'deletedBy';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'instalation_subtype';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'blameable' => [
                'class' => BlameableBehavior::className(),
            ],
            'timestamp' => [
                'class' => TimestampBehavior::className(),
            ],
            'softdelete' => [
                'class' => SoftDeleteBehavior::className(),
                'softDeleteAttributeValues' => [
                    'is_deleted' => TRUE,
                    'deleted_at' => time(),
                    'deleted_by' => function($model) {
                        if (Yii::$app->user->isGuest === FALSE) {
                            return Yii::$app->user->id;
                        }

                        return NULL;
                    },
                ],
                'restoreAttributeValues' => [
                    'is_deleted' => FALSE,
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 512],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by'])->alias(static::ALIAS_CREATEDBY);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'updated_by'])->alias(static::ALIAS_UPDATEDBY);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDeletedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'deleted_by'])->alias(static::ALIAS_DELETEDBY);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstalationDistributions()
    {
        return $this->hasMany(\app\models\InstalationDistribution::className(), ['subtype_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstalationGenerators()
    {
        return $this->hasMany(\app\models\InstalationGenerator::className(), ['subtype_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstalationTransmissions()
    {
        return $this->hasMany(\app\models\InstalationTransmission::className(), ['subtype_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstalationUtilizations()
    {
        return $this->hasMany(\app\models\InstalationUtilization::className(), ['subtype_id' => 'id']);
    }

}
