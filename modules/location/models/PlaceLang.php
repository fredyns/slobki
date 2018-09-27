<?php

namespace app\modules\location\models;

use Yii;
use yii\helpers\ArrayHelper;
use app\modules\location\Module;
use app\modules\location\models\base\PlaceLang as BasePlaceLang;

/**
 * This is the base-model class for table "location_place_lang".
 *
 * @property string $id
 * @property string $place_id
 * @property string $language
 * @property string $name
 *
 * @property \app\modules\location\models\Place $place
 * @property string $aliasModel
 */
class PlaceLang extends BasePlaceLang
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%location_place_lang}}';
    }

    /**
     * Alias name of table for crud viewsLists all Area models.
     * Change the alias name manual if needed later
     * @return string
     */
    public function getAliasModel($plural = false)
    {
        if ($plural){
            return Module::t('models', 'Place Translations');
        } else{
            return Module::t('models', 'Place Translation');
        }
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
            [['place_id'], 'exist', 'skipOnError' => true, 'targetClass' => Place::className(), 'targetAttribute' => ['place_id' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Module::t('models', 'ID'),
            'place_id' => Module::t('models', 'Place'),
            'language' => Module::t('models', 'Language'),
            'name' => Module::t('models', 'Name'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlace()
    {
        return $this->hasOne(\app\modules\location\models\Place::className(), ['id' => 'place_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function afterSave($insert, $changedAttributes)
    {
        if (array_key_exists('name', $changedAttributes)) {
            $this->putSearchName($this->name, $changedAttributes['name']);
        }

        return parent::afterSave($insert, $changedAttributes);
    }

    /**
     * 
     * @param type $newValue
     * @param type $oldValue
     */
    public function putSearchName($newValue, $oldValue)
    {
        $replaced = 0;
        $searchName = $this->place->search_name;
        $searchName = str_replace($oldValue, $newValue, $searchName, $replaced);

        if ($replaced == 0) {
            $searchName .= $newValue.';';
        }

        $this->place->search_name = $searchName;
        $this->place->save(FALSE);
    }

}