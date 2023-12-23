<?php
namespace app\controllers;

use app\models\Category;
use app\models\Tariff;
use Yii;
use yii\filters\auth\HttpBearerAuth;

class CategoryController extends FunctionController
{
    public $modelClass = 'app\models\Category';

    public function behaviors() //поведение
    {
    $behaviors = parent::behaviors();
    $behaviors['authenticator'] = [
    'class' => HttpBearerAuth::class,
    'only'=>['create', 'delete']
    ];
    return $behaviors;
    }

    public function actionCreate()
    {
        if (!$this->admin()) return $this->send(403, $this->auth_adm);
        $data=Yii::$app->request->post();
        $category=new Category();
        $category->load($data, '');
        if (!$category->validate()) return $this->validation($category);
        $category->save();
        $answer=['data'=>['status'=>'ОК', 'id'=>(int)$category->id_category]]; 
        return $this->send(200, $answer);
    }

    public function actionDelete($id)
    {
        if (!$this->admin()) return $this->send(403, $this->auth_adm);
        $category=Category::findOne($id);
        if(!$category) return $this->send(404, $this->not_found);
        $category->delete();
        $answer=['data'=>['status'=>'ОК']]; 
        return $this->send(200, $answer);
    }

}