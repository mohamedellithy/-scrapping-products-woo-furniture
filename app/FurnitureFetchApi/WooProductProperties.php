<?php
namespace App\FurnitureFetchApi;
use Log;
class WooProductProperties{
    protected static $product;
    public $product_name = '';
    public $product_id = '';
    public $product_sku = '';
    public $product_categories='';
    public $product_permalink = '';
    public $stock_status = '';
    public $special_price = '';
    public $product_thumbnail = '';
    public $regular_price = '';
    public $product_price = '';
    public $product_currency = '';
    public $product_slug = '';
    public $product_gallery = '';
    public $total_sale = '';
    public $product_variation = '';
    public $product_discount = '';
    public $product_perecent_discount = '';
    public $product_description = '';
    public $product_short_description = '';
    public $related_products = '';
    public $product_quantity = null;
    public $attributes = '';
    public $saved_value='';
    public $is_variated = false;
    public $parent_product;
    public $configurable_options;

    public function __construct($product){
        $this->set_product_item($product);
        foreach(get_object_vars($this) as $property_name => $property_value){
            if(method_exists($this, "set_".$property_name)){
                try{ $this->{$property_name} = $this->{"set_".$property_name}() ?? null; }
                catch(\Exception $e){ $this->log_errors($e->getMessage(),"set_".$property_name); }
            }
        }
    }

    public function set_product_item($product){
        self::$product = $product['products']['data']['products']['items'] ?? null;
    }

    public function log_errors($error,$type){
        Log::error("Place Error : ( ".$type." ) | Error =>  ".$error." Product : (".json_encode(self::$product).")");
    }


}
