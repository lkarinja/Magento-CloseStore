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
namespace PlymDesign\CloseStore\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

use Psr\Log\LoggerInterface;

use Magento\Framework\Message\ManagerInterface;

class UpdateObserver implements ObserverInterface
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
	 * Manager Interface for displaying messages
	 *
	 * @var ManagerInterface
	 */
	protected $managerInterface;

	/**
	 * Persistant variable files
	 */
	protected $store_status = __DIR__ . '/..' . '/data/status.variable';

	/**
	 * Constructor for setting interfaces
	 *
	 * @param LoggerInterface $loggerInterface Logger Interface to be referenced and used
	 * @param ManagerInterface $managerInterface Manager Interface to be referenced and used
	 *
	 * @return void
	 */
	public function __construct(
		LoggerInterface $loggerInterface,
		ManagerInterface $managerInterface
	){
		$this->logger = $loggerInterface;
		$this->managerInterface = $managerInterface;
	}

	/**
	 * Method called when event checkout_cart_update_items_after is triggered
	 *
	 * Determines if a user is trying to change an item to their cart during a period when the store should be closed
	 *
	 * @param Observer $observer Observer object passed
	 * 
	 * @return void
	 */
    public function execute(Observer $observer)
    {
		//Write to Debug Log
        $this->debug('Observer UpdateObserver executing');

		//If the store is set to closed
		if($this->loadData($this->store_status))
		{
			//Get the cart where items are being updated
			$cart = $observer->getCart();
			//Get all items in the cart
			$items = $cart->getItems();
			//For all items in the cart...
			foreach($items as $item)
			{
				//Get the item's ID
				$itemId = $item->getItemId();
				//Remove that item from the cart, then save the cart
				$cart->removeItem($itemId)->save();
			}

			//Clear the cart and save it (So the error message can successfully be displayed)
			$cart->truncate();
			$cart->save();

			//Alert customer that they cannot add items to their cart
			$this->managerInterface->addErrorMessage("The store is closed, so you cannot use your shopping cart");
		}
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
