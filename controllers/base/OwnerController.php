<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace app\controllers\base;

use Yii;
use app\models\Owner;
use app\models\OwnerForm;
use app\models\OwnerSearch;
use yii\web\Controller;
use dmstr\bootstrap\Tabs;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\HttpException;

/**
 * OwnerController implements the CRUD actions for Owner model.
 */
abstract class OwnerController extends Controller
{

    /**
     * @var boolean whether to enable CSRF validation for the actions in this controller.
     * CSRF validation is enabled only when both this property and [[Request::enableCsrfValidation]] are true.
     */
    public $enableCsrfValidation = false;


    /**
     * Lists all (active) Owner models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel  = new OwnerSearch(['is_deleted' => FALSE]);
        $dataProvider = $searchModel->search(Yii::$app->request->get());

        Tabs::clearLocalStorage();

        Url::remember();
        Yii::$app->session['__crudReturnUrl'] = null;

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Lists all archive of Owner models.
     * @return mixed
     */
    public function actionArchive()
    {
        $searchModel  = new OwnerSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->get());

        Tabs::clearLocalStorage();

        Url::remember();
        Yii::$app->session['__crudReturnUrl'] = null;

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Displays a single Owner model.
     * @param integer $id
     *
     * @return mixed
     */
    public function actionView($id)
    {
        Yii::$app->session['__crudReturnUrl'] = Url::previous();
        Url::remember();
        Tabs::rememberActiveState();

        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Owner model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new OwnerForm;

        try {
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            } elseif (!Yii::$app->request->isPost) {
                $model->load(Yii::$app->request->get());
            }
        } catch (\Exception $e) {
            $msg = (isset($e->errorInfo[2]))?$e->errorInfo[2]:$e->getMessage();
            $model->addError('_exception', $msg);
        }

        return $this->render('create', ['model' => $model]);
    }

    /**
     * Updates an existing Owner model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findForm($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(Url::previous());
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Owner model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        try {
            $model->softDelete();
        } catch (\Exception $e) {
            $msg = (isset($e->errorInfo[2]))?$e->errorInfo[2]:$e->getMessage();
            Yii::$app->getSession()->addFlash('error', $msg);
            
            return $this->redirect(Url::previous());
        }

        // TODO: improve detection
        $isPivot = strstr('$id',',');
        if ($isPivot == true) {
            return $this->redirect(Url::previous());
        } elseif (isset(Yii::$app->session['__crudReturnUrl']) && Yii::$app->session['__crudReturnUrl'] != '/') {
            Url::remember(null);
            $url = Yii::$app->session['__crudReturnUrl'];
            Yii::$app->session['__crudReturnUrl'] = null;

            return $this->redirect($url);
        } else {
            return $this->redirect(['index']);
        }
    }

    /**
     * Finds the Owner model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Owner the loaded model
     * @throws HttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Owner::findOne($id)) !== null) {
            return $model;
        } else {
            throw new HttpException(404, 'The requested page does not exist.');
        }
    }

    /**
     * Finds the OwnerForm model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Owner the loaded model
     * @throws HttpException if the model cannot be found
     */
    protected function findForm($id)
    {
        if (($model = OwnerForm::findOne($id)) !== null) {
            return $model;
        } else {
            throw new HttpException(404, 'The requested page does not exist.');
        }
    }

}
