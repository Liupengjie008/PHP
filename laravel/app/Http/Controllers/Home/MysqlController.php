<?php

namespace App\Http\Controllers\Home;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
class MysqlController extends Controller
{
    public function index(){
    	$user = DB::select('select * from laravel where id = ?', [2]);
		// dd($user);
		$user3 = DB::select('select * from laravel where id = :id', [':id'=>4]);
		dd($user,$user3);
    }

    public function add(){
    	DB::insert('insert into laravel (id, name, email, password) values (?, ?, ? , ? )',
        [4, 'Laravel','laravel4@test.com','123']);
    	DB::insert('insert into laravel (id, name, email, password) values (?, ?, ?, ? )',
        [5, 'Academy','academy5@test.com','123']);

    }

    public function update(){
    	$affected = DB::update('update laravel set name="LaravelAcademy" where name = ?', ['Academy']);
		echo $affected;
    }

    public function del(){
    	$deleted = DB::delete('delete from laravel where id = 2');
		echo $deleted;
    }

    public function drop_table(){
    	die;
    	DB::statement('drop table laravel');
    }

    /**
	* 启动所有应用服务
	*
	* @return void
	*/
	public function boot()
	{	
		DB::insert('insert into laravel (id, name, email, password) values (?, ?, ? , ? )',[8, 'LaravelAcademy','laravel-academy8@test.com','123']);
	    DB::listen(function($sql, $bindings, $time) {
	        echo 'SQL语句执行：'.$sql.'，参数：'.json_encode($bindings).',耗时：'.$time.'ms';
	    });
	}

	
    
}
