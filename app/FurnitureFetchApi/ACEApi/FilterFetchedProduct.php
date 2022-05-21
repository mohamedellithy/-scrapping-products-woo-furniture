<?php
namespace App\FurnitureFetchApi\ACEApi;
use App\FurnitureFetchApi\WooProductProperties;
use Symfony\Component\DomCrawler\Crawler;
use App\FurnitureFetchApi\HandleFurnitureRequest;
class FilterFetchedProduct extends WooProductProperties{
    public const prefix_url = "https://www.aceuae.com/en-ae";
    public $crawler;
    public function set_product_item($product){
        // return product
        self::$product = $product ?? null;
    }
    public function set_product_name(){
        return self::$product['name']['en'] ?? self::$product['name']['ar'];
    }

    public function set_product_sku(){
        return self::$product['default_SKU'] ?? self::$product['unique_id'];
    }

    public function set_product_id(){
        return self::$product['unique_id'] ?? null;
    }

    public function set_stock_status(){
        return (self::$product['inventory']['in_stock'] == false ? "OUT_STOCK":"IN_STOCK");
    }

    public function set_product_quantity(){
        return self::$product['inventory']['quantity'] ?? null;
    }

    public function set_product_price(){
        return self::$product['pricing']['sale_price'] ?? null;
    }

    public function set_regular_price(){
        return self::$product['pricing']['base_price'] ?? null;
    }

    public function set_product_thumbnail(){
        self::$product['product_thumbnail'] = [
            'url'  => self::$product['medias'][0]['defaultMediaUrl'] ?? self::$product['medias'][1]['defaultMediaUrl'],
            'label'     => self::$product['title']['en'] ?? ''
        ];
        return self::$product['product_thumbnail'] ?? null;
    }

    public function set_product_currency(){
        return self::$product['pricing']['currency_code']['en'] ?? null;
    }

    public function set_product_description(){
        return self::$product['description']['en'] ?? null;
    }

    public function set_product_short_description(){
        $addition_description = isset(self::$product['key_features']['en']) ? implode('<br/>',self::$product['key_features']['en']) : null;
        return (self::$product['description']['en'] ?? null).($addition_description ?? null);
    }

    public function set_product_categories(){
        $categories = [];
        $count_levels   = count(self::$product['categories']['en']);
        $category_level = self::$product['categories']['en'] ?? [];
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
        return self::$product['variants'] ?? null;
    }

    public function set_product_permalink(){
        return self::$product['landing_page_base_url'] ? self::prefix_url.strtok(self::$product['landing_page_base_url'],'.') : null;
    }

    public function set_product_gallery(){
        array_shift(self::$product['medias']);
        $galleries = self::$product['medias'];
        foreach($galleries as $gallery):
            self::$product['product_gallery'][] = [
                'thumbnail' => ['url'  => $gallery['defaultMediaUrl'] ?? null ],
                'label'     => self::$product['title']['en'] ?? ''
            ];
        endforeach;

        return self::$product['product_gallery'] ?? null;
    }

    public function set_product_slug(){
        $slug = explode('/',strtok(self::$product['landing_page_base_url'],'.'));
        return $slug[count($slug) - 1] ?? null;
    }

    public function set_related_products(){
        return null;
    }

    public function set_attributes(){
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


