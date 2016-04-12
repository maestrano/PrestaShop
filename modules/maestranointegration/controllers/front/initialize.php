<?php
/*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @since 1.5.0
 */

// define('DATA_SEQUENCE_FILE', realpath('../../var/_data_sequence'));

class MaestranointegrationInitializeModuleFrontController extends ModuleFrontController
{
	/**
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
		parent::initContent();

		set_time_limit(0);

		// Last update timestamp
		$timestamp = $this->lastDataUpdateTimestamp();
		$date = date('c', $timestamp);
		$current_timestamp = round(microtime(true));

		error_log("Fetching data updates since $date");

		// Fetch updates
		$client = new Maestrano_Connec_Client();
		$subscriptions = Maestrano::param('webhook.connec.subscriptions');
		foreach ($subscriptions as $entity => $enabled) {
			if(!$enabled) { continue; }

			// Fetch first page of entities since last update timestamp
			$params = array("\$filter" => "updated_at gte '$date'");
			$result = $this->fetchData($client, $entity, $params);
			// Fetch next pages
			while(array_key_exists('pagination', $result) && array_key_exists('next', $result['pagination'])) {
				$result = $this->fetchData($client, $result['pagination']['next']);
			}
		}

		// Set update timestamp
		$this->setLastDataUpdateTimestamp($current_timestamp);
	}

	// Read the last update timestamp
	private function lastDataUpdateTimestamp() {
		$timestamp = $this->openAndReadFile('/var/lib/prestashop/webapp/modules/maestranointegration/var/_data_sequence');
		return empty($timestamp) ? 0 : $timestamp;
	}

	// Update the update timestamp
	private function setLastDataUpdateTimestamp($timestamp) {
		file_put_contents('/var/lib/prestashop/webapp/modules/maestranointegration/var/_data_sequence', $timestamp);
	}

	// Open or create a file and returns its content
	private function openAndReadFile($file_path) {
		if(!file_exists($file_path)) {
			$fp = fopen($file_path, "w");
			fwrite($fp,"");
			fclose($fp);
		}
		return file_get_contents($file_path);
	}

	// Fetches and import data from specified entity
	function fetchData($client, $entity, $params=array()) {
		$msg = $client->get($entity, $params);
		$code = $msg['code'];
		$body = $msg['body'];

		if($code != 200) {
			error_log("Cannot fetch connec entities=$entity, code=$code, body=$body");
			return array();
		} else {
			error_log("Received entities=$entity, code=$code");
			$result = json_decode($body, true);
			// Dynamically find mappers and map entities
			foreach(BaseMapper::getMappers() as $mapperClass) {
				if (class_exists($mapperClass)) {
					$test_class = new ReflectionClass($mapperClass);
					if($test_class->isAbstract()) { continue; }

					$mapper = new $mapperClass();
					if(array_key_exists($mapper->getConnecResourceName(), $result)) {
						$mapper->persistAll($result[$mapper->getConnecResourceName()]);
					}
				}
			}

			return $result;
		}
	}
}
