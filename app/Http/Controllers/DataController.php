<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Redis;
use  App\Http\Resources\DataStore;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use App\Http\Requests;

use Illuminate\Support\Facades\Cache;
class DataController extends Controller
{
    private $redis;
    public function __construct(){
        $this->redis=Redis::connection();
    }


    //mother method to call other method by judging request type and query string
    public function index(Request $request)
    { 
        //if request contain any querystring or not
        if($request->query('keys')==null){

            if($request->isMethod('get')){
               $this->showall();
            }

            //if the request is post type
            elseif($request->isMethod('post')){

                //check if data available on request body
                if(sizeof($request->toArray())>0){
                    $this->store($request->toArray());
                }else{
                    header("Status-Code: 400");
                    $response['status_code_header'] = 'HTTP/1.1 400 Bad Request';
                    $response['message']= 'missing perameter';
                    echo json_encode($response);
                }
            }

            //if the request is patch type
            elseif($request->isMethod('patch')){
                
                if(sizeof($request->toArray())>0){
                    $this->update($request->toArray());
                }
                //check if data available on request body
                else{
                    header("Status-Code: 400");
                    $response['status_code_header'] = 'HTTP/1.1 400 Bad Request';
                    $response['message']= 'missing perameter';
                    echo json_encode($response);
                }
            }
            else{
                //if request comes in a method which not allowed
                header("Status-Code: 405");
                $response['status_code_header'] = 'HTTP/1.1 405 Method Not Allowed';
                $response['message']= 'Method Not Allowed';
                echo json_encode($response);
            }
            
        }
        else{
             //if the request is get type and have a query string
            if($request->isMethod('get')){
                $this->showspecific($request->query('keys'));
            }
        }
        
    }

   

    public function store($request)
    {
      
       foreach($request as $key=>$val){
           if(!$this->redis->exists($key)){
            Redis::set($key, $val, 'EX', 300); 
           }
       }
       header("Status-Code: 201");
       header("Access-Control-Allow-Methods: POST");
       header("content-type: application/json; charset=UTF-8");
       $response['status_code_header'] = 'HTTP/1.1 201 OK';
       $response['message']= 'resource created';
       echo json_encode($response);
    }

    
    public function showall()
    {
        $response_arr=[];
        $keys=$this->redis->keys('*');
        foreach($keys as $key){
            $response_arr[$key]=$this->redis->get($key);
        }
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: GET");
        header("Status-Code: 200");
        header("Access-Control-Max-Age: 0");
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = ($response_arr);
        echo json_encode($response);
    }

    public function showspecific($query){
        $quesry_arr=explode(" ",$query);
        $response_arr=[];
        foreach($quesry_arr as $key){
            $response_arr[$key]=$this->redis->get($key);
        }
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: GET");
        header("Status-Code: 200");
        header("Access-Control-Max-Age: 0");
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = ($response_arr);
        echo json_encode($response);

    }
   
    public function update($request)
    {
        foreach($request as $key=>$val){
            if($this->redis->exists($key)){
                $this->redis->setex($key, 300, $val);
            }
        }
        header("Status-Code: 201");
        header("Access-Control-Allow-Methods: PATCH");
        header("content-type: application/json; charset=UTF-8");
        $response['status_code_header'] = 'HTTP/1.1 201 OK';
        $response['message']= 'resource updated';
        echo json_encode($response);
    }

}
