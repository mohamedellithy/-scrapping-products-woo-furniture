<?php

namespace App\FurnitureFetchApi;
use App\Term;
use App\TermTaxonomy;
use App\TermTaxonomyProduct;
use App\AttributeTaxonomy;
use App\ProductAttribute;
use App\Product;
use Log;
use Illuminate\Database\Eloquent\Builder;
// use App\FurnitureFetchApi\DwellApi\FilterFetchedProduct;
use App\FurnitureFetchApi\InsertAttributes;
class InsertAttributeProduct{
    protected static $product;
    protected static $filtered;
    public $result;
    public function __construct(Product $product,$filter) {
        self::$product  = $product;
        self::$filtered = $filter;
        $insert_new_attribute = new InsertAttributes();
        $insert_new_attribute->insert_attributes_not_found_in_product(self::$filtered->attributes);
        // check if is parent product
        if(self::$filtered->is_variated == false){
            foreach(self::$filtered->attributes as $attribute){
                $this->add_attributes_for_parent_product($attribute);
            }
        }
        else{
            foreach (self::$filtered->configurable_options as $config_option) {

                $this->add_attributes_for_variant_product($config_option);
            }
        }
    }

    public function add_attributes_for_parent_product($attribute){
        $term = Term::whereHas('meta',function(Builder $query) use ($attribute){
            $query->where([
                'meta_key'   => $attribute['attribute_code'].'_value',
                'meta_value' => $attribute['attribute_value']
            ]);
        })->whereHas('term_taxonomy',function(Builder $query) use ($attribute){
            $query->where([
                'taxonomy' => 'pa_'.$attribute['attribute_code']
            ]);
        })->first();


        if($term){
            $insert_term_product = self::$product->meta()->updateOrCreate(
                ['meta_key'   => '_product_attributes'],
                ['meta_value' => $this->serialize_attribute('pa_'.$attribute['attribute_code'],self::$product)]
            );

            //Log::info('my code : '.$attribute['attribute_code'].' '.' '.$insert_term_product->meta_value);

            if($insert_term_product){
                // get term id
                $term_attribute = $term->meta()->where('meta_key',$attribute['attribute_code'].'_value')->first();

                // get taxonomy id
                $term_taxonomy = $term->term_taxonomy()->where('taxonomy','pa_'.$attribute['attribute_code'])->first();

                // insert term attribute with product
                $insert_term_product = TermTaxonomyProduct::firstOrCreate([
                    'object_id'        => self::$product->ID,
                    'term_taxonomy_id' => $term_taxonomy->term_taxonomy_id,
                    'term_order'       => 0
                ]);

                // insert attribute with product
                $attribute = ProductAttribute::updateOrCreate(
                    ['product_id' => self::$product->ID,
                     'taxonomy'   => 'pa_'.$attribute['attribute_code'],
                     'term_id'    => $term_attribute->term_id],
                    ['is_variation_attribute' => 1,'in_stock'=> 1,'product_or_parent_id' => self::$filtered->parent_product->ID ?? self::$product->ID ]
                );
            }

            $this->result = true;
        }else{
            $this->result = false;
        }
    }

    public function add_attributes_for_variant_product($config_option) {
        // forloop all attributes in product
        foreach (self::$filtered->attributes as $attribute) {
            // check if this attribute is have variate
            if ($attribute['attribute_code'] == $config_option['attribute_code']) {
                $term = Term::whereHas('meta', function (Builder $query) use ($attribute) {
                    $query->where([
                        'meta_key'   => $attribute['attribute_code'].'_value',
                        'meta_value' => $attribute['attribute_value']
                    ]);
                })->whereHas('term_taxonomy', function (Builder $query) use ($attribute) {
                    $query->where([
                        'taxonomy'   => 'pa_'.$attribute['attribute_code']
                    ]);
                })->first();

                if ($term) {

                    if(self::$filtered->parent_product){
                        $insert_term_product = self::$filtered->parent_product->meta()->updateOrCreate(
                            ['meta_key'   => '_product_attributes'],
                            ['meta_value' => $this->serialize_attribute('pa_'.$attribute['attribute_code'],self::$filtered->parent_product,1)]
                        );
                    }

                    $insert_term_product = self::$product->meta()->updateOrCreate(
                        ['meta_key'    => 'attribute_pa_'.$attribute['attribute_code'],
                         'meta_value'  => $term->slug]
                    );

                    if ($insert_term_product) {
                        Log::info("mo take : ".$term->name);
                        // get term id
                        $term_attribute = $term->meta()->where('meta_key', $attribute['attribute_code'].'_value')->first();

                        // inserted new order term value
                        $term->meta()->updateOrCreate(
                            ['term_id'    => $term->term_id,
                             'meta_key'   => 'order_pa_'.$attribute['attribute_code']],
                            ['meta_value' => 0]
                        );

                        // get taxonomy id
                        $term_taxonomy = $term->term_taxonomy()->where('taxonomy', 'pa_'.$attribute['attribute_code'])->first();

                        // insert term attribute with product
                        $insert_term_product = TermTaxonomyProduct::firstOrCreate([
                            'object_id'        => self::$filtered->parent_product ? self::$filtered->parent_product->ID : self::$product->ID,
                            'term_taxonomy_id' => $term_taxonomy->term_taxonomy_id,
                            'term_order'       => 0
                        ]);

                        // insert attribute with product
                        $attribute = ProductAttribute::updateOrCreate(
                            ['product_id' => self::$product->ID,
                             'taxonomy'   => 'pa_'.$attribute['attribute_code'],
                             'term_id'    => $term_attribute->term_id],
                            ['is_variation_attribute' => 1,'in_stock'=> 1,'product_or_parent_id' => self::$filtered->parent_product->ID ?? self::$product->ID ]
                        );
                    }

                    $this->result = true;
                }
            }
        }
    }

    public function serialize_attribute($attribute,$product,$is_variation = 0){
        $serialize_attr = $product->meta()->where('meta_key','_product_attributes')->first();
        $serialize_attr = $serialize_attr ? $serialize_attr->meta_value : '';
        $unserialize_attr = unserialize($serialize_attr);
        $unserialize_attr[$attribute] = array (
            'name' => $attribute,
            'value' => '',
            'position' => 0,
            'is_visible' => 1,
            'is_variation' => $is_variation ?? 0,
            'is_taxonomy' => 1,
        );

        return serialize($unserialize_attr);
    }
}
