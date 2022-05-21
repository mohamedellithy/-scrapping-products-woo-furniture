<?php

namespace App\FurnitureFetchApi;
use App\Product;
use Log;
class DeleteFetechedProducts{
    protected $prefix;
    public $ids = array();
    public $products;
    public function __construct($ids){
        $this->ids        = $ids;
        $this->products   = Product::whereIN('ID',$ids)->get();

        foreach($this->products as $product):

            // deleted product meta lookup
            if(method_exists($this,'deleted_product_meta')){
                try{
                    $this->deleted_product_meta($product);
                }catch(\Exception $e){}
            }

            // deleted or deleted
            if(method_exists($this,'deleted_attachemnts_product')){
                try{
                    $this->deleted_attachemnts_product($product);
                }catch(\Exception $e){}
            }

            // deleted or deleted
            if(method_exists($this,'deleted_term_taxonomy_product')){
                try{
                    $this->deleted_term_taxonomy_product($product);
                }catch(\Exception $e){}
            }

            // deleted or deleted
            if(method_exists($this,'deleted_attribute_product')){
                try{
                   $this->deleted_attribute_product($product);
                }catch(\Exception $e){}
            }

            // deleted or deleted
            if(method_exists($this,'deleted_meta_look_up_product')){
                try{
                   $this->deleted_meta_look_up_product($product);
                }catch(\Exception $e){}
            }

        endforeach;

         // deleted or deleted
         if(method_exists($this,'deleted_childs_product')){
            try{
                $this->deleted_childs_product();
            }catch(\Exception $e){}
        }

        // deleted or deleted
        if(method_exists($this,'deleted_products')){
            try{
                $this->deleted_products();
            }catch(\Exception $e){}
        }

    }

    public function deleted_products(){
        Product::destroy($this->ids);
    }

    public function deleted_product_meta($product){
        $product->meta()->delete();
    }

    public function deleted_childs_product(){
        Product::where('post_type','product_variation')->whereIN('parent_id',$this->ids)->delete();
    }

    public function deleted_attachemnts_product($product){
        $product->attachments()->delete();
    }

    public function deleted_term_taxonomy_product($product){
        $product->term_taxonomy()->delete();
    }

    public function deleted_attribute_product($product){
        $product->attribute()->delete();
    }

    public function deleted_meta_look_up_product($product){
        $product->product_meta_look_up()->delete();
    }


}