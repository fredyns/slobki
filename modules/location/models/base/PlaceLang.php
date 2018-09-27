<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace app\modules\location\models\base;

use Yii;

/**
 * This is the base-model class for table "location_place_lang".
 *
 * @property string $id
 * @property string $place_id
 * @property string $language
 * @property string $name
 *
 * @property \app\modules\location\models\LocationPlace $place
 * @property string $aliasModel
 */
abstract class PlaceLang extends \yii\db\ActiveRecord
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'location_place_lang';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['place_id'], 'integer'],
            [['language'], 'string', 'max' => 16],
            [['name'], 'string', 'max' => 1024],
            [['place_id'], 'exist', 'skipOnError' => true, 'targetClass' => \app\modules\location\models\LocationPlace::className(), 'targetAttribute' => ['place_id' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('modules/location/models', 'ID'),
            'place_id' => Yii::t('modules/location/models', 'Place ID'),
            'language' => Yii::t('modules/location/models', 'Language'),
            'name' => Yii::t('modules/location/models', 'Name'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlace()
    {
        return $this->hasOne(\app\modules\location\models\LocationPlace::className(), ['id' => 'place_id']);
    }




}
