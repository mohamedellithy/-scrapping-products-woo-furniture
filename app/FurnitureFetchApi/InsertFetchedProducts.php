<?php

namespace App\FurnitureFetchApi;
use App\Product;
use App\ProductMeta;
use Log;
use App\Option;
use App\FurnitureFetchApi\InsertProductAttachment;
use App\FurnitureFetchApi\InsertCategoryProduct;
use App\FurnitureFetchApi\InsertAttributeProduct;
class InsertFetchedProducts{
    protected static $fetched_product;
    protected static $inserted_product;
    protected $prefix;
    public $result = array();
    public function __construct($fetched_products){
       self::$fetched_product = $fetched_products;
       $this->insert_new_product();
       $this->insert_details_new_product();
    }

    public function insert_new_product(){
        if ($this->product_inserted_before() == false) {
            // create new product
            self::$inserted_product = Product::create([
                'post_content'          => self::$fetched_product->product_description ?? '',
                'post_excerpt'          => self::$fetched_product->product_short_description ?? '',
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

    public function product_inserted_before(){
        // check if product is existed
        $product_is_already_exist = ProductMeta::where([
            'meta_key'   => '_fetch_product_id',
            'meta_value' => $this->prefix.self::$fetched_product->product_id
        ]);

        // if product is exist return true
        if($product_is_already_exist->exists()){
            self::$inserted_product = $product_is_already_exist->first()->product ?? null;
            return true;
        }

        // return false not exist
        return false;
    }

    public function insert_details_new_product(){
        // inserted or updated
        if(method_exists($this,'inserted_or_update_type_product')){
            try{ $this->inserted_or_update_type_product(); }
            catch(\Exception $e){ $this->log_errors($e->getMessage(),'update_type_product'); }
        }

        // inserted product meta lookup
        if(method_exists($this,'inserted_or_updated_product_meta_look_up')){
            try{ $this->inserted_or_updated_product_meta_look_up(); }
            catch(\Exception $e){ $this->log_errors($e->getMessage(),'update_product_meta_look_up'); }
        }

        // inserted or updated price
        if(method_exists($this,'inserted_or_update_price')){
            try{ $this->inserted_or_update_price(); }
            catch(\Exception $e){ $this->log_errors($e->getMessage(),'update_price'); }
        }

        // inserted or updated product id
        if(method_exists($this,'inserted_or_update_product_id')){
            try{ $this->inserted_or_update_product_id(); }
            catch(\Exception $e){ $this->log_errors($e->getMessage(),'update_product_id'); }
        }

        // inserted or updated product thumbnail
        if(method_exists($this, 'inserted_or_updated_thumbnail')) {
            try{ 
                $this->inserted_or_updated_thumbnail(); 
            }catch(\Exception $e){ $this->log_errors($e,'update_thumbnail'); }
        }

        // inserted or updated product gallery
        if (method_exists($this, 'inserted_or_updated_gallery')) {
            try{ $this->inserted_or_updated_gallery(); }
            catch(\Exception $e){ $this->log_errors($e,'update_gallery'); }
        }

        // inserted or updated product stock status
        if(method_exists($this, 'inserted_or_updated_stock')){
            try{ $this->inserted_or_updated_stock_status(); }
            catch(\Exception $e){ $this->log_errors($e->getMessage(),'update_stock'); }
        }

        // inserted or updated product quantity
        if(method_exists($this, 'inserted_or_updated_quantity')){
            try{ $this->inserted_or_updated_quantity(); }
            catch(\Exception $e){ $this->log_errors($e->getMessage(),'update_quantity'); }
        }

        // inserted or updated product url
        if(method_exists($this, 'inserted_or_updated_product_url')){
            try{ $this->inserted_or_updated_product_url(); }
            catch(\Exception $e){ $this->log_errors($e->getMessage(),'update_product_url'); }
        }

        // inserted or updated product sku
        if(method_exists($this, 'inserted_or_updated_sku')){
            try{ $this->inserted_or_updated_sku(); }
            catch(\Exception $e){ $this->log_errors($e->getMessage(),'update_sku'); }
        }

        // inserted or updated product total sale
        if (method_exists($this, 'inserted_or_updated_total')) {
            try{ $this->inserted_or_updated_total_sale(); }
            catch(\Exception $e){ $this->log_errors($e->getMessage(),'update_total'); }
        }

        // inserted or updated category
        if (method_exists($this, 'inserted_or_updated_category')) {
            try{ $this->inserted_or_updated_category(); }
            catch(\Exception $e){ $this->log_errors($e->getMessage(),'update_category'); }
        }

        // inserted or updated attribute
        if (method_exists($this, 'inserted_or_updated_attribute')) {
            try{ $this->inserted_or_updated_attribute(); }
            catch(\Exception $e){ $this->log_errors($e->getMessage(),'update_attribute'); }
        }

        // inserted or updated variation
        if(method_exists($this, 'inserted_or_updated_variate')){
            try{ $this->inserted_or_updated_variate(); }
            catch(\Exception $e){ $this->log_errors($e->getMessage(),'update_variate'); }
        }

        if(method_exists($this,'update_ids_products')){
            try{ $this->update_ids_products(); }
            catch(\Exception $e){ $this->log_errors($e->getMessage(),'update_ids'); }
        }

        return true;
    }

    public function inserted_or_update_price(){
        $this->result['_price_changed'] = self::$inserted_product->meta()->updateOrCreate(
            ['meta_key' => '_price'],
            ['meta_value' => self::$fetched_product->product_price]
        ) ? true : null;

        $this->result['_price_changed'] = self::$inserted_product->meta()->updateOrCreate(
            ['meta_key' => '_sale_price'],
            ['meta_value' => self::$fetched_product->product_price]
        ) ? true : null;

        $this->result['_price_changed'] = self::$inserted_product->meta()->updateOrCreate(
            ['meta_key'   => '_regular_price'],
            ['meta_value' => self::$fetched_product->regular_price]
        ) ? true : null;
    }

    public function inserted_or_update_product_id(){
        if(self::$fetched_product->product_id == null):
            $this->result['_product_id_changed'] = false;
            return;
        endif;

        $this->result['_product_id_changed'] = self::$inserted_product->meta()->updateOrCreate(
            ['meta_key' => '_fetch_product_id'],
            ['meta_value' => $this->prefix.self::$fetched_product->product_id]
        ) ? true : null;
    }
    public function inserted_or_updated_thumbnail(){
        if(self::$fetched_product->product_thumbnail == null):
            $this->result['_attachment_thumbnail_changed'] = false;
            return;
        endif;

        $inserted_attachment = new InsertProductAttachment(self::$inserted_product,self::$fetched_product);
        $this->result['_attachment_thumbnail_changed']  = $inserted_attachment->results;
    }
    public function inserted_or_updated_gallery(){
        if(self::$fetched_product->product_gallery == null):
            $this->result['_attachment_gallery_changed'] = false;
            return;
        endif;

        $inserted_gallery = new InsertProductGallery(self::$inserted_product,self::$fetched_product);
        $this->result['_attachment_gallery_changed']  = $inserted_gallery->results;
    }
    public function inserted_or_updated_stock_status(){
        if(self::$fetched_product->stock_status == null):
            $this->result['_stock_status_changed'] = false;
            return;
        endif;

        $this->result['_stock_status_changed'] = self::$inserted_product->meta()->updateOrCreate(
            ['meta_key'   => '_stock_status'],
            ['meta_value' => self::$fetched_product->stock_status == 'IN_STOCK' ? "instock" : "outofstock"]
        ) ? true : null;
    }

    public function inserted_or_updated_quantity(){
        if(self::$fetched_product->product_quantity != null):
            $this->result['_stock_status_changed'] = self::$inserted_product->meta()->updateOrCreate(
                ['meta_key' => '_stock'],
                ['meta_value' => self::$fetched_product->product_quantity ?? 0]
            ) ? true : null;

            $this->result['_stock_status_changed'] = self::$inserted_product->meta()->updateOrCreate(
                ['meta_key' => '_manage_stock'],
                ['meta_value' => true]
            ) ? true : null;
        endif;
    }

    public function inserted_or_updated_sku(){
        $this->result['_sku_changed'] = self::$inserted_product->meta()->updateOrCreate(
            ['meta_key'   => '_sku'],
            ['meta_value' => self::$fetched_product->product_sku ? $this->prefix.self::$fetched_product->product_sku : null]
        ) ? true : null;
    }

    public function inserted_or_updated_product_meta_look_up(){
        $min_price = self::$fetched_product->regular_price ? str_replace(',','',self::$fetched_product->regular_price) : 0.0000;
        $max_price = self::$fetched_product->product_price ? str_replace(',','',self::$fetched_product->product_price) : 0.0000;
        $this->result['_product_meta_look_up'] = self::$inserted_product->product_meta_look_up()->updateOrCreate(
            ['sku'            => self::$fetched_product->product_sku ? $this->prefix.self::$fetched_product->product_sku : null],
            ['min_price'      => $min_price ?? 0.0000,
             'max_price'      => $max_price ?? 0.0000,
             'onsale'         => (self::$fetched_product->regular_price != null && self::$fetched_product->product_price != null ? 1 : 0),
             'stock_status'   => self::$fetched_product->stock_status == 'IN_STOCK' ? "instock" : "outofstock",
             'stock_quantity' => self::$fetched_product->product_quantity ?? null
            ]
        ) ? true : null;
    }

    public function inserted_or_updated_product_url(){
        $this->result['_product_url_changed'] = self::$inserted_product->meta()->updateOrCreate(
            ['meta_key' => '_product_url'],
            ['meta_value' => self::$fetched_product->product_permalink ?? null]
        ) ? true : null;
    }
    public function inserted_or_updated_total_sale(){
        $this->result['_total_sales_changed'] = self::$inserted_product->meta()->updateOrCreate(
            ['meta_key' => 'total_sales'],
            ['meta_value' => self::$fetched_product->total_sale ?? 0]
        ) ? true : null;
    }

    public function inserted_or_updated_category(){
        if(self::$fetched_product->product_categories == null):
            $this->result['_inserted_category_product'] = false;
            return;
        endif;

        // try to insert categories
        $inserted_category = new InsertCategoryProduct(self::$inserted_product,self::$fetched_product);
        $this->result['_inserted_category_product']  = $inserted_category->result;
    }
    public function inserted_or_updated_attribute(){
        if(self::$fetched_product->attributes == null):
            $this->result['_inserted_attribute_product'] = false;
            return;
        endif;

        // try to insert attributes
        $inserted_attribute = new InsertAttributeProduct(self::$inserted_product,self::$fetched_product);
        $this->result['_inserted_attribute_product']  = $inserted_attribute->result;
    }

    public function log_errors($error,$type){
        Log::error("Platform : (".$this->prefix.") | ID : (".self::$fetched_product->product_id.") | Place Error : ( ".$type." )  =>  ".$error);
    }

    public function update_ids_products(){
        if(self::$inserted_product->parent_product == null):
            //get option that follow platform
            $options_value = Option::where(['option_name' => $this->prefix."_new"])->value('option_value');

            //get value option
            if($options_value == null):
                $options_value[] = self::$inserted_product->ID ?? null;
            else:
                $options_value   = unserialize($options_value);
                array_push($options_value,self::$inserted_product->ID);
            endif;

            // updated or create option
            Option::updateOrCreate(
                ['option_name' => $this->prefix."_new"],
                ['option_value' => serialize($options_value)]
            );
        endif;
    }

}
