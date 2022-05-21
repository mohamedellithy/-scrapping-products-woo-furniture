<?php
namespace App\FurnitureFetchApi\EbarzaApi;
use App\FurnitureFetchApi\WooProductProperties;
class FilterFetchedProduct extends WooProductProperties{
    public const prefix_url = "https://www.ebarza.com";
    public function set_product_item($product){
        // return product
        self::$product = $product ?? null;
    }
    public function set_product_name(){
        return self::$product['title'] ?? null;
    }

    public function set_product_sku(){
        return self::$product['sku'] ?? self::$product['id'];
    }

    public function set_product_id(){
        return self::$product['id'] ?? null;
    }

    public function set_stock_status(){
        return self::$product['available'] == true ? 'IN_STOCK' : 'OUT_OF_STOCK';
    }

    public function set_product_quantity(){
        return self::$product['inventory_quantity'] ?? self::$product['variants'][0]['inventory_quantity'];
    }

    public function set_product_price(){
        return self::$product['price'] ? self::$product['price'] / 100 : null;
    }

    public function set_regular_price(){
        return self::$product['compare_at_price'] ? self::$product['compare_at_price'] / 100 : self::$product['price'] / 100;
    }

    public function set_product_thumbnail(){
        if(!strpos('https',self::$product['featured_image'])){
            self::$product['featured_image'] = "https:".self::$product['featured_image'];
        }
        self::$product['product_thumbnail'] = [
            'url'  => self::$product['featured_image'] ?? null,
            'label'=> self::$product['vendor'] ?? self::$product['title']
        ];
        return self::$product['product_thumbnail'] ?? null;
    }

    public function set_product_currency(){
        return 'AED';
    }

    public function set_product_description(){
        return self::$product['description'] ?? null;
    }

    public function set_product_short_description(){
        return self::$product['description'] ?? NULL;
    }

    public function set_product_categories(){
        if(!isset(self::$product['tags']))
           return null;

        $categories = self::$product['tags'];
        foreach($categories as $category):
            $item= [
                'name'        => $category,
                'url_key'     => strtolower(str_replace(' ','-',$category)),
                'breadcrumbs' => null
            ];

            self::$product['categories'][] = $item;
        endforeach;
        return self::$product['categories'] ?? null;
    }

    public function set_product_variation(){
        // disable default variant or first if no more variant
        if(count(self::$product['variants']) == 1)
            return null;

        return self::$product['variants'] ?? null;
    }

    public function set_product_permalink(){
        if(!isset(self::$product['url']))
           return null;

        return self::$product['url'] ? self::prefix_url.self::$product['url'] : null;
    }

    public function set_product_gallery(){
        if(!isset(self::$product['media']))
           return null;

        $galleries = self::$product['media'];
        foreach($galleries as $gallery):
            self::$product['product_gallery'][] = [
                'thumbnail' => ['url'  => strtok($gallery['src'],"?")],
                'label' => self::$product['alt'] ?? self::$product['title']
            ];
        endforeach;

        return self::$product['product_gallery'] ?? null;
    }

    public function set_product_slug(){
        if(!isset(self::$product['url']) && !isset(self::$product['handle']) )
           return str_replace(' ','-',self::$product['name'] ?? self::$product['title']).'-'.self::$product['id'];


        $data = explode('/',self::$product['url']);
        return self::$product['handle'] ?? str_replace(' ','-',self::$product['title']).'-'.self::$product['id'];
    }

    public function set_related_products(){
        return null;
    }

    public function set_attributes(){
        if(isset(self::$product['options'])):
            foreach(self::$product['options'] as $option):
                if(isset($option['name'])):
                    self::$product['attributes'][] = [
                        'attribute_label'  => "options",
                        'attribute_value'  => implode('-',$option['values']),
                        'attribute_type'   => 'select',
                        'attribute_code'   => strtolower(str_replace(' ','-',"options")),
                        'attribute_options'=> []
                    ];
                endif;
            endforeach;
        endif;

        foreach(self::$product as $key => $value):
            if(preg_match('/option[0-9]+/',$key)):
                if($value != null):
                    self::$product['attributes'][] = [
                        'attribute_label'  => "options",
                        'attribute_value'  => $value,
                        'attribute_type'   => 'select',
                        'attribute_code'   => strtolower(str_replace(' ','-',"options")),
                        'attribute_options'=> []
                    ];
                endif;
            endif;
        endforeach;

        return self::$product['attributes'] ?? null;
    }

    public function set_special_price(){
        return null;
    }

    public function set_product_discount(){
        return null;
    }

    public function set_total_sale(){
        return null;
    }

    public function set_saved_value(){
        return null;
    }

    public function set_configurable_options(){
        foreach(self::$product as $key => $value){
            if(preg_match('/option[0-9]+/',$key)){
                if($value != null):
                    self::$product['configurable_options'][] = [
                        'attribute_label'  => "options",
                        'attribute_value'  => $value,
                        'attribute_type'   => 'select',
                        'attribute_code'   => strtolower(str_replace(' ','-',"options")),
                        'attribute_options'=> []
                    ];
                endif;
            }
        }
        return self::$product['configurable_options'] ?? null;
    }

}


