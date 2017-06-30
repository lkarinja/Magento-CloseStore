<?php
/*
Copyright © 2017 Leejae Karinja

This file is part of CloseStore.

CloseStore is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

CloseStore is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with CloseStore.  If not, see <http://www.gnu.org/licenses/>.
*/
namespace PlymDesign\CloseStore\Cron;

use Psr\Log\LoggerInterface;

use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Cache\Frontend\Pool;

class CloseStore
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
	protected $close_override = __DIR__ . '/..' . '/data/close_override.variable';
	protected $last_close_time = __DIR__ . '/..' . '/data/last_close_time.variable';
	protected $next_close_time = __DIR__ . '/..' . '/data/next_close_time.variable';

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
	 * Method called when the cron is run to close the store
	 *
	 * Closes the store at 07:30 EST every Wednesday and sets global variables accordingly
	 *
	 * Cron time should be '30 11 * * 3 *'
	 * :30 minute, 11th hour, any day of the month, any month of the year, Wednesday, any year
	 * This will execute every Wednesday at 11:30 GMT (7:30 EST)
	 *
	 * @return CloseStore $this
	 */
    public function execute()
    {
		//Write to Debug Log
        $this->debug('Cron CloseStore executing');

		//If there is no override time for the store to be closed
		if(!$this->loadData($this->close_override))
		{
			//If the store is not closed yet (Store is open)
			if(!$this->loadData($this->store_status))
			{
				//Save 'store_status' as true (Close store)
				$this->saveData($this->store_status, true);

				//Save 'last_close_time' as current time
				$this->saveData($this->last_close_time, strtotime('NOW'));

				//Save 'next_close_time' as 7 days from now
				$this->saveData($this->next_close_time, strtotime('NOW +7 DAYS'));

				//Clear cache
				$this->clearCache();

				$this->debug('Store closed status is set to true (Store is Closed)');
			//If the store was already closed
			}else{
				$this->debug('Store is already closed');
			}
		//If there is an override file
		}else{
			$this->debug('An override time has been found, and the store will not be closed');
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
