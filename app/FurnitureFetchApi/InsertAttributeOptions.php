<?php

namespace App\FurnitureFetchApi;
use App\Term;
use App\TermTaxonomy;
use App\TermTaxonomyProduct;
use App\Product;
use Log;
class InsertAttributeOptions{
    public $result;
    protected static $attribute;
    public function __construct($attribute){
        self::$attribute = $attribute;
    }

    public function insert_attribute_have_options(){
        foreach(self::$attribute['attribute_options'] as $option){
            // $this->create_term($option);
            // inserted new tirm
            $insereted_term = Term::firstOrCreate(
                ['name'       => $option['label'] ,'slug'=> self::handle_slug($option['label']) ],
                ['term_group' => 0]
            );

            // inserted new value
            $insereted_term->meta()->updateOrCreate(
                ['meta_key'   => self::$attribute['attribute_code'].'_value'],
                ['meta_value' => $option['value']]
            );

            // inserted term taxonomy
            if($insereted_term){
                $insereted_term_taxonomy = $insereted_term->term_taxonomy()->firstOrCreate(
                    ['term_id'     => $insereted_term->term_id,'taxonomy'    => 'pa_'.self::$attribute['attribute_code']],
                    ['description' => 'category data',
                    'parent'      => 0
                    ]
                );

                //Log::info('test mo :'.$option['label'].' --- '.$option['value'].' -- '.$insereted_term->term_id);

                $this->result[$option['label']] =  $insereted_term_taxonomy ? true :false;
            } else {
                $this->result[$option['label']] =  false;
            }
        }
    }

    public function insert_attribute_have_value_only(){
         // inserted new term
         $insereted_term = Term::firstOrCreate(
            ['name'       => self::$attribute['attribute_value'] ,'slug'=> self::handle_slug(self::$attribute['attribute_value']) ],
            ['term_group' => 0]
         );

        // inserted new value
        $insereted_term->meta()->updateOrCreate(
            ['meta_key'   => self::$attribute['attribute_code'].'_value'],
            ['meta_value' => self::$attribute['attribute_value']]
        );

        // inserted term taxonomy
        if($insereted_term){
            $insereted_term_taxonomy = $insereted_term->term_taxonomy()->firstOrCreate(
               ['term_id'     => $insereted_term->term_id,'taxonomy'    => 'pa_'.self::$attribute['attribute_code']],
               ['description' => 'category data',
                'parent'      => 0
               ]
            );

            $this->result[self::$attribute['attribute_value']] =  $insereted_term_taxonomy ? true :false;
        } else {
            $this->result[self::$attribute['attribute_value']] =  false;
        }
    }

    public static function handle_slug($value){
        $value = preg_replace('/[^A-Za-z0-9\-]/', '', $value);
        $value = strtolower($value);
        $slug  = str_replace(' ','',$value);
        return $slug;
    }

}


