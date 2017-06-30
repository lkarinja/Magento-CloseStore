<?php

namespace PlymDesign\CloseStore\Cron;

use Psr\Log\LoggerInterface;

use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Cache\Frontend\Pool;

class OverrideStoreStatus
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
	protected $close_override = __DIR__ . '/..' . '/data/close_override.variable';
	protected $next_open_time = __DIR__ . '/..' . '/data/next_open_time.variable';
	protected $next_close_time = __DIR__ . '/..' . '/data/next_close_time.variable';
	protected $last_open_time = __DIR__ . '/..' . '/data/last_open_time.variable';
	protected $last_close_time = __DIR__ . '/..' . '/data/last_close_time.variable';

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
	 * Method called when the cron is run to override the status of the store
	 *
	 * Determine if there is a CSV file available
	 * If yes, determine the proper time to Open and Close the store relative to the specified override times
	 * Open or Close the store if the current time meets the override time
	 *
	 * Cron time should be '*\/5 * * * *' (Note that the slash is escaped for the PHP doc)
	 * Every 5th minute of the hour, any hour of the day, any day of the month, any month of the year, any year
	 * This will execute every 5 minutes
	 *
	 * @return StoreStatus $this
	 */
    public function execute()
    {
		//Write to Debug Log
        $this->debug('Cron OverrideStoreStatus executing');

		//Path to where the override file should be (Top directory of module)
		$override_file_path = realpath(__DIR__ . '/..' . '/OVERRIDE.csv');

		//If a CSV override file is found
		if(file_exists($override_file_path))
		{
			//Read contents of the CSV into an array
			$csv_data = array_map('str_getcsv', file($override_file_path));

			//Prepare arrays open_times and close_times to be populated
			$open_times = array();
			$close_times = array();

			//Get data from the CSV and populate the arrays open_times and close_times 
			foreach($csv_data as $csv_data_times)
			{
				array_push($open_times, $csv_data_times[0]);
				array_push($close_times, $csv_data_times[1]);
			}

			//Get the current time as a timestamp
			$current_time = strtotime('NOW');

			//Sort the times
			asort($open_times);
			asort($close_times);

			//Set locking variables
			$has_open = false;
			$has_close = false;

			//For all times found in open_times
			foreach($open_times as $open_time)
			{
				//Parse the time as a timestamp and convert to GMT from EST
				$open_timestamp = strtotime($open_time . '+4 HOURS');

				//If there is an override time for Opening the store for the current week
				//Check if date is past the last_close_time and before next_close_time and there is not already an override open time
				if($this->loadData($this->last_close_time) < $open_timestamp && $open_timestamp < $this->loadData($this->next_close_time) && !$has_open)
				{
					//Save 'open_override' as true (There is an override on the open time)
					$this->saveData($this->open_override, true);
					$has_open = true;

					//If the current time is at or after the override Open time
					if($current_time >= $open_timestamp)
					{
						//If the store is not opened yet (Store is closed)
						if($this->loadData($this->store_status))
						{
							//Save 'store_status' as false (Open store)
							$this->saveData($this->store_status, false);

							//Save 'last_open_time' as current time
							$this->saveData($this->last_open_time, strtotime('NOW'));

							//Save 'next_open_time' as next default open time
							$this->saveData($this->next_open_time, strtotime('NEXT SATURDAY 16:00'));

							//Allow more open overrides for multiple overrides in the same week
							$has_open = false;

							//Clear cache
							$this->clearCache();

							$this->debug('Store closed status override to false (Store is Opened)');
						//If the store was already opened
						}else{
							$this->debug('Store is already opened');
						}
					}else{
						//Save 'next_open_time' as override open time
						$this->saveData($this->next_open_time, $open_timestamp);
					}
				//If there is not an override time for Opening the store for the current week
				}else{
					if(!$has_open){
						//Save 'open_override' as false (There is not an override on the open time for the current week)
						$this->saveData($this->open_override, false);
					}
				}
			}

			//For all times found in close_times
			foreach($close_times as $close_time)
			{
				//Parse the time as a timestamp and convert to GMT from EST
				$close_timestamp = strtotime($close_time . '+4 HOURS');

				//If there is an override time for Closing the store for the current week
				//Check if date is past the last_open_time and before next_open_time and there is not already an override close time
				if($this->loadData($this->last_open_time) < $close_timestamp && $close_timestamp < $this->loadData($this->next_open_time) && !$has_close)
				{
					//Save 'close_override' as true (There is an override on the close time)
					$this->saveData($this->close_override, true);
					$has_close = true;

					//If the current time is at or after the override Close time
					if($current_time >= $close_timestamp)
					{
						//If the store is not closed yet (Store is open)
						if(!$this->loadData($this->store_status))
						{
							//Save 'store_status' as true (Close store)
							$this->saveData($this->store_status, true);

							//Save 'last_close_time' as current time
							$this->saveData($this->last_close_time, strtotime('NOW'));

							//Save 'next_close_time' as next default close time
							$this->saveData($this->next_close_time, strtotime('NEXT WEDNESDAY 11:30'), $this->loadData($this->last_open_time));

							//Allow more close overrides for multiple overrides in the same week
							$has_close = false;

							//Clear cache
							$this->clearCache();

							$this->debug('Store closed status override to true (Store is Closed)');
						//If the store was already closed
						}else{
							$this->debug('Store is already closed');
						}
					}else{
						//Save 'next_close_time' as override close time
						$this->saveData($this->next_close_time, $close_timestamp);
					}
				//If there is not an override time for Closing the store for the current week
				}else{
					if(!$has_close){
						//Save 'close_override' as false (There is not an override on the close time for the current week)
						$this->saveData($this->close_override, false);
					}
				}
			}
		//If there was no CSV override file found
		}else{
			$this->debug('Override file does not exist');
			//Save 'close_override' as false
			$this->saveData($this->close_override, false);
			//Save 'open_override' as false
			$this->saveData($this->open_override, false);
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