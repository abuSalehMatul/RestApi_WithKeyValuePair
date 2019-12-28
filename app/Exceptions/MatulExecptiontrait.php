<?php
namespace App\Exceptions;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Debug\Exception\FatalErrorException;

trait MatulExecptiontrait{
    public function apiException($request, $e){
        if($this->isFatalException($e)){
            $this->fatalExceptionAction();
        }
        if($this->isnotFoundHttpException($e)){
            $this->notFoundHttpException();
        }
    }
    private function isFatalException($e){
        return $e instanceof FatalErrorException?true:false;
    }
    private function isnotFoundHttpException($e){
        return $e instanceof NotFoundHttpException?true:false;
    }
    private function fatalExceptionAction(){
        header("Status-Code: 404");
        $response['status_code_header'] = 'HTTP/1.1 404 Not found';
        $response['message']= 'endpoint not found or redis server not opened';
        echo json_encode($response);
    }
    private function notFoundHttpException(){
        header("Status-Code: 404");
        $response['status_code_header'] = 'HTTP/1.1 404 Not found';
        $response['message']= 'endpoint not found';
        echo json_encode($response);
    }
}