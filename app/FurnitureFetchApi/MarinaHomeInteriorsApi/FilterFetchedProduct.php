<?php
namespace App\FurnitureFetchApi\MarinaHomeInteriorsApi;
use App\FurnitureFetchApi\WooProductProperties;
use App\FurnitureFetchApi\MarinaHomeInteriorsApi\FetchCategoryInfo;
class FilterFetchedProduct extends WooProductProperties{
    public const thumbnail_prefix_url = "https://prod.gumlet.io/media/catalog/product/cache/405e76798338ebf6b7792a8b5f7f32a4";
    public const website_url = "https://www.marinahomeinteriors.com/";
    public function set_product_item($product){
        self::$product = $product ?? null;
        self::$product['custom_attributes'] = collect(self::$product['custom_attributes']);
    }
    public function set_product_name(){
        // fetch product name
        return self::$product['name'] ?? null;
    }

    public function set_product_sku(){
        // fetch product sku
        return self::$product['sku'] ?? null;
    }

    public function set_product_id(){
        // fetch product id
        return self::$product['id'] ?? null;
    }

    public function set_stock_status(){
        // fetch product status
        return self::$product['status'] ?? null;
    }

    public function set_product_price(){
        // fetch product price
        return self::$product['price'] ?? null;
    }

    public function set_regular_price(){
        // fetch regular price product
        self::$product['regular_price'] = self::$product['custom_attributes']
        ->where('attribute_code','special_price')->pluck('value')->toArray();

        return self::$product['regular_price'][0] ?? null;
    }

    public function set_product_thumbnail(){
        // fetch product thumbnail
        self::$product['product_thumbnail'] = self::$product['custom_attributes']
        ->where('attribute_code','thumbnail')->pluck('value')->toArray();

        // handle thumbnail
        if(self::$product['product_thumbnail']){
            self::$product['product_thumbnail'] = [
                'url'  => self::thumbnail_prefix_url.self::$product['product_thumbnail'][0],
                'label'=> null
            ];
        }

        return self::$product['product_thumbnail'] ? self::$product['product_thumbnail'] : null;
    }

    public function set_product_description(){
        // fetch product description
        self::$product['description'] = self::$product['custom_attributes']
        ->where('attribute_code','description')->pluck('value')->toArray();

        return self::$product['description'][0] ?? null;
    }

    public function set_product_short_description(){
        // fetch product short description
        self::$product['product_info'] = self::$product['custom_attributes']
        ->where('attribute_code','short_description')->pluck('value')->toArray();

        return self::$product['product_info'][0] ?? null;
    }

    public function set_product_categories(){
        // get all categories
        $categories = self::$product['custom_attributes']->whereIN('attribute_code',['category_ids','category_main','sub_category'])->pluck('value')->toArray();

        // flatten all values category ids
        $categories = collect($categories)->flatten()->all();

        // if categories found foreach it
        foreach($categories as $category):

            // call api request to fetch data category
            $fetch_category      = FetchCategoryInfo::fetch_category_by_id($category);

            // check if category exist or not
            if(isset($fetch_category['name'])):

                // call api request to fetch parent category
                $parent_category = FetchCategoryInfo::fetch_category_by_id($fetch_category['parent_id']);

                $cat_all[]       = [
                    // insert category
                    'name'        => $fetch_category['name'] ?? null, // name
                    'url_key'     => strtolower(str_replace(' ','-',$fetch_category['name'].'_'.$fetch_category['id'])), // slug
                    // if category have parent Category ID
                    'breadcrumbs' => $fetch_category['parent_id'] ? [
                        [
                            'category_name' => $parent_category['name'] ?? null, // name
                            'url_key'       => strtolower(str_replace(' ','-',$parent_category['name'].'_'.$parent_category['id'])) ?? null // slug
                        ]
                    ] : null
                ];

            endif;

        endforeach;

        self::$product['categores'] = $cat_all ?? null;
        return self::$product['categores'];
    }

    public function set_product_variation(){
        // fetch variation
        return self::$product['variants'] ?? null;
    }

    public function set_product_permalink(){
        // fetch slug
        self::$product['url_key'] = self::$product['custom_attributes']
        ->where('attribute_code','url_key')->pluck('value')->toArray();

        return self::$product['url_key'][0] ? self::website_url.self::$product['url_key'][0].'.html' : null;
    }

    public function set_product_gallery(){
        // fetch product gallery
        $gallery = [];
        if(isset(self::$product['media_gallery_entries'])){
            foreach(self::$product['media_gallery_entries'] as $thumbnail){
                $gallery[] = [
                    'thumbnail' => [
                        'url'   => self::thumbnail_prefix_url.$thumbnail['file']
                    ],
                    'label' => $thumbnail['label']
                ];
            }
        }

        return $gallery ?? null;
    }

    public function set_product_slug(){
        // fetch product
        self::$product['url_key'] = self::$product['custom_attributes']
        ->where('attribute_code','url_key')->pluck('value')->toArray();

        return self::$product['url_key'][0] ?? null;
    }

    public function set_related_products(){
        // fetch related products
        return self::$product['product_links'] ?? null;
    }

    public function set_attributes(){
        // fetch product attributes
        $type = [];
        self::$product['attributes'] = self::$product['custom_attributes']
        ->whereIn('attribute_code',['color','material','finish','country_of_manufacture','show_dimensions'])
        ->pluck('value','attribute_code')->toArray();

        // if types is Not empty
        if (isset(self::$product['attributes'])) {

            foreach(self::$product['attributes'] as $key => $value) {
                $type[] = [
                    'attribute_label' => str_replace('_', '-', $key),
                    'attribute_value' => str_replace(',',' - ',strip_tags($value)),
                    'attribute_type'  => 'select',
                    'attribute_code'  => strtolower(str_replace(' ', '-', $key)),
                    'attribute_options'=> []
                ];
            }

        }
        return $type ?: null;
    }

    public function set_product_quantity(){
        // fetch product quantity
        return self::$product['quantity'] ?? null;
    }

    public function set_configurable_options(){
        // fetch product configurable options
        return  null;
    }

}


