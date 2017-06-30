<?php

namespace PlymDesign\CloseStore\Plugin;

use Psr\Log\LoggerInterface;

use Magento\Framework\View\LayoutFactory;

use Magento\Framework\App\Response\Http as ResponseHttp;

use Magento\Framework\App\State;

use Magento\Framework\Event\Manager;

class PageDispatch
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
	 * Layout Factory for creating a new layout to render blocks to
	 *
	 * @var LayoutFactory
	 */
	protected $layoutFactory;

	/**
	 * HTTP Response for changing HTML on a loaded page
	 *
	 * @var ResponseHttp
	 */
	protected $response;

	/**
	 * State Object for getting the site location of where a request was made
	 *
	 * @var State
	 */
	protected $state;

	/**
	 * Persistant variable files
	 */
	protected $store_status = __DIR__ . '/..' . '/data/status.variable';

	/**
	 * Constructor for setting interfaces
	 *
	 * @param LoggerInterface $loggerInterface Logger Interface to be referenced and used
	 * @param LayoutFactory $layoutFactory Layout Factory to be referenced and used
	 * @param ResponseHttp $response HTTP Response to be referenced and used
	 * @param State $state State Object to be referenced and used
	 *
	 * @return void
	 */
	public function __construct(
		LoggerInterface $loggerInterface,
		LayoutFactory $layoutFactory,
		ResponseHttp $response,
		State $state
	){
		$this->logger = $loggerInterface;
		$this->layoutFactory = $layoutFactory;
		$this->response = $response;
		$this->state = $state;
	}

	/**
	 * Method called before \Magento\Framework\Event\Manager::dispatch() is called
	 *
	 * Intercepts any event dispatch, determines if the event is a page load, and displays a notification if the store is closed
	 *
	 * @param Manager $subject Manager object passed that contains the intercepted method
	 * @param string $name Default parameter from Manager::dispatch()
	 * @param array $data Default parameter from Manager::dispatch()
	 *
	 * @return void
	 */
	public function beforeDispatch(Manager $subject, $name, array $data = [])
	{
		//If the dispatched event is a layout_load_before event and comes from the frontend
		if($name == 'layout_load_before' && $this->state->getAreaCode() == 'frontend')
		{
			//Write to Debug Log
			$this->debug('Plugin PageDispatch executing');

			//If the store is set to closed
			if($this->loadData($this->store_status))
			{
				//Create a new block for messages to be rendered
				$block = $this->layoutFactory->create()->getMessagesBlock();

				//Alert customer that the store is closed
				$block->addNotice("NOTICE! The store is closed, so you will not be able to order any items!");

				//Get the message block as HTML
				$messageHtml = $block->getGroupedHtml();

				//Add the HTML to the loaded page
				$this->response->appendBody($messageHtml);
			}
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