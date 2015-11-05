<?php

/**
* Map Connec Product representation to/from Prestashop Product
*/

class ProductMapper extends BaseMapper {


	protected $companyMapper = null;


	public function __construct() 
	{
		parent::__construct();

		$this->connec_entity_name = 'Product';
		$this->local_entity_name = 'Products';
		$this->connec_resource_name = 'items';
		$this->connec_resource_endpoint = 'items';

	}

	// Return the Product local id
	protected function getId($product) 
	{
		return $product->id;
	}

	// Return a local Product by id
	protected function loadModelById($local_id) 
	{

	}

	// Map the Connec resource attributes onto the Prestashop Product
	protected function mapConnecResourceToModel($product_hash, $product) 
	{
		// Fiels mapping
		$product->setTypeId('simple');
        if (array_key_exists('code', $product_hash)) { $product->setSku($product_hash['code']); }
        if (array_key_exists('name', $product_hash)) { $product->setName($product_hash['name']); }
        if (array_key_exists('description', $product_hash)) { $product->setDescription($product_hash['description']); }
        
        // Set Price
        if (array_key_exists('sale_price', $product_hash)) {
            if (array_key_exists('net_amount', $product_hash['sale_price'])) {
                $product->setPrice($product_hash['sale_price']['net_amount']);
            }
        }
        
        // Set Weight
        if (array_key_exists('weight', $product_hash)) {
            $product->setWeight($product_hash['weight']);
        } 
        else {
            $product->setWeight(0);
        }        
        
        // Set Status
        if (array_key_exists('status', $product_hash)) {
			$product->setStatus($product_hash['status']);
		}
        
	}

	// Map the Prestashop Product to a Connec resource hash
	protected function mapModelToConnecResource($product) 
	{
		$product_hash = array(); 
				
		//$product_hash['serial_number'] = $product->column_fields['serial_no'];
		//$product_hash['part_number'] = $product->column_fields['productcode'];
		
		// Map attributes
		$product_hash['code'] = $product->reference;
        $product_hash['name'] = $product->name[1];
        $product_hash['description'] = $product->description[1];        
        $product_hash['sale_price'] =  array('net_amount' => $this->format_string_to_decimal($product->price));
        
        // Default product type to PURCHASED on creation
        if($this->is_new($product)) { $product_hash['type'] = 'PURCHASED'; }
        
        //Product Weight
        $product_hash['weight'] = $product->weight;
        
        // Product Status
        $product_hash['status'] = ($product->active) ? "ACTIVE" : "INACTIVE";
        
        // Inventory tracking
		$qtyinstock = $this->format_string_to_decimal($product->quantity);
    
		$product_hash['quantity_on_hand'] = $qtyinstock;
		
		// Tax for th Product
		ProductMapper::mapTaxToConnecResource($product, $product_hash);
		
		return $product_hash;        
		
	}
	
	// Add tax to product hash
	public static function mapTaxToConnecResource($product, &$product_hash) 
	{ 
		$sql = "SELECT * FROM "._DB_PREFIX_."tax_rule WHERE id_tax_rules_group = '".pSQL($product->id_tax_rules_group)."'";
	
		if ($row = Db::getInstance()->getRow($sql))
		{			
			if($row['id_tax'] > 0)
			{
				$mno_id_map = MnoIdMap::findMnoIdMapByLocalIdAndEntityName($row['id_tax'], 'TAXRECORD');
				if($mno_id_map) 
				{ 					
					$product_hash['sale_tax_code_id'] = $mno_id_map['mno_entity_guid']; 
				}
			}			
		}		
	}

}
