<?php

namespace App\FurnitureFetchApi\DwellApi;
use App\Product;
use App\ProductMeta;
use App\FurnitureFetchApi\InsertFetchedProducts;
class InsertVariateProduct extends InsertFetchedProducts{
    public function insert_new_product(){
        $this->prefix = 'DwellV';
        if ($this->product_inserted_before() == false) {
            // create new product
            self::$inserted_product = Product::create([
                'post_content'          => self::$fetched_product->product_short_description ?? "",
                'post_title'            => self::$fetched_product->product_name,
                'post_status'           => 'publish',
                'post_name'             => self::$fetched_product->product_slug,
                'post_parent'           => self::$fetched_product->parent_product->ID,
                'post_type'             => 'product_variation',
                'post_date_gmt'         => date('Y-m-d h:i:s'),
                'post_modified_gmt'     => date('Y-m-d h:i:s')
            ]);

            $this->result['_product_changed_main_attrs']  = self::$inserted_product ? true : null;
        }
    }

    public function inserted_or_update_price(){
        $this->result['_price_changed'] = self::$inserted_product->meta()->updateOrCreate([
            'meta_key' => '_price',
            'meta_value' => self::$fetched_product->product_price
        ]) ? true : null;

        $this->result['_price_changed'] = self::$inserted_product->meta()->updateOrCreate([
            'meta_key' => '_sale_price',
            'meta_value' => self::$fetched_product->product_price
        ]) ? true : null;

        $this->result['_price_changed'] = self::$inserted_product->meta()->updateOrCreate([
            'meta_key' => '_regular_price',
            'meta_value' => self::$fetched_product->product_price
        ]) ? true : null;

    }
}
