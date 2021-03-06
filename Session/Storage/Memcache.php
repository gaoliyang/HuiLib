<?php 
namespace HuiLib\Session\Storage;

/**
 * Session Memcache实现
 * 
 * Session通过prefix隔离命名空间
 * 
 * @author 祝景法
 * @since 2013/09/27
 */
class Memcache extends \HuiLib\Session\SessionBase
{
	public function open ( $savePath , $name )
	{
		parent::open($savePath, $name);
		
		return true;
	}
	
	public function read ( $sessionId )
	{
		parent::read($sessionId);
		return $this->driver->get(self::$prefix.$sessionId);
	}
	
	public function write ( $sessionId , $sessionData )
	{
		parent::write($sessionId, $sessionData);
		return $this->driver->add(self::$prefix.$sessionId, $sessionData, FALSE, $this->lifeTime);
	}
	
	public function close ()
	{
		parent::close();
		
		return true;
	}
	
	public function destroy ( $sessionId )
	{
		parent::destroy($sessionId);
		
		return $this->delete($sessionId);
	}
	
	/**
	 * 删除session实体数据接口
	 */
	public function delete ( $sessionId )
	{
		return $this->driver->delete(self::$prefix.$sessionId);
	}
}