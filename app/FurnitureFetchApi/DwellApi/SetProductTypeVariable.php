<?php

namespace App\FurnitureFetchApi\DwellApi;
use App\Product;
use App\ProductMeta;
use App\Term;
use App\TermTaxonomy;
use App\TermTaxonomyProduct;
class SetProductTypeVariable{
    public $result;
    public function __construct(Product $product){
        $term_variable = Term::where(['name' => 'variable' , 'slug' => 'variable'])->first();

        if($term_variable){
            $term_taxonomy = TermTaxonomy::where([
               'term_id' => $term_variable->term_id,
               'taxonomy'=> 'product_type'
            ])->first();

            if($term_taxonomy){
                TermTaxonomyProduct::updateOrCreate([
                    'object_id'        => $product->ID,
                    'term_taxonomy_id' => $term_taxonomy->term_taxonomy_id,
                    'term_order'       => 0
                ]);
            }
        }else{
            $this->result['_product_type_variable']   = false;
        }
    }
}
