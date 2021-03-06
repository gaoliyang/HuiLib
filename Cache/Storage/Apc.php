<?php
namespace HuiLib\Cache\Storage;

/**
 * Apc基础类
 *
 * @author 祝景法
 * @since 2013/09/15
 */
class Apc extends \HuiLib\Cache\CacheBase
{
	/**
	 * Apc库键前缀 防止多应用实例名称冲突
	 *
	 * 通过prefix隔离命名空间
	 *
	 * @var string
	 */
	private $prefix='';
	
	protected function __construct($config)
	{
		if (!extension_loaded('apc')) {
			throw new \HuiLib\Error\Exception ( "PHP环境并不支持Apc拓展，请安装后继续" );
		}
		
		$this->config=$config;
		if (!empty( $config['prefix'] )) {
			$this->prefix= $config['prefix'];
		}
	}
	
	/**
	 * 强制添加一个缓存
	 *
	 * 修改为: 强制设置，强制过期
	 *
	 * @param string $key 缓存键
	 * @param mix $value 缓存值
	 * @param int $expire 过期时间，0永不过期
	 */
	public function add($key, $value,$expire=0)
	{
		return apc_store($this->prefix.$key, $value, $expire);
	}
	
	/**
	 * 添加一个新的缓存
	 *
	 * 仅在缓存变量不存在的情况下缓存变量到数据存储中
	 *
	 * @param string $key 缓存键
	 * @param mix $value 缓存值
	 * @param int $expire 过期时间，0永不过期
	 */
	public function addnx($key, $value,$expire=0)
	{
		return apc_add($this->prefix.$key, $value, $expire);
	}
	
	/**
	 * 删除一个缓存
	 *
	 * @param string $key 缓存键
	 */
	public function delete($key)
	{
		return apc_delete($this->prefix.$key);
	}
	
	/**
	 * 获取一个缓存内容
	 *
	 * @param string $key 缓存键，支持多键
	 * @return mix or false not exist
	 */
	public function get($key)
	{
		return apc_fetch($this->prefix.$key);
	}
	
	/**
	 * 给缓存值加上一个数
	 *
	 * @param string $key 缓存键
	 * @param mix $value 增加的值
	 */
	public function increase($key, $value=1){
		return apc_inc($this->prefix.$key, $value);
	}
	
	/**
	 * 给缓存值减去一个数
	 *
	 * @param string $key 缓存键
	 * @param mix $value 减少的值
	 */
	public function decrease($key, $value=1){
		return apc_dec($this->prefix.$key, $value);
	}
	
	/**
	 * 清空所有数据
	 *
	 */
	public function flush(){
		return $this->connect->flush();
	}
	
	public function toString(){
		return 'apc';
	}
}