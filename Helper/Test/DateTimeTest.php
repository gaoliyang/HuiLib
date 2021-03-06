<?php
namespace HuiLib\Helper\Test;

use HuiLib\Helper\DateTime;

/**
 * 数据库测试类
 *
 * @author 祝景法
 * @since 2013/10/27
 */
class DateTimeTest extends \HuiLib\Test\TestBase
{
	public function run(){
		$this->test();
	}
	
	private function test(){
		$this->assert(DateTime::format(DateTime::YMDHIS, time()), '2014-05-25 22:49:06');
	}
	
	protected static function className(){
		return __CLASS__;
	}
}