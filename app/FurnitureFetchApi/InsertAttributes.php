<?php
namespace App\FurnitureFetchApi;
use App\AttributeTaxonomy;
class InsertAttributes{
    protected static $attributes;
    protected $result;

    public function insert_attributes_not_found_in_product($atts){
        self::$attributes = $atts;
        $this->create_attributes();
    }

    public function insert_attributes_general($product){
        self::$attributes = $product->get_attributes();
        $this->create_attributes();
    }

    public function create_attributes(){

        foreach(self::$attributes as $attribute){
            $inserted_attrs = AttributeTaxonomy::firstOrCreate(
                ['attribute_name' => $attribute['attribute_code'],'attribute_label' => $attribute['attribute_label']],
                ['attribute_type' => $attribute['attribute_type']]
            );

            if(!isset($attribute['attribute_options'])){
                $this->result[$attribute['attribute_code']]['main'] = false;
            }else{

                if(count($attribute['attribute_options']) != 0){
                    $insert_options  = new InsertAttributeOptions($attribute);
                    $insert_options->insert_attribute_have_options();
                    $this->result[$attribute['attribute_code']]['options'] = $insert_options->result;
                }

                if((count($attribute['attribute_options']) == 0) && ($attribute['attribute_value'] != null) ){
                    $insert_options  = new InsertAttributeOptions($attribute);
                    $insert_options->insert_attribute_have_value_only();
                    $this->result[$attribute['attribute_code']]['options'] = $insert_options->result;
                }

                if($inserted_attrs){
                    $this->result[$attribute['attribute_code']]['main'] = true;
                }
                else{
                    $this->result[$attribute['attribute_code']]['main'] = false;
                }

            }

        }
    }
}
