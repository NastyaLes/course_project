<?php
namespace app\controllers;

use app\models\Order;
use app\models\Tariff;
use Yii;
use yii\filters\auth\HttpBearerAuth; //реализация базовой Http аутентификации

class OrderController extends FunctionController
{
    public $modelClass = 'app\models\Order';

    public function behaviors() //поведение
    {
    $behaviors = parent::behaviors();
    $behaviors['authenticator'] = [
    'class' => HttpBearerAuth::class,
    'only'=>['create', 'change', 'delete']
    ];
    return $behaviors;
    }

    public function actionCreate() //создание новых моделей
    {
        $data=Yii::$app->request->post();
        $order=new Order();
        $order->load($data, '');
        if (!$order->validate()) return $this->validation($order);
        $order->save();
        $answer=['data'=>['status'=>'ОК', 'id'=>(int)$order->id_order]]; 
        return $this->send(200, $answer);
    }

    public function actionDelete($id)
    {
        $order=Order::findOne($id);
        if($order){
            if($order->status_order == "Принято в обработку")
            {
                $order->delete();
                $answer=['data'=>['status'=>'ОК']]; 
                return $this->send(200, $answer);
            }
            else 
            {
                $error=['error'=> ['code'=>403, 'message'=>'Access denied',
                'errors'=>'Order status completed or refusal']];
                return $this->send(422, $error);
            }
        }
        return $this->send(404, $this->not_found);
    }

    public function actionChange($id)
    {
        if (!$this->admin()) return $this->send(403, $this->auth_adm);
        $order= Order::findOne($id);
        $status = Yii::$app->request->getBodyParams();
        $order->status_order=$status['status_order'];
        if (!$order->validate()) return $this->validation($order);
        $order->save();
        $answer=['data'=>['status'=>'ОК']]; 
        return $this->send(200, $answer);
    }

}