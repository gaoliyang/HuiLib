<?php
namespace HuiLib\Log\Test;

use HuiLib\Log\LogBase;

/**
 * 日志模块测试类
 *
 * @author 祝景法
 * @since 2013/11/10
 */
class LogTest extends \HuiLib\Test\TestBase
{
	public function run(){
		$this->testMysql();
	}
	
	private function testMysql(){
	    $logMysql=LogBase::getMysql()->setType(LogBase::TYPE_DAEMON)->setIdentify('Up201405')->add('Find some thing wrong.');
	    //支持批量插入 需要同一个日志实例
	    $logMysql->add('Find 1 thing wrong.');
	    $logMysql->add('Find 2 thing wrong.', FALSE);
	    $logMysql->add('Find 1 thing wrong.');
	    $logMysql->add('sorry, db falied');
	    $logMysql->add('sorry, db falied');
	    $logMysql->add('sorry, db falied');
	}
	
	private function testFile(){
	    $logFile=LogBase::getFile()->setType(LogBase::TYPE_DAEMON)->setIdentify('Up201405')->add('Find some thing wrong.');
	    //支持批量插入 需要同一个日志实例
	    $logFile->add('Find 1 thing wrong.');
	    $logFile->add('Find 2 thing wrong.');
	    $logFile->add('sorry, db falied', TRUE);
	    $logFile->add('sorry, db falied');
	    $logFile->add('sorry, db falied');
	}

	protected static function className(){
		return __CLASS__;
	}
}