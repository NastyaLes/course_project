<?php
namespace app\controllers;
use yii\rest\Controller;
use yii\web\Response;
use yii\widgets\ActiveForm;
use Yii;

class FunctionController extends Controller{

    public $not_found = ["error"=>["code"=> 404, "message"=>"Item not found"]];

    public $unauth = ["error"=>["code"=> 401, "message"=>"Unauthorized"]];

    public $auth_adm = ["error"=>["code"=> 403, "message"=>"Access denied"]];

    public function send($code, $data){
        $response=$this->response;
        $response->format = Response::FORMAT_JSON;
        $response->data=$data;
        $response->statusCode=$code;
        return $response;
    }

    public function validation($model){
        $error=['error'=> ['code'=>422, 'message'=>'Validation error',
        'errors'=>ActiveForm::validate($model)]];
        return $this->send(422, $error);
    }

    public function admin(){
        if (Yii::$app->user->identity->admin==1) return true; else return false;
    }
}
