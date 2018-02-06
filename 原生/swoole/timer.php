<?php
	
	/*
		swoole_timer_tick 函数就相当于setInterval，是持续触发的
		swoole_timer_after 函数相当于setTimeout，仅在约定的时间触发一次
		swoole_timer_tick和swoole_timer_after函数会返回一个整数，表示定时器的ID
		可以使用 swoole_timer_clear 清除此定时器，参数为定时器ID
	*/


	//每隔2000ms触发一次 （定时器）
	swoole_timer_tick(2000, function ($timer_id) {
	    echo "tick-2000ms\n";
	    echo $timer_id."\n";
	    echo "\n";
	});

	swoole_timer_tick(2000, function () {
	    echo "tick-2000ms\n";
	    echo "\n";
	});

	swoole_timer_tick(2000, function ($timer_id) {
	    echo "tick-2000ms\n";
	    echo $timer_id."\n";
	    echo "\n";
	    //清除id为3的定时器
		swoole_timer_clear($timer_id);
	});

	//3000ms后执行此函数 (一次性定时器)
	swoole_timer_after(3000, function () {
	    echo "after 3000ms.\n";
	    echo "\n";
	});

	

?>