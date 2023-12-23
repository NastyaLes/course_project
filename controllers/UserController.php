<?php
namespace app\controllers;

use app\models\User;
use app\models\Order;
use app\models\Tariff;
use app\models\Category;
use app\models\LoginForm;
use Yii;
use yii\filters\auth\HttpBearerAuth; //реализация базовой Http аутентификации

class UserController extends FunctionController
{
    public $modelClass = 'app\models\User';

    public function behaviors() //поведение
    {
    $behaviors = parent::behaviors();
    $behaviors['authenticator'] = [
    'class' => HttpBearerAuth::class,
    'only'=>['view', 'change']
    ];
    return $behaviors;
    }

    public function actionCreate() //создание новых моделей
    {
        $data=Yii::$app->request->post();
        $user=new User();
        $user->load($data, '');
        $user->password=Yii::$app->getSecurity()->generatePasswordHash($user->password); //хеширование пароля
        $user->token=Yii::$app->getSecurity()->generateRandomString(80); //для генерации случайного токена
        if (!$user->validate()) return $this->validation($user);
        $user->save(); //производит создание новой модели или обновления существующей
        $data=['data'=>['status'=>'ОК', 'id'=>(int)$user->id_user]]; 
        return $this->send(200, $data); //возвращает ответ
    }

    public function actionLogin() //аутентификация пользователя
    {
        $data=\Yii::$app->request->post();
        $login_data=new LoginForm();
        $login_data->load($data, '');
        if (!$login_data->validate()) return $this->validation($login_data);
        $user=User::find()->where(['phone'=>$login_data->phone])->one();
        if (!is_null($user)) {
        if (\Yii::$app->getSecurity()->validatePassword($login_data->password, $user->password)) {
        $token = \Yii::$app->getSecurity()->generateRandomString(80);
        $user->token = $token;
        $user->save(false); //false — произвести запись без валидации
        $data = ['data' => ['token' => $token]];
        return $this->send(200, $data);
        }
        }
        return $this->send(401, $this->unauth);
    }

    public function actionChange()
    {
        $user=Yii::$app->user->identity;
        $tel = Yii::$app->request->getBodyParams();
        $user->phone=$tel['phone'];
        if (!$user->validate()) return $this->validation($user);
        $user->save();
        $answer=['data'=>['status'=>'ОК']]; 
        return $this->send(200, $answer);
    }



    public function actionView()
    {    
        $user=Yii::$app->user->identity;
        $orders=$user->getOrders()->all();
        $orderItems='';
        $in_processing=[];
        $accepted=[];
        $completed=[];
        $refusal=[];
        if (!is_null($user)) {
        foreach ($orders as $order){
            $order=new Order($order);
            $tariff=new Tariff($order->getTariff()->one());
            $category=new Category($tariff->getCategory()->one());
            $price_min=(double)$tariff->price_min;
            $tariff->category_id = $category->name_category;
            $orderItems=['name_tariff'=>$tariff->name_tariff, 'price_min'=>$price_min, 'category'=>$tariff->category_id, 'options'=>$order->options, 'status_order'=>$order->status_order];            
                switch ($order->status_order){
                    case 'Принято в обработку': 
                        $in_processing[]=$orderItems;
                        $orderItems=[];
                        break;
                    case 'Подтвержден':
                        $accepted[]=$orderItems;
                        $orderItems=[];
                        break;
                    case 'Выполнен': 
                        $completed[]=$orderItems;
                        $orderItems=[];
                        break;
                    case 'Отказано': 
                        $refusal[]=$orderItems;
                        $orderItems=[];
                        break;
                }
        }
            $answer=['data'=>['user'=>$user, 'orders'=>['in_processing'=>$in_processing, 'accepted'=>$accepted, 'completed'=>$completed, 'refusal'=>$refusal]]]; 
            return $this->send(200, $answer);
        }
        return $this->send(404, $this->not_found);
    }

}