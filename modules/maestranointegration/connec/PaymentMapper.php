<?php

/**
* Map Connec Invoice representation to/from Prestashop Invoice
*/

class PaymentMapper extends BaseMapper {
	
	
	public function __construct() 
	{
		parent::__construct();

		$this->connec_entity_name = 'Payment';
        $this->local_entity_name = 'Payments';
        $this->connec_resource_name = 'payments';
        $this->connec_resource_endpoint = 'payments';
	}
	
	// Return the Product local id
	protected function getId($payment) 
	{
		
	}
	
	// Return a local Product by id
	protected function loadModelById($local_id) 
	{

	}
	
	// Map the Connec resource attributes onto the Prestashop
	protected function mapConnecResourceToModel($payment_hash, $payment) 
	{
		// Not saved locally, one way to connec!	

	}
	
	// Map the Prestashop Product to a Connec resource hash
	protected function mapModelToConnecResource($payment) 
	{
		$payment_hash = array();
        $payment_hash['type'] = 'CUSTOMER';
        
        // Missing payment lines are considered as deleted by Connec!
        $payment_hash['opts'] = array('sparse' => false);
        	
	}
	
}
