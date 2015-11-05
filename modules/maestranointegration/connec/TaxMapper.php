<?php

/**
* Map Connec Tax Tax representation to/from Prestashop Tax
*/
class TaxMapper extends BaseMapper {
	
  public function __construct() 
  {
    parent::__construct();

    $this->connec_entity_name        = 'TaxCode';
    $this->local_entity_name         = 'TaxRecord';
    $this->connec_resource_name      = 'tax_codes';
    $this->connec_resource_endpoint  = 'tax_codes';
  }

  // Return the Tax local id
  protected function getId($tax) 
  {
    return $tax->id;
  }

  // Return a local Tax by id
  protected function loadModelById($local_id) 
  {
    
  }
  
  protected function mapConnecResourceToModel($tax_hash, $tax) 
  {    
    if($this->is_set($tax_hash['code'])) { $tax->set('taxname', $tax_hash['code']); }
    if($this->is_set($tax_hash['name'])) { $tax->set('taxlabel', $tax_hash['name']); }
    if($this->is_set($tax_hash['sale_tax_rate'])) { $tax->set('percentage', $tax_hash['sale_tax_rate']); }
  }

  // Map the Prestashop Tax to a Connec resource hash
  protected function mapModelToConnecResource($tax) 
  {
    $tax_hash = array();
    $tax_hash['code'] = str_replace(" ","-",strtolower($tax->name[1]));
    $tax_hash['name'] = $tax->name[1];
    $tax_hash['sale_tax_rate'] = $tax->rate;
    return $tax_hash;
  }


}
