<?php
namespace App\FurnitureFetchApi\DanubeHomeApi;
use App\FurnitureFetchApi\WooProductProperties;
class FilterFetchedProduct extends WooProductProperties{
    public const prefix_url = "https://danubehome.com/media/catalog/product";
    public function set_product_name(){
        return self::$product[0]['name'] ?? null;
    }

    public function set_product_sku(){
        return self::$product[0]['sku'] ?? null;
    }

    public function set_product_id(){
        return self::$product[0]['id'] ?? null;
    }

    public function set_stock_status(){
        return self::$product[0]['stock_status'] ?? null;
    }

    public function set_product_price(){
        return self::$product[0]['price']['minimalPrice']['amount']['value'] ?? null;
    }

    public function set_regular_price(){
        return self::$product[0]['price']['regularPrice']['amount']['value'] ?? null;
    }

    public function set_product_thumbnail(){
        self::$product['thumbnail'] = [
           'url'    => self::prefix_url.(self::$product[0]['thumbnail']['path'] ?? null),
           'label'  => self::$product[0]['thumbnail']['label'] ?? null
        ];
        return self::$product['thumbnail'] ?? null;
    }

    public function set_product_currency(){
        return self::$product[0]['price']['regularPrice']['amount']['currency'] ?? null;
    }

    public function set_product_description(){
        return self::$product[0]['description']['html'] ?? null;
    }

    public function set_product_short_description(){
        return self::$product[0]['description']['html'] ?? null;
    }

    public function set_product_categories(){
        return self::$product[0]['categories'] ?? null;
    }

    public function set_product_variation(){
        return self::$product[0]['variants'] ?? null;
    }

    public function set_product_permalink(){
        return self::$product[0]['canonical_url'] ?? null;
    }

    public function set_product_gallery(){
        self::$product['gallery'] = [];
        foreach(self::$product[0]['media_gallery_entries'] as $gallery):
            self::$product['gallery'][] = [
                'thumbnail' =>[
                    'url'    => self::prefix_url.($gallery['file'] ?? null)
                ],
                'label'  => $gallery['label'] ?? null
            ];
        endforeach;
        return self::$product['gallery'] ?? null;
    }

    public function set_product_slug(){
        $data = explode('/',$this->product_permalink);
        return self::$product[0]['url_key'] ?? $data[count($data) - 1];
    }

    public function set_related_products(){
        return self::$product[0]['related_products'] ?? null;
    }

    public function set_attributes(){
        return self::$product[0]['attributes'] ?? null;
    }

    public function set_special_price(){
        return self::$product[0]['special_price'] ?? null;
    }

    public function set_product_discount(){
        return self::$product[0]['pricing']['discount'] ?? null;
    }

    public function set_total_sale(){
        return self::$product[0]['pricing']['salable_quantity'] ?? null;
    }

    public function set_saved_value(){
        return self::$product[0]['pricing']['you_save'] ?? null;
    }

    public function set_configurable_options(){
        return self::$product[0]['configurable_options'] ?? null;
    }

}


