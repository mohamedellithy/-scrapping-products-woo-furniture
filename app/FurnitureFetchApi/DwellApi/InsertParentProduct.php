<?php

namespace App\FurnitureFetchApi\DwellApi;
use App\Product;
use App\FurnitureFetchApi\SetProductTypeVariable;
use App\FurnitureFetchApi\InsertFetchedProducts;
class InsertParentProduct extends InsertFetchedProducts{
    public function insert_new_product(){
        $this->prefix = 'Dwell';
        if ($this->product_inserted_before() == false) {
            // create new product
            self::$inserted_product = Product::create([
                'post_content'          => self::$fetched_product->product_short_description ?? '',
                'post_title'            => self::$fetched_product->product_name,
                'post_status'           => 'publish',
                'post_name'             => self::$fetched_product->product_slug,
                'post_type'             => 'product',
                'post_date_gmt'         => date('Y-m-d h:i:s'),
                'post_modified_gmt'     => date('Y-m-d h:i:s')
            ]);

            $this->result['_product_changed_main_attrs']  = self::$inserted_product ? true : null;
        }
    }

    public function inserted_or_updated_variate(){
        $parent_product = self::$inserted_product ?? null;
        if(self::$fetched_product->product_variation == null){
            $this->result['variate'] = false;
        }else{

            foreach(self::$fetched_product->product_variation as $variate) {

                // start filter data fetched from single product
                $variate_filtered                       = new FilterFetchedProduct($variate);
                $variate_filtered->parent_product       = $parent_product ?? '';
                $variate_filtered->is_variated          = true;
                //$variate_filtered->configurable_options = self::$fetched_product->configurable_options;

                // try to insert filtered data
                $insert_new_item  = new InsertVariateProduct($variate_filtered);

                $this->result['variate'][] = $variate_filtered;
            }
        }
    }

    public function inserted_or_update_type_product(){
        $this->result['_type_variable'] = self::$fetched_product->product_variation;
        if(self::$fetched_product->product_variation == null):
            $remove_variation = SetProductTypeVariable::remove_variation_if_exist(self::$inserted_product);
            $this->result['_type_variable'] = false;
            return;
        endif;

        $set_product_type = new SetProductTypeVariable(self::$inserted_product);
        $this->result['_type_variable'] = $set_product_type->result;

    }
}
