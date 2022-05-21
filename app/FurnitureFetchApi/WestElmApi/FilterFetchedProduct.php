<?php
namespace App\FurnitureFetchApi\WestElmApi;
use App\FurnitureFetchApi\WooProductProperties;
class FilterFetchedProduct extends WooProductProperties{
    public const prefix_url = "https://www.westelm.ae/";
    public $crawler;
    public function set_product_item($product){
        // return product
        self::$product = $product ?? null;
    }
    public function set_product_name(){
        return self::$product['title']['en'] ?? self::$product['title']['ar'];
    }

    public function set_product_sku(){
        return self::$product['sku'] ?? null;
    }

    public function set_product_id(){
        return self::$product['nid'] ?? null;
    }

    public function set_stock_status(){
        return (self::$product['stock_quantity'] == null ? "OUT_STOCK":"IN_STOCK");
    }

    public function set_product_quantity(){
        return self::$product['stock_quantity'] ?? null;
    }

    public function set_product_price(){
        return self::$product['final_price']['en'] ?? null;
    }

    public function set_regular_price(){
        return self::$product['original_price']['en'] ?? null;
    }

    public function set_product_thumbnail(){
        self::$product['product_thumbnail'] = [
            'url'  => self::$product['media'][0]['url'] ?? self::$product['media'][1]['url'],
            'label'     => self::$product['title']['en'] ?? ''
        ];
        return self::$product['product_thumbnail'] ?? null;
    }

    public function set_product_currency(){
        return self::$product['currencyCode'] ?? null;
    }

    public function set_product_description(){
        return self::$product['body']['en'] ?? null;
    }

    public function set_product_short_description(){
        return self::$product['body']['en'] ?? null;
    }

    public function set_product_categories(){
        $categories = [];
        $count_levels   = count(self::$product['field_category_name']['en']);
        $category_level = self::$product['field_category_name']['en'] ?? [];
        for($level = 0; $level < $count_levels; $level++) {
            foreach($category_level['lvl'.$level] as $category):
                $parent  = null;
                if($level > 0){
                    $category_arr  = explode('>',$category);
                    $parent[] =[
                        'category_name' => trim($category_arr[$level - 1]) ?? null,
                        'url_key'       => strtolower(str_replace(' ','-',trim($category_arr[$level - 1]))) ?? null
                    ];
                    $category = trim($category_arr[count($category_arr) - 1]);
                }

                $item= [
                    'name'        => $category,
                    'url_key'     => strtolower(str_replace(' ','-',$category)),
                    'breadcrumbs' => $parent
                ];

                $categories[] = $item;
            endforeach;
        }
        return $categories ?? null;
    }

    public function set_product_variation(){
        return self::$product['gprDescription']['variants'] ?? null;
    }

    public function set_product_permalink(){
        return self::$product['url']['en'] ? self::prefix_url.strtok(self::$product['url']['en'],'.') : null;
    }

    public function set_product_gallery(){
        array_shift(self::$product['media']);
        $galleries = self::$product['media']; 
        foreach($galleries as $gallery):
            self::$product['product_gallery'][] = [
                'thumbnail' => ['url'  => $gallery['url']],
                'label'     => self::$product['title']['en'] ?? ''
            ];
        endforeach;

        return self::$product['product_gallery'] ?? null;
    }

    public function set_product_slug(){
        $data = explode('/',strtok(self::$product['url']['en'],'.'));
        return $data[count($data) - 1] ?? null;
    }

    public function set_related_products(){
        return null;
    }

    public function set_attributes(){
        if(isset(self::$product['attr_delivery_ways']['en'])):
            $delivery_ways = is_array(self::$product['attr_delivery_ways']['en']) ? implode(' - ',self::$product['attr_delivery_ways']['en']) : self::$product['attr_delivery_ways']['en'];
            self::$product['attributes'][] = [
                'attribute_label'  => 'delivery_ways',
                'attribute_value'  => $delivery_ways ?? null,
                'attribute_type'   => 'select',
                'attribute_code'   => 'delivery_ways',
                'attribute_options'=> []
            ];
        endif;
        
        if(isset(self::$product['attr_product_brand']['en'])):
            $brand = is_array(self::$product['attr_product_brand']['en']) ? implode(' - ',self::$product['attr_product_brand']['en']) : self::$product['attr_product_brand']['en'];
            self::$product['attributes'][] = [
                'attribute_label'  => 'product_brand',
                'attribute_value'  => $brand ?? null,
                'attribute_type'   => 'select',
                'attribute_code'   => 'product_brand',
                'attribute_options'=> []
            ];
        endif;

        if(isset(self::$product['attr_size']['en'])):
            $size = is_array(self::$product['attr_size']['en']) ? implode(' - ',self::$product['attr_size']['en']) : self::$product['attr_size']['en'];
            self::$product['attributes'][] = [
                'attribute_label'  => 'size',
                'attribute_value'  => $size ?? null,
                'attribute_type'   => 'select',
                'attribute_code'   => 'size',
                'attribute_options'=> []
            ];
        endif;

        if(isset(self::$product['attr_line']['en'])):
            $line = is_array(self::$product['attr_line']['en']) ? implode(' - ',self::$product['attr_line']['en']) : self::$product['attr_line']['en'];
            self::$product['attributes'][] = [
                'attribute_label'  => 'line',
                'attribute_value'  => $line ?? null,
                'attribute_type'   => 'select',
                'attribute_code'   => 'line',
                'attribute_options'=> []
            ];
        endif;

        if(isset(self::$product['attr_color']['en'])):
            $colors = is_array(self::$product['attr_color']['en']) ? implode(' - ',self::$product['attr_color']['en']) : self::$product['attr_color']['en'];
            self::$product['attributes'][] = [
                'attribute_label'  => 'colors',
                'attribute_value'  => $colors ?? null,
                'attribute_type'   => 'select',
                'attribute_code'   => 'colors',
                'attribute_options'=> []
            ];
        endif;

        if(isset(self::$product['attr_gender']['en'])):
            $gender = is_array(self::$product['attr_gender']['en']) ? implode(' - ',self::$product['attr_gender']['en']) : self::$product['attr_line']['en'];
            self::$product['attributes'][] = [
                'attribute_label'  => 'gender',
                'attribute_value'  => $gender ?? null,
                'attribute_type'   => 'select',
                'attribute_code'   => 'gender',
                'attribute_options'=> []
            ];
        endif;
        return self::$product['attributes'] ?? null;
    }

    public function set_special_price(){
        return null;
    }

    public function set_product_discount(){
        return self::$product['discount']['en'] ?? null;
    }

    public function set_total_sale(){
        return null;
    }

    public function set_saved_value(){
        return null;
    }

    public function set_configurable_options(){
        return self::$product['configurable_options'] ?? null;
    }

}


