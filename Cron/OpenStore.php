<?php

namespace PlymDesign\CloseStore\Cron;

use Psr\Log\LoggerInterface;

use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Cache\Frontend\Pool;

class OpenStore
{

	/**
	 * Determine whether to write to debug log
	 *
	 * @var bool
	 */
	private $use_debug = true;

	/**
	 * Logger Interface for writing to log files in \var\log\
	 *
	 * @var LoggerInterface
	 */
	protected $logger;

	/**
	 * Persistant variable files
	 */
	protected $store_status = __DIR__ . '/..' . '/data/status.variable';
	protected $open_override = __DIR__ . '/..' . '/data/open_override.variable';
	protected $last_open_time = __DIR__ . '/..' . '/data/last_open_time.variable';
	protected $next_open_time = __DIR__ . '/..' . '/data/next_open_time.variable';

	/**
	 * Type List Interface for clearing cache types
	 *
	 * @var TypeListInterface
	 */
	protected $cacheTypeList;

	/**
	 * Frontend Pool Object for cleaning store cache
	 *
	 * @var Pool
	 */
	protected $cacheFrontendPool;

	/**
	 * Constructor for setting interfaces
	 *
	 * @param LoggerInterface $loggerInterface Logger Interface to be referenced and used
	 * @param TypeListInterface $cacheTypeList Type List Interface to be referenced and used
	 * @param Pool $cacheFrontendPool Frontend Pool Object to be referenced and used
	 *
	 * @return void
	 */
	public function __construct(
		LoggerInterface $loggerInterface,
		TypeListInterface $cacheTypeList,
		Pool $cacheFrontendPool
	){
		$this->logger = $loggerInterface;
		$this->cacheTypeList = $cacheTypeList;
		$this->cacheFrontendPool = $cacheFrontendPool;
	}

	/**
	 * Method called when the cron is run to open the store
	 *
	 * Opens the store at 12:00 EST every Saturday and sets global variables accordingly
	 *
	 * Cron time should be '0 16 * * 6 *'
	 * :00 minute, 16th hour, any day of the month, any month of the year, Saturday, any year
	 * This will execute every Saturday at 16:00 GMT (12:00 EST)
	 *
	 * @return OpenStore $this
	 */
    public function execute()
    {
		//Write to Debug Log
        $this->debug('Cron OpenStore executing');

		//If there is no override time for the store to be opened
		if(!$this->loadData($this->open_override))
		{
			//If the store is not opened yet (Store is closed)
			if($this->loadData($this->store_status))
			{
				//Save 'store_status' as false (Open store)
				$this->saveData($this->store_status, false);

				//Save 'last_open_time' as current time
				$this->saveData($this->last_open_time, strtotime('NOW'));

				//Save 'next_open_time' as 7 days from now
				$this->saveData($this->next_open_time, strtotime('NOW +7 DAYS'));

				//Clear cache
				$this->clearCache();

				$this->debug('Store closed status is set to false (Store is Opened)');
			//If the store was already opened
			}else{
				$this->debug('Store is already opened');
			}
		//If there is an override file
		}else{
			$this->debug('An override time has been found, and the store will not be opened');
		}
		//Finish the cronjob
		return $this;
	}

	/**
	 * Load variable from file
	 */
	protected function loadData($file){
		$result = json_decode(file_get_contents(realpath($file)), true);
		//$this->debug('Loaded ' . json_encode($result) . ' from file ' . basename($file));
		return $result;
	}

	/**
	 * Save variable to file
	 */
	protected function saveData($file, $data){
		//$this->debug('Writing ' . json_encode($data) . ' to file ' . basename($file));
		return file_put_contents(realpath($file), json_encode($data));
	}

	/**
	 * Clear Magento cache
	 */
	protected function clearCache(){
		$types = array('config','layout','block_html','collections','reflection','db_ddl','eav','config_integration','config_integration_api','full_page','translate','config_webservice');
		foreach ($types as $type) {
			$this->cacheTypeList->cleanType($type);
		}
		foreach ($this->cacheFrontendPool as $cacheFrontend) {
			$cacheFrontend->getBackend()->clean();
		}
	}

	/**
	 * Method for writing to /var/log/debug.log
	 *
	 * If and only if $use_debug is true, write to log
	 *
	 * @param string $data Data to write to log
	 *
	 * @return bool true if data was logged, false if data was not logged
	 */
	private function debug($data){
		if($this->use_debug == true){
			$this->logger->debug($data);
			return true;
		}else{
			return false;
		}
	}
}