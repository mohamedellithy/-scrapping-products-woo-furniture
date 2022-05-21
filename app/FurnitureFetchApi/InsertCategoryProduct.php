<?php

namespace App\FurnitureFetchApi;
use App\Term;
use App\TermTaxonomy;
use App\TermTaxonomyProduct;
use App\Product;
//use App\FurnitureFetchApi\DwellApi\FilterFetchedProduct;
class InsertCategoryProduct{
    protected static $product;
    protected static $filtered;
    public $result;
    public function __construct(Product $product,$filter) {
        self::$product  = $product;
        self::$filtered = $filter;
        foreach(self::$filtered->product_categories as $category){
            $this->create_term($category);
        }
    }

    public function create_term($category) {
        $insereted_term = Term::firstOrCreate(
           ['name'       => $category['name'] ,'slug' => $category['url_key'] ],
           ['term_group' => 0]
        );

        if($insereted_term){
            $insereted_term_taxonomy = $insereted_term->term_taxonomy()->firstOrCreate(
               ['term_id'     => $insereted_term->term_id],
               ['taxonomy'    => 'product_cat',
                'description' => 'category data',
                'parent'      => isset($category['breadcrumbs']) ? $this->parent_term($category['breadcrumbs']) : 0
               ]
            );

            if($insereted_term_taxonomy){
                $insert_term_product = TermTaxonomyProduct::firstOrCreate([
                    'object_id'        => self::$product->ID,
                    'term_taxonomy_id' => $insereted_term_taxonomy->term_taxonomy_id,
                    'term_order'       => 0
                ]);
                $this->result = true;
            }

        }else{
            $this->result = false;
        }

    }

    public function parent_term($parents){
        if($parents != null){
            $parent = $parents[count($parents) - 1];
            $term = Term::firstOrCreate(
                ['name'       => $parent['category_name'],
                 'slug'       => $parent['url_key'] ?? strtolower(str_replace(' ','-',$parent['category_name'])), 'term_group' => 0 ]
            );
            if($term){
                $insereted_term_taxonomy = $term->term_taxonomy()->firstOrCreate(
                    ['term_id'        => $term->term_id],
                    ['taxonomy'       => 'product_cat',
                        'description' => 'category data',
                        'parent'      => 0
                    ]
                );

                if($insereted_term_taxonomy){
                    $insert_term_product = TermTaxonomyProduct::firstOrCreate([
                        'object_id'        => self::$product->ID,
                        'term_taxonomy_id' => $insereted_term_taxonomy->term_taxonomy_id,
                        'term_order'       => 0
                    ]);
                }
                return $term->term_id;
            }
        }

        return $parents;
    }
}
