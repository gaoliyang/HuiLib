<?php
namespace HuiLib\App;

/**
 * 应用创建文件
 *
 * @author 祝景法
 * @since 2013/08/11
 */
abstract class AppBase
{
	/**
	 * 引导程序
	 * @var \HuiLib\Bootstrap
	 */
	protected $bootStrap;
	
	/**
	 * 请求对象
	 * @var \HuiLib\Request\RequestBase
	 */
	protected $requestInstance;
	
	/**
	 * 响应内容
	 */
	protected $responce;
	
	/**
	 * 运行配置
	 * @var \HuiLib\Config\ConfigBase
	 */
	protected $appConfig;
	protected $configPath;
	
	/**
	 * 网站全局配置 定位原来数据的setting表
	 * @var \HuiLib\Config\ConfigBase
	 */
	protected $siteConfigInstance=NULL;
	
	/**
	 * 数据库连接
	 *  @var \HuiLib\Db\DbBase
	 */
	protected $dbInstance=NULL;
	
	/**
	 * session实例
	 *  @var \HuiLib\Session\SessionBase
	 */
	protected $sessionInstance=NULL;
	
	/**
	 * 翻译国际化功能
	 * 
	 *  @var \HuiLib\Lang\LangBase 
	 */
	protected $langInstance=NULL;

	/**
	 * 构造函数
	 * 
	 * @param string $config 配置文件路径
	 * @throws \Exception
	 */
	protected function __construct($config)
	{
		if (! is_file ( $config )) {
			throw new \Exception ( "Please specify a accessable config file!" );
		}
		$this->configPath = $config;
		
		$this->bootStrap = \HuiLib\Bootstrap::getInstance ();
		
		//初始化应用配置
		$this->initConfig ();
		
		//php运行配置
		$this->initPhpSetting ();
		
		//自动加载配置
		$this->initAutoLoad ();
		
		//初始化请求对象 具体可能在子类初始化
		$this->initRequest ();
	}

	/**
	 * 应用执行入口
	 */
	public function run()
	{
		//初始化Session Test需要独立初始化
		$this->initSession();
		
		$this->requestInstance->init();
		
		if (method_exists($this->requestInstance->controllerInstance(), 'dispatch')) {
			$this->requestInstance->controllerInstance()->dispatch();
		}else{
			throw new \HuiLib\Error\Exception ( "AppBase::run controller dispatch failed!" );
		}
		
		$this->requestInstance->controllerInstance()->output();
	}

	/**
	 * 测试执行入口
	 */
	public function runTest()
	{
		$queryString = \HuiLib\Helper\Param::getQueryString ();
		
		//初始化测试库
		$instance = $queryString::getInstance ();
		$instance->setApp ( $this );
		
		//执行
		$instance->run ();
	}

	/**
	 * 初始化启动配置
	 */
	protected function initConfig()
	{
		$this->appConfig = new \HuiLib\Config\ConfigBase ( $this->configPath );
	}

	/**
	 * 返回配置实例
	 * @return \HuiLib\Config\ConfigBase
	 */
	public function configInstance()
	{
		return $this->appConfig;
	}
	
	/**
	 * 返回网站配置实例
	 * 
	 * @return \HuiLib\Config\ConfigBase
	 */
	public function siteConfigInstance()
	{
		if ($this->siteConfigInstance===NULL) {
			$siteIni=$this->appConfig->getByKey('app.global');
			$this->siteConfigInstance = new \HuiLib\Config\ConfigBase ( $siteIni );
		}
		
		return $this->siteConfigInstance;
	}
	
	/**
	 * 返回启动器实例
	 * @return \HuiLib\Bootstrap
	 */
	public function bootstrapInstance()
	{
		return $this->bootStrap;
	}

	/**
	 * 初始化请求
	 * 
	 * 先在之类初始化请求，然后父类初始化配置
	 */
	protected abstract function initRequest();
	
	/**
	 * 返回请求对象实例
	 * @return \HuiLib\Request\RequestBase
	 */
	public function requestInstance()
	{
		return $this->requestInstance;
	}

	/**
	 * 获取应用所在的命名空间
	 */
	public function getAppNamespace()
	{
		$appNamespace=$this->appConfig->getByKey ( 'app.namespace' );
		
		if (empty($appNamespace) || !is_string($appNamespace)) {
			throw new \HuiLib\Error\Exception ( "配置文件应用命名空间{app.namespace}配置错误" );
		}
		
		return $appNamespace;
	}

	/**
	 * 初始化Session资源
	 */
	protected function initSession()
	{
		$this->sessionInstance=\HuiLib\Session\SessionBase::create($this->configInstance ());
	}
	
	/**
	 * 返回session对象实例
	 * @return \HuiLib\Session\SessionBase
	 */
	public function sessionInstance()
	{
		return $this->sessionInstance;
	}

	/**
	 * 初始化错误处理器
	 */
	protected function initErrorHandle()
	{
	}

	/**
	 * 初始化异常处理器
	 */
	protected function initExceptionHandle()
	{
	}

	/**
	 * 初始化错误处理器
	 */
	final protected function initPhpSetting()
	{
		$settings = $this->appConfig->mergeKey ( $this->appConfig->getByKey ( 'phpSettings' ) );
		
		foreach ( $settings as $key => $value ) {
			ini_set ( $key, $value );
		}
		
		return true;
	}

	/**
	 * 将配置文件中的autoLoad项目注册到load空间
	 */
	protected function initAutoLoad()
	{
		$loads = $this->appConfig->getByKey ( 'autoLoad' );
		
		$loader = $this->bootStrap->autoLoaderInstance ();
		foreach ( $loads as $name => $path ) {
			$loader->addSpace ( $name, $path );
		}
		
		return true;
	}

	/**
	 * 执行期末绑定
	 */
	protected function initShutCall()
	{
		$this->shutCall = \HuiLib\Runtime\ShutCall::getInstance ();
	}

	public function shutCallInstance()
	{
		return $this->shutCall;
	}

	/**
	 * 初始化性能记录器
	 */
	protected function initProfile()
	{
	}

	/**
	 * 初始化应用类
	 * 
	 * @return \HuiLib\App\AppBase
	 */
	public static function factory($runMethod, $config)
	{
		$appClass = '\\HuiLib\\App\\' . ucfirst ( $runMethod );
		$appInstance = new $appClass ( $config );
		return $appInstance;
	}
}
