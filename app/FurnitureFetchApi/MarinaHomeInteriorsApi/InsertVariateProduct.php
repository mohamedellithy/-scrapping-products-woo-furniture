<?php

namespace App\FurnitureFetchApi\MarinaHomeInteriorsApi;
use App\Product;
use App\ProductMeta;
use App\FurnitureFetchApi\InsertFetchedProducts;
class InsertVariateProduct extends InsertFetchedProducts{
    public $prefix;
    public function insert_new_product(){
        $this->prefix = "MarinaHomeInteriorsV";
        if ($this->product_inserted_before() == false) {
            // create new product
            self::$inserted_product = Product::create([
                'post_content'          => self::$fetched_product->product_short_description ?? '',
                'post_title'            => self::$fetched_product->product_name,
                'post_status'           => 'publish',
                'post_name'             => self::$fetched_product->product_slug ?? self::$fetched_product->parent_product->post_name,
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
            'meta_value' => self::$fetched_product->regular_price
        ]) ? true : null;

    }

    public function inserted_or_updated_stock_status(){
        $this->result['_stock_status_changed'] = self::$inserted_product->meta()->updateOrCreate([
            'meta_key' => '_stock_status',
            'meta_value' => self::$fetched_product->stock_status == 1 ? "instock" : "outofstock"
        ]) ? true : null;
    }

    public function inserted_or_updated_product_meta_look_up(){
        $this->result['_product_meta_look_up'] = self::$inserted_product->product_meta_look_up()->updateOrCreate(
            [   'sku'            => self::$fetched_product->product_sku ?? null],
            [   'min_price'      => self::$fetched_product->product_price ?? null,
                'max_price'      => self::$fetched_product->regular_price ?? null,
                'onsale'         => (self::$fetched_product->regular_price != null && self::$fetched_product->product_price != null ? 1 : 0),
                'stock_status'   => self::$fetched_product->stock_status == 1 ? "instock" : "outofstock",
                'stock_quantity' => self::$fetched_product->product_quantity ?? null
            ]) ? true : null;
    }

}
