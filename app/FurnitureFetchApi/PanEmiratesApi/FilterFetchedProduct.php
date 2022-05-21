<?php
namespace App\FurnitureFetchApi\PanEmiratesApi;
use App\FurnitureFetchApi\WooProductProperties;
class FilterFetchedProduct extends WooProductProperties{
    public const prefix_url = "https://www.panemirates.com/uae/en";
    public function set_product_item($product){
        // return product
        self::$product = $product ?? null;
    }
    public function set_product_name(){
        return self::$product['title'] ?? null;
    }

    public function set_product_sku(){
        return self::$product['sku'] ?? self::$product['product_id'];
    }

    public function set_product_id(){
        return self::$product['product_id'] ?? null;
    }

    public function set_stock_status(){
        return self::$product['has_stock'] == true ? 'IN_STOCK' : 'OUT_OF_STOCK';
    }

    public function set_product_quantity(){
        return self::$product['stock_level'] ?? null;
    }

    public function set_product_price(){
        return self::$product['current_price']['numeric'] ?? null;
    }

    public function set_regular_price(){
        return self::$product['regular_price']['numeric'] ?? null;
    }

    public function set_product_thumbnail(){
        if(isset(self::$product['images'])):
            self::$product['product_thumbnail'] = [
                'url'  => self::prefix_url.(self::$product['images'][0]['sizes']['image']['src'] ?? (self::$product['images'][0]['sizes']['medium']['src'] ?? null)),
                'label'=> self::$product['images'][0]['alt'] ?? self::$product['title']
            ];
        endif;
        return self::$product['product_thumbnail'] ?? null;
    }

    public function set_product_currency(){
        return 'AED';
    }

    public function set_product_description(){
        return self::$product['description'] ?? null;
    }

    public function set_product_short_description(){
        return (self::$product['description'] ?? NULL).(self::$product['summary'] ?? NULL).(self::$product['stock_level_message'] ?? NULL).(self::$product['size_guide'] ?? NULL);
    }

    public function set_product_categories(){
        $item= [
            'name'        => self::$product['category'] ?? null,
            'url_key'     => strtolower(str_replace(' ','-',self::$product['category'] ?? null)),
            'breadcrumbs' => null
        ];

        self::$product['categories'][] = $item;
        return self::$product['categories'] ?? null;
    }

    public function set_product_variation(){
        // disable default variant or first if no more variant
        return self::$product['variants'] ?? null;
    }

    public function set_product_permalink(){
        return self::prefix_url.(self::$product['url'] ?? null);
    }

    public function set_product_gallery(){
        array_shift(self::$product['images']);
        $galleries = self::$product['images'];
        foreach($galleries as $gallery):
            self::$product['product_gallery'][] = [
                'thumbnail' => ['url'  => self::prefix_url.strtok($gallery['sizes']['image']['src'] ?? $gallery['sizes']['medium']['src'],"?")],
                'label'     => $gallery['alt'] ?? self::$product['title']
            ];
        endforeach;

        return self::$product['product_gallery'] ?? null;
    }

    public function set_product_slug(){
        $slug = explode('/',self::$product['url']);
        return $slug[count($slug) - 1] ?? str_replace(' ','-',self::$product['title']).'-'.self::$product['id'];
    }

    public function set_related_products(){
        return null;
    }

    public function set_attributes(){
        // if(isset(self::$product['options'])):
        //     foreach(self::$product['options'] as $option):
        //         if(isset($option['name'])):
        //             foreach($option['values'] as $item):
        //                 self::$product['attributes'][] = [
        //                     'attribute_label'  => "options",
        //                     'attribute_value'  => $item ?? null,
        //                     'attribute_type'   => 'select',
        //                     'attribute_code'   => strtolower(str_replace(' ','-',"options")),
        //                     'attribute_options'=> []
        //                 ];
        //             endforeach;
        //         endif;
        //     endforeach;
        // endif;

        // foreach(self::$product as $key => $value){
        //     if(preg_match('/option[0-9]+/',$key)){
        //         if($value != null):
        //             self::$product['attributes'][] = [
        //                 'attribute_label'  => "options",
        //                 'attribute_value'  => $value,
        //                 'attribute_type'   => 'select',
        //                 'attribute_code'   => strtolower(str_replace(' ','-',"options")),
        //                 'attribute_options'=> []
        //             ];
        //         endif;
        //     }
        // }

        // if(isset(self::$product['weight'])):
        //     self::$product['attributes'][] = [
        //         'attribute_label'  => "weight",
        //         'attribute_value'  => self::$product['weight'],
        //         'attribute_type'   => 'select',
        //         'attribute_code'   => strtolower(str_replace(' ','-',"weight")),
        //         'attribute_options'=> []
        //     ];
        // endif;

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
        // foreach(self::$product as $key => $value){
        //     if(preg_match('/option[0-9]+/',$key)){
        //         if($value != null):
        //             self::$product['configurable_options'][] = [
        //                 'attribute_label'  => "options",
        //                 'attribute_value'  => $value,
        //                 'attribute_type'   => 'select',
        //                 'attribute_code'   => strtolower(str_replace(' ','-',"options")),
        //                 'attribute_options'=> []
        //             ];
        //         endif;
        //     }
        // }

        // if(isset(self::$product['weight'])):
        //     self::$product['configurable_options'][] = [
        //         'attribute_label'  => "weight",
        //         'attribute_value'  => self::$product['weight'],
        //         'attribute_type'   => 'select',
        //         'attribute_code'   => strtolower(str_replace(' ','-',"weight")),
        //         'attribute_options'=> []
        //     ];
        // endif;

        return self::$product['configurable_options'] ?? null;
    }

}


