<?php

namespace app\models;

use Yii;
use app\dictionaries\InstalationType;
use app\dictionaries\SubmissionProgressStatus;
use app\models\Submission;
use fredyns\stringcleaner\StringCleaner;
use yii\helpers\ArrayHelper;

/**
 * This is the form model class for table "submission".
 * 
 * @property InstalationDistributionForm $distribution
 * @property InstalationGeneratorForm $generator
 * @property InstalationTransmissionForm $transmission
 * @property InstalationUtilizationForm $utilization
 * @property InstalationDistributionForm $instalationDistribution
 * @property InstalationGeneratorForm $instalationGenerator
 * @property InstalationTransmissionForm $instalationTransmission
 * @property InstalationUtilizationForm $instalationUtilization
 */
class SubmissionForm extends Submission
{
    const SCENARIO_APPLY_REQUEST = 'apply-request';

    public $report_file;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(
                parent::behaviors(), [
                # custom behaviors
                'file_upload' => [
                    'class' => 'mdm\upload\UploadBehavior',
                    'attribute' => 'report_file', // required, use to receive input file
                    'savedAttribute' => 'report_file_id', // optional, use to link model with saved file.
                    'uploadPath' => '@app/content/slo'.DIRECTORY_SEPARATOR.$this->id, // saved directory. default to '@runtime/upload'
                    'autoSave' => true, // when true then uploaded file will be save before ActiveRecord::save()
                    'autoDelete' => true, // when true then uploaded file will deleted before ActiveRecord::delete()
                ],
                ]
        );
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            # filter
            'string_filter' => [
                ['instalation_location', 'agenda_number', 'report_number', 'instalation_name'],
                'filter',
                'filter' => function($value){
                    return StringCleaner::forPlaintext($value);
                },
            ],
            # default
            [['progress_status'], 'default', 'value' => SubmissionProgressStatus::REQUEST],
            # required
            [['instalation_name'], 'required'],
            # type
            [['progress_status', 'owner_id', 'instalation_type', 'instalation_country_id', 'instalation_province_id', 'instalation_regency_id', 'bussiness_type_id', 'sbu_id', 'technical_pic_id', 'technical_personel_id'], 'integer'],
            [['instalation_location'], 'string'],
            [['instalation_latitude', 'instalation_longitude'], 'number'],
            [['agenda_number', 'report_number'], 'string', 'max' => 64],
            [['instalation_name'], 'string', 'max' => 128],
            # format
            [['examination_date'], 'date', 'format' => 'yyyy-MM-dd'],
            # option
            [
                ['progress_status'],
                'in', 'range' => [
                    SubmissionProgressStatus::REQUEST,
                    SubmissionProgressStatus::REGISTRATION,
                    SubmissionProgressStatus::REGISTERED,
                ],
            ],
            [
                ['instalation_type'],
                'in', 'range' => [
                    InstalationType::GENERATOR,
                    InstalationType::TRANSMISSION,
                    InstalationType::DISTRIBUTION,
                    InstalationType::UTILIZATION,
                ],
            ],
            # constraint
            [
                ['bussiness_type_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => \app\models\BussinessType::className(),
                'targetAttribute' => ['bussiness_type_id' => 'id'],
            ],
            [
                ['owner_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => \app\models\Owner::className(),
                'targetAttribute' => ['owner_id' => 'id'],
            ],
            [
                ['sbu_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => \app\models\Sbu::className(),
                'targetAttribute' => ['sbu_id' => 'id'],
            ],
            [
                ['technical_personel_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => \app\models\TechnicalPersonel::className(),
                'targetAttribute' => ['technical_personel_id' => 'id'],
            ],
            [
                ['technical_pic_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => \app\models\TechnicalPic::className(),
                'targetAttribute' => ['technical_pic_id' => 'id'],
            ],
            # safe
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();

        $scenarios[static::SCENARIO_APPLY_REQUEST] = [
            'examination_date',
            'instalation_type',
            'owner_id',
            'instalation_name',
            'instalation_location',
            'instalation_country_id',
            'instalation_province_id',
            'instalation_regency_id',
        ];

        return $scenarios;
    }

    /**
     * save new request application
     * 
     * @return boolean
     */
    public function applyRequest()
    {
        $this->scenario = static::SCENARIO_APPLY_REQUEST;

        if ($this->load(Yii::$app->request->post()) == FALSE) {
            return FALSE;
        }

        if ($this->validate() == FALSE) {
            return FALSE;
        }

        $this->progress_status = SubmissionProgressStatus::REQUEST;
        $this->requested_by = Yii::$app->user->id;
        $this->requested_at = time();

        return $this->save(FALSE);
    }

    /**
     * @inheritdoc
     */
    public function save($runValidation = true, $attributeNames = null)
    {
        // when no validation required

        if ($runValidation === FALSE) {
            return parent::save($runValidation, $attributeNames);
        }

        // validate main form

        if ($this->validate($attributeNames) == FALSE) {
            return FALSE;
        }

        // save main form

        if (parent::save(FALSE, $attributeNames) == FALSE) {
            return FALSE;
        }

        // select technical form

        switch ($this->instalation_type) {
            case InstalationType::GENERATOR:
                $technical_model = $this->generator;
                break;
            case InstalationType::TRANSMISSION:
                $technical_model = $this->transmission;
                break;
            case InstalationType::DISTRIBUTION:
                $technical_model = $this->distribution;
                break;
            case InstalationType::UTILIZATION:
                $technical_model = $this->utilization;
                break;
            default:
                // when no technical form
                return TRUE;
        }

        // execute technical form

        /* @var $technical_model \yii\db\ActiveRecord */
        $technical_model->load(Yii::$app->request->post());

        // save technical form

        return $technical_model->save();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstalationDistribution()
    {
        return $this->hasOne(InstalationDistributionForm::className(), ['submission_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstalationGenerator()
    {
        return $this->hasOne(InstalationGeneratorForm::className(), ['submission_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstalationTransmission()
    {
        return $this->hasOne(InstalationTransmissionForm::className(), ['submission_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstalationUtilization()
    {
        return $this->hasOne(InstalationUtilizationForm::className(), ['submission_id' => 'id']);
    }

    /**
     * @return InstalationDistributionForm
     */
    public function getDistribution()
    {
        if ($this->instalationDistribution) {
            return $this->instalationDistribution;
        } else  {
            return new InstalationDistributionForm(['submission_id' => $this->id]);
        }
    }

    /**
     * @return InstalationGeneratorForm
     */
    public function getGenerator()
    {
        if ($this->instalationGenerator) {
            return $this->instalationGenerator;
        } else  {
            return new InstalationGeneratorForm(['submission_id' => $this->id]);
        }
    }

    /**
     * @return InstalationTransmissionForm
     */
    public function getTransmission()
    {
        if ($this->instalationTransmission) {
            return $this->instalationTransmission;
        } else  {
            return new InstalationTransmissionForm(['submission_id' => $this->id]);
        }
    }

    /**
     * @return InstalationUtilizationForm
     */
    public function getUtilization()
    {
        if ($this->instalationUtilization) {
            return $this->instalationUtilization;
        } else  {
            return new InstalationUtilizationForm(['submission_id' => $this->id]);
        }
    }

}