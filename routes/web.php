<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
use Illuminate\Support\Facades\Cache;
Route::get('/', function () {
    // return view('welcome');
    $redis=app()->make('redis');
    $redis->set('key1','value matul');
    $redis->set('key2','value matul');
    //print_r( $redis->get('key1'));

});
Route::get('/matul',function(){
   // $redis = Redis::connection();
    // $redis->set('key1','value matul');
    // $redis->set('key2','value matul');
    Cache::put('key4', 'value', 30);
    $redis = Redis::connection();
    print_r( $redis->get('key4'));
   //Cache::put('key', 'value', $minutes);
   // print_r(Cache::get('key4'));
   // print_r(env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_database_'));
});
