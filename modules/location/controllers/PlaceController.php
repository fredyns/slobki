<?php

namespace app\modules\location\controllers;

use Yii;
use app\modules\location\models\Place;
use app\modules\location\models\search\PlaceSearch;
use yii\web\Controller;
use yii\web\HttpException;
use yii\helpers\Url;
use yii\filters\AccessControl;
use dmstr\bootstrap\Tabs;

/**
 * PlaceController implements the CRUD actions for Place model.
 */
class PlaceController extends Controller
{
    /**
     * @var boolean whether to enable CSRF validation for the actions in this controller.
     * CSRF validation is enabled only when both this property and [[Request::enableCsrfValidation]] are true.
     */
    public $enableCsrfValidation = false;

    /**
     * Lists all Place models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PlaceSearch;
        $dataProvider = $searchModel->search($_GET);

        Tabs::clearLocalStorage();

        Url::remember();
        Yii::$app->session['__crudReturnUrl'] = null;

        return $this->render('index', [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
        ]);
    }

    /**
     * Displays a single Place model.
     * @param string $id
     *
     * @return mixed
     */
    public function actionView($id)
    {
        Yii::$app->session['__crudReturnUrl'] = Url::previous();
        Url::remember();
        Tabs::rememberActiveState();

        return $this->render('view', ['model' => $this->findModel($id)]);
    }

    /**
     * Creates a new Place model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Place;

        try {
            if ($model->load($_POST) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            } elseif (!Yii::$app->request->isPost) {
                $model->load($_GET);
            }
        } catch (\Exception $e) {
            $msg = (isset($e->errorInfo[2])) ? $e->errorInfo[2] : $e->getMessage();
            $model->addError('_exception', $msg);
        }

        return $this->render('create', ['model' => $model]);
    }

    /**
     * Updates an existing Place model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load($_POST) && $model->save()) {
            return $this->redirect(Url::previous());
        } else {
            return $this->render('update', ['model' => $model]);
        }
    }

    /**
     * Deletes an existing Place model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        try {
            $this->findModel($id)->delete();
        } catch (\Exception $e) {
            $msg = (isset($e->errorInfo[2])) ? $e->errorInfo[2] : $e->getMessage();
            Yii::$app->getSession()->addFlash('error', $msg);
            return $this->redirect(Url::previous());
        }

        // TODO: improve detection
        $isPivot = strstr('$id', ',');
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
     * Finds the Place model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Place the loaded model
     * @throws HttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Place::findOne($id)) !== null) {
            return $model;
        } else {
            throw new HttpException(404, 'The requested page does not exist.');
        }
    }

}