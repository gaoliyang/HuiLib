<?php
namespace HuiLib\Lang\Test;

/**
 * 翻译类测试类
 *
 * @author 祝景法
 * @since 2013/08/25
 */
class LangTest extends \HuiLib\Test\TestBase
{
	public function run(){
		$this->test();
	}
	
	private function test(){
		$lang=\HuiLib\Lang\LangBase::getHuiLibLang();
		//测试时具体写入子串
		//params:Array ( [0] => HuiLib.lang.test [1] => 芸临网 [2] => hanhui [3] => 355 [4] => 3.55 ) 
		echo $lang->translate('HuiLib.lang.test', '芸临网', 'hanhui', 1000, 3.55000);
	}

	protected static function className(){
		return __CLASS__;
	}
}