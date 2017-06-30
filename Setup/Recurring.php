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
namespace PlymDesign\CloseStore\Setup;

use Psr\Log\LoggerInterface;

use Magento\Framework\Setup\InstallSchemaInterface;

use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class Recurring implements InstallSchemaInterface
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
	protected $next_open_time = __DIR__ . '/..' . '/data/next_open_time.variable';
	protected $next_close_time = __DIR__ . '/..' . '/data/next_close_time.variable';
	protected $last_open_time = __DIR__ . '/..' . '/data/last_open_time.variable';
	protected $last_close_time = __DIR__ . '/..' . '/data/last_close_time.variable';

	/**
	 * Constructor for setting interfaces
	 *
	 * @param LoggerInterface $loggerInterface Logger Interface to be referenced and used
	 *
	 * @return void
	 */
	public function __construct(LoggerInterface $loggerInterface)
	{
		$this->logger = $loggerInterface;
	}

	/**
	 * Method called during each setup:upgrade
	 *
	 * Sets variables in \data\ accordingly
	 *
	 * @param SchemaSetupInterface $setup
	 * @param ModuleContextInterface $context
	 *
	 * @return void
	 */
	public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
		//Write to Debug Log
        $this->debug('Recurring for CloseStore executing...');

		//If the store should be opened (Default times)
		if(strtotime('NEXT WEDNESDAY 11:30') - strtotime('LAST SATURDAY 16:00') < strtotime('7 DAYS', 0))
		{
			//Save 'store_status' as false (Open store)
			$this->saveData($this->store_status, false);
		//If the store should be closed (Default times)
		}else{
			//Save 'store_status' as true (Close store)
			$this->saveData($this->store_status, true);
		}
		//Set 'last_open_time' to last Saturday 16:00 GMT
		$this->saveData($this->last_open_time, strtotime('LAST SATURDAY 16:00', strtotime('TOMORROW')));
		//Set 'next_open_time' to next Saturday 16:00 GMT
		$this->saveData($this->next_open_time, strtotime('NEXT SATURDAY 16:00', strtotime('YESTERDAY')));
		//Set 'last_close_time' to last Wednesday 11:30 GMT
		$this->saveData($this->last_close_time, strtotime('LAST WEDNESDAY 11:30', strtotime('TOMORROW')));
		//Set 'next_close_time' to next Wednesday 11:30 GMT
		$this->saveData($this->next_close_time, strtotime('NEXT WEDNESDAY 11:30', strtotime('YESTERDAY')));
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
