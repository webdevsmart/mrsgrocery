<?php

/**
 * @property MsAttribute $MsAttribute
 * @property MsBadge $MsBadge
 * @property MsBalance $MsBalance
 * @property MsCategory $MsCategory
 * @property MsCommission $MsCommission
 * @property MsConversation $MsConversation
 * @property MsCoupon $MsCoupon
 * @property MsCustomField $MsCustomField
 * @property MsFile $MsFile
 * @property MsFilter $MsFilter
 * @property MsHelper $MsHelper
 * @property MsImportExportData $MsImportExportData
 * @property MsImportExportFile $MsImportExportFile
 * @property MsMail $MsMail
 * @property MsMessage $MsMessage
 * @property MsOption $MsOption
 * @property MsOrderData $MsOrderData
 * @property MsPgPayment $MsPgPayment
 * @property MsPgRequest $MsPgRequest
 * @property MsProduct $MsProduct
 * @property MsQuestion $MsQuestion
 * @property MsReport $MsReport
 * @property MsReturn $MsReturn
 * @property MsReview $MsReview
 * @property MsSeller $MsSeller
 * @property MsSellerGroup $MsSellerGroup
 * @property MsSetting $MsSetting
 * @property MsShippingMethod $MsShippingMethod
 * @property MsSocialLink $MsSocialLink
 * @property MsStatistic $MsStatistic
 * @property MsSuborder $MsSuborder
 * @property MsSuborderStatus $MsSuborderStatus
 * @property MsTransaction $MsTransaction
 * @property MsValidator $MsValidator
 */
class MsLoader
{
	public $appVer = "8.14.2";
	public $dbVer = "2.11.1.0";
	public $langVer = "1.4.2.0";

	/**
     * Lookup for shared class instances
     *
     * @var array
     */
    public $instances = array();

    /** @var Registry */
    protected $registry;

    /** @var MsLoader */
    private static $instance;

    private function __construct()
    {
        spl_autoload_register(array('MsLoader', '_autoloadLibrary'));
        spl_autoload_register(array('MsLoader', '_autoloadController'));
        spl_autoload_register(array('MsLoader', '_autoloadMultimerchlib'));
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __get($class)
    {
        return $this->load($class);
    }

    public function setRegistry(Registry $registry)
    {
        $this->registry = $registry;
        return $this;
    }

    public function getRegistry()
    {
        return $this->registry;
    }

	/**
	 * Load MultiMerch class with namespace support
	 *
	 * @param $className
	 * @param bool $shareInstance Share existing instance or create new
	 * @return Object
	 */
	public function load($className, $shareInstance = true)
	{
		$cname = \MultiMerch\Stdlib\CName::canonicalizeName($className);
		if (!$shareInstance) {
			$instance = new $className($this->registry);
		} else {
			if (isset($this->instances[$cname])) {
				$instance = $this->instances[$cname];
			} else {
				$instance = new $className($this->registry);
				$this->instances[$cname] = $instance;
			}
		}

		return $instance;
	}

	private static function _autoloadLibrary($class)
	{
		$file = DIR_SYSTEM . 'library/' . strtolower($class) . '.php';
		if (file_exists($file)) {
			require_once(VQMod::modCheck(modification($file), $file));
		} else {
			$file = DIR_SYSTEM . 'library/multimerch/' . strtolower($class) . '.php';
			if (file_exists($file)) {
				require_once(VQMod::modCheck(modification($file), $file));
			}
		}
	}

	private static function _autoloadController($class)
	{
		preg_match_all('/((?:^|[A-Z])[a-z]+)/', $class, $matches);

		if (isset($matches[0][1]) && isset($matches[0][2])) {
			$file = DIR_APPLICATION . 'controller/' . strtolower($matches[0][1]) . '/' . strtolower($matches[0][2]) . '.php';
			if (file_exists($file)) {
				require_once(VQMod::modCheck(modification($file), $file));
			}
		}
	}

	private static function _autoloadMultimerchlib($class)
	{
		if (strpos($class, '\\') === false) {
			return false; // no namespace used
		}
		$class = str_replace('\\', '/', strtolower($class));
		if (strpos($class, 'multimerch') !== 0) {
			return false; // not a MultiMerch\ class
		}

		$class = mb_substr($class, mb_strlen('multimerch/'));
		$file = DIR_SYSTEM . 'vendor/multimerchlib/' . $class . '.php';

		if (is_file($file)) {
			include_once(\VQMod::modCheck(modification($file), $file));
			return true;
		} else {
			return false;
		}
	}
}
