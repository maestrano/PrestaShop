<?php

/**
* Map Connec SalesOrder representation to/from Prestashop SalesOrder
*/

class OrderStatusUpdateMapper extends BaseMapper {


	public function __construct() 
	{
		parent::__construct();

		$this->connec_entity_name       = 'SalesOrder';
		$this->local_entity_name        = 'SalesOrders';
		$this->connec_resource_name     = 'sales_orders';
		$this->connec_resource_endpoint = 'sales_orders';
		
		$this->sales_order_status_mapping = 
					array(
						'Awaiting check payment' 		=> 'AUTHORISED',
						'Payment accepted' 				=> 'PAID',
						'Processing in progress' 		=> 'AUTHORISED',
						'Shipped' 						=> 'SHIPPED',
						'Delivered' 					=> 'DELIVERED',
						'Canceled' 						=> 'Cancelled',
						'Refunded' 						=> 'REFUNDED',
						'Payment error' 				=> 'SUBMITTED',
						'On backorder (paid)' 			=> 'SUBMITTED',
						'Awaiting bank wire payment' 	=> 'AUTHORISED',
						'Awaiting PayPal payment' 		=> 'AUTHORISED',
						'Remote payment accepted' 		=> 'AUTHORISED',
						'On backorder (not paid)' 		=> 'AUTHORISED',
						'Awaiting Cash On Delivery validation' => 'AUTHORISED',
					);

	}

	// Return the Product local id
	protected function getId($order_status) 
	{
		return $order_status['id_order'];
	}

	// Return a local Product by id
	protected function loadModelById($local_id) 
	{

	}

	// Map the Connec resource attributes onto the Prestashop Product
	protected function mapConnecResourceToModel($order_status_hash, $order_status) 
	{
		// Not saved locally, one way to connec!
	}

	// Map the Prestashop Product to a Connec resource hash
	protected function mapModelToConnecResource($order_status) 
	{
		$order_status_hash = array();	

		$order_status_hash['status'] = $this->sales_order_status_mapping[$order_status['newOrderStatus']->name];		
		
		return $order_status_hash;
	}
	
	
	
	

}
