<?php
namespace HuiLib\Helper;

/**
 * 日期时间函数库
 *
 * @author 祝景法
 * @since 2013/10/27
 */
class DateTime
{
	const YMDHIS=1;
	const YMDHI=2;
	const MDHIS=3;
	const HI=4;
	const YMD=6;
	const TEXT_YMD=9;
	const WEEK=100;
	//农历
	const LUNAR_DAY=10;
	//友好模式
	const READABLE=1000;
	//每天的秒数
	const DAY_SECONDS=86400;

	public static function format($format = self::YMDHIS, $time = 0)
	{
		if (! $time) $time = time ();
		
		switch ($format) {
			case 1 :
				return date ( "Y-m-d H:i:s", $time );
				break;
			case 2 :
				return date ( "Y-m-d H:i", $time );
				break;
			case 3 :
				return date ( "m-d H:i:s", $time );
				break;
			case 4 :
				return date ( "H:i", $time );
				break;
			case 5 :
				if (date ( 'Y', time()) > date ( "Y", $time )) {
					return date ( "Y-m-d", $time ); // 为排版 前一年不显示具体时间
				} else {
					return date ( "m-d H:i", $time );
				}
				break;
			case 6 :
				return date ( "Y-m-d", $time );
				break;
			case 9 :
				return date ( "Y年m月d日", $time );
				break;
			case 10 :
				return \HuiLib\Helper\NongLi::getInstance()->S2L ( self::format ( $time, 6 ) );
				break;
			case 11 :
				return date ( "Y年n月j日G时", $time ); // 更新换头像动态，一小时内不重复。2012年7月15日5时
				break;
			case 100 :
				$weekarray = array ('天', '一', '二', '三', '四', '五', '六' );
				return '星期' . $weekarray [date ( 'w', $time )];
			case 101 :
				$weekarray = array ('天', '一', '二', '三', '四', '五', '六' );
				return '(' . $weekarray [date ( 'w', $time )] . ')';
			case 102 :
				if (date ( 'Y', time () ) > date ( "Y", $time )) {
					return date ( "Y年m月d日 H:i", $time );
				} else {
					return date ( "m月d日 H:i", $time );
				}
				break;
			case 1000 :
				$timeNow = time ();

				// 59秒前
				if ($timeNow - $time < 60) {
					return ($timeNow - $time) . '秒前';
				}
				
				// 1分钟前
				if ($timeNow - $time < 3600) {
					return floor ( ($timeNow - $time) / 60 ) . '分钟前';
				}
				
				// 1小时前
				if ($timeNow - $time < 86400) {
					return floor ( ($timeNow - $time) / 3600 ) . '小时前';
				}
				
				// 5天前
				if ($timeNow - $time < 86400 * 7) {
					return floor ( ($timeNow - $time) / 86400 ) . '天前';
				}
				
				return self::format ( 5, $time );
				break;
			default :
				return date ( "Y-m-d H:i:s", $time );
				break;
		}
	}
}