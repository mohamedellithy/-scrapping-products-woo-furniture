<?php

namespace App\FurnitureFetchApi\ACEApi;
use App\Product;
use App\FurnitureFetchApi\InsertFetchedProducts;
class InsertParentProduct extends InsertFetchedProducts{
    public function insert_new_product(){
        $this->prefix = 'ACE';
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
}
