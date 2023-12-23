<?php
namespace app\controllers;

use app\models\Tariff;
use app\models\Category;
use Yii;
use yii\filters\auth\HttpBearerAuth; //реализация базовой Http аутентификации
use yii\web\UploadedFile;

use yii\rest\ActiveController;
class TariffController extends FunctionController
{
    public $modelClass = 'app\models\Tariff';

    public function behaviors() //поведение
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
        'class' => HttpBearerAuth::class,
        'only'=>['create', 'change', 'delete']
        ];
        return $behaviors;
    }

    public function actionCreate()
    {
        if (!$this->admin()) return $this->send(403, $this->auth_adm);
        $data=Yii::$app->request->post();
        $tariff=new Tariff();
        $tariff->load($data, '');
        if (!$tariff->validate()) return $this->validation($tariff);

        if (UploadedFile::getInstanceByName('photo')){
            $tariff->photo=UploadedFile::getInstanceByName('photo');
            $hash=hash('md5', $tariff->photo->baseName) . '.' . $tariff->photo->extension;
            $tariff->photo->saveAs(\Yii::$app->basePath. '/assets/upload/' . $hash);
            $tariff->photo=$hash;
        }

        $tariff->save(false);
        $answer=['data'=>['status'=>'ОК', 'id'=>(int)$tariff->id_tariff]]; 
        return $this->send(200, $answer);
    }

    public function actionCatalog() //вывод раздела с тарифами
    {
        $tariffs= Tariff::find()->select(['id_tariff', 'name_tariff', 'price_min'])->all();
        if ($tariffs)
        {
            $answer=['data'=>['tariffs'=>$tariffs]]; 
            return $this->send(200, $answer);
        }
        return $this->send(404, $this->not_found);
    }

    
    public function actionView($id) //вывод тарифа
    {
        $tariff= Tariff::findOne($id);
        if (!$tariff) return $this->send(404, $this->not_found);
        $category=new Category($tariff->getCategory()->one());
        $tariff->category_id = $category->name_category;
        $answer=['data'=>['tariff'=>$tariff]];
        return $this->send(200, $answer);
    }

    public function actionChange($id)
    {
        if (!$this->admin()) return $this->send(403, $this->auth_adm);
        $tariff = Tariff::findOne($id);
        if(!$tariff) return $this->send(404, $this->not_found);
        $data=Yii::$app->request->post();
        $tariff->load($data, '');
        
        if (UploadedFile::getInstanceByName('photo')){
       
            $url=Yii::$app->basePath.$tariff->photo;
             $tariff->photo = UploadedFile::getInstanceByName('photo');
            
             if (!$tariff->validate()) return $this->validation($tariff);
             @unlink($url);
     
             $photo_name='/assets/upload/photo_tariff_' . Yii::$app->getSecurity()->generateRandomString(40) .'.'. $tariff->photo->extension;
            
             $tariff->photo->saveAs(Yii::$app->basePath.$photo_name);
             $tariff->photo=$photo_name; 
            }
        
        $tariff->save(false); //false — произвести запись без валидации
        $answer=['data'=>['status'=>'ОК']]; 
        return $this->send(200, $answer);
    }

    public function actionDelete($id)
    {
        if (!$this->admin()) return $this->send(403, $this->auth_adm);
        $tariff=Tariff::findOne($id);
        if(!$tariff) return $this->send(404, $this->not_found);
        $tariff->delete();
        $answer=['data'=>['status'=>'ОК']]; 
        return $this->send(200, $answer);
    }

}