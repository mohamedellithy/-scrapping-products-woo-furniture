<?php
namespace App\FurnitureFetchApi\TheHomeApi;
use App\FurnitureFetchApi\WooProductProperties;
use Symfony\Component\DomCrawler\Crawler;
use App\FurnitureFetchApi\HandleFurnitureRequest;
class FilterFetchedProduct extends WooProductProperties{
    public const  prefix_url = "https://thehome.ae/";
    public static $product_details;
    public $crawler;
    public function set_product_item($product){
        // return product
        self::$product = $product ?? null;

        // if this parent product that have variations
        if(self::$product["type_product"] != 'variate'):

            // instant copy of single product
            $fetch_single_product = new HandleFurnitureRequest();

            // access endpoint
            $fetch_single_product->endpoint = self::$product['link'];

            // start fetch data in single product
            $fetch_single_product->resolve_api($json= false,'single_product');

            // start fetch some data from crawler documents
            $this->crawler = new Crawler($fetch_single_product->results['single_product']);
        endif;
    }
    public function set_product_name(){
        return self::$product['title']['rendered'] ?? null;
    }

    public function set_product_sku(){
        if(self::$product["type_product"] != 'variate'):
            self::$product['sku'] = preg_replace('/[a-zA-Z]/i','',$this->crawler->filter('.product_meta .sku')->text(""));
        endif;
        // get sku from product
        return self::$product['sku'] ?? self::$product['id'];
    }

    public function set_product_id(){
        return self::$product['id'] ?? null;
    }

    public function set_stock_status(){
        if(!isset(self::$product['is_in_stock']) || (self::$product['is_in_stock'] == true)):
            $status = "IN_STOCK";
        else:
            $status = "OUT_OF_STOCK";
        endif;
        return $status ?? null;
    }

    public function set_product_quantity(){
        return isset(self::$product['quantity']) ? self::$product['quantity'] : null;
    }

    public function set_product_price(){
        if(self::$product["type_product"] != 'variate'):
            self::$product['price'] = preg_replace('/[a-zA-Z]/i','',$this->crawler->filter('ins .woocommerce-Price-amount')->text(""));
        endif;

        return self::$product['price'] ?? null;
    }

    public function set_regular_price(){
        if(self::$product["type_product"] != 'variate'):
            self::$product['regular_price'] = preg_replace('/[a-zA-Z]/i','',$this->crawler->filter('del .woocommerce-Price-amount')->text(""));
        endif;

        return self::$product['regular_price'] ?? null;
    }

    public function set_product_thumbnail(){
        // call api furniture for get thubmnail image
        $featured_image  = new HandleFurnitureRequest();

        // check if thumbnail is exsit
        if(self::$product['featured_media'] == null):
            return null;
        endif;

        // set endpoint url of resolve request
        $featured_image->endpoint  = self::prefix_url."wp-json/wp/v2/media/".self::$product['featured_media'];

        // set resolve api calling
        $featured_image->resolve_api($json= true,'featured_image');

        // set image thubmnail url
        self::$product['product_thumbnail']['url']   = $featured_image->results['featured_image']['source_url'] ?? null;

        // set image thubmnail label
        self::$product['product_thumbnail']['label'] = $featured_image->results['featured_image']['title']['rendered'] ?? null;

        // if api return empty urls from api
        if(self::$product['product_thumbnail']['url'] == null):

            // if product type isnot variations
            if(self::$product["type_product"] != 'variate'):

                // manula scrapping with crawler
                self::$product['product_thumbnail']['url']   = $this->crawler->filter('figure > .woocommerce-product-gallery__image > a')->attr("href","");
                self::$product['product_thumbnail']['label'] = self::$product['title']['rendered'] ?? null;
            endif;
        endif;

        // return image
        return self::$product['product_thumbnail'] ?? null;
    }

    public function set_product_currency(){
        // if product type isnot variations
        if(self::$product["type_product"] != 'variate'):
            self::$product['currencyCode'] = $this->crawler->filter('del .woocommerce-Price-amount span.woocommerce-Price-currencySymbol')->text("");
        endif;

        return self::$product['currencyCode'] ?? null;
    }

    public function set_product_description(){
        return self::$product['content']['rendered'] ?? null;
    }

    public function set_product_short_description(){
        $AdditionDetails = "";

        // if product type isnot variations
        if(self::$product["type_product"] != 'variate'):
            // manual scrapping by using crawler
            $AdditionDetails  = $this->crawler->filter('.woocommerce-product-details__short-description')->html("");
            $AdditionDetails .= $this->crawler->filter('.woocommerce .rey-wcPanels .rey-wcPanel-inner')->html("");
        endif;

        return isset(self::$product['excerpt']['rendered']) ? self::$product['excerpt']['rendered'].$AdditionDetails : null;
    }

    public function set_product_categories(){
        if(self::$product["type_product"] != 'variate'):

            //.woocommerce div.product .rey-breadcrumbs-item
            self::$product['categories'] = $this->crawler->filter('.product_meta .posted_in a')->each(function (Crawler $node, $i){
                return $node->text();
            });

            // if categories is empty
            if(self::$product['categories'] != null):
                foreach(self::$product['categories'] as $category):
                    self::$product['categories_all'][] = [
                        'name'        => $category,
                        'url_key'     => strtolower(str_replace(' ','-',$category)),
                        'breadcrumbs' => null
                    ];
                endforeach;
            endif;

        endif;

        return self::$product['categories_all'] ?? null;
    }

    public function set_product_variation(){
        // get on product info by details from crawler
        if(self::$product["type_product"] != 'variate'):

            // manual scrapping variation
            self::$product['product_variation'] = json_decode($this->crawler->filter('form.cart')->attr("data-product_variations",""),true);

            // filter and handle variations
            // start for loop products variate
            if(self::$product['product_variation'] != null):
                // loop variate
                foreach(self::$product['product_variation'] as $variate):
                    $variate_array['id']                  = $variate['variation_id'] ?? null;
                    $variate_array['title']['rendered']   = self::$product['title']['rendered'] ? self::$product['title']['rendered'].' '.$variate['variation_id'] : null;
                    $variate_array['slug']                = self::$product['title']['rendered'] ? strtolower(str_replace(' ','-',self::$product['title']['rendered'])).'-'.$variate['variation_id'] : null;
                    $variate_array['quantity']            = $variate['max_qty'] != "" ? $variate['max_qty'] : null;
                    $variate_array['is_in_stock']         = $variate['is_in_stock'] ?? null;
                    $variate_array['attributes']          = $variate['attributes'] ?? null;
                    $variate_array['type_product']        = 'variate';
                    $variate_array['featured_media']      = $variate['image_id'] ?? null;
                    $variate_array['regular_price']       = $variate['display_regular_price'] ?? null;
                    $variate_array['price']               = $variate['display_price'] ?? null;
                    $variate_all[] = $variate_array;
                endforeach;
                // depend on variate
                self::$product['product_variation'] = $variate_all ?? null;
            endif;

            $this->is_variated = true;
        endif;

        return self::$product['product_variation'] ?? null;
    }

    public function set_product_permalink(){
        return self::$product['link'] ?? null;
    }

    public function set_product_gallery(){
        // call api furniture for get thubmnail image
        $featured_image  = new HandleFurnitureRequest();

        // set endpoint url of resolve request
        $featured_image->endpoint  = self::prefix_url."wp-json/wp/v2/media?parent=".self::$product['id'];

        // set resolve api calling
        $featured_image->resolve_api($json= true,'attachments_media');

        // foreach all medai attachments
        foreach($featured_image->results['attachments_media'] as $gallery):
            self::$product['product_gallery'][] = [
                'thumbnail' => ['url'  => $gallery['source_url'] ?? null],
                'label'     => $gallery['title']['rendered'] ?? null
            ];
        endforeach;

        return self::$product['product_gallery'] ?? null;
    }

    public function set_product_slug(){
        self::$product['url_key'] = self::$product['slug'] ?? null;
        return self::$product['url_key'] ?? null;
    }

    public function set_related_products(){
        return null;
    }

    public function set_attributes(){
        // if product is have variations
        if(!isset(self::$product['attributes']))
            return null;


        foreach(self::$product['attributes'] as $key => $attribute):
            $key = str_replace('attribute_pa_',"",$key);
            // any dynamic attribute or default is color
            self::$product['attributes_all'][] = [
                'attribute_label' => $key ?? null,
                'attribute_value' => $attribute ?? null,
                'attribute_type'  => 'select',
                'attribute_code'  => $key ?? null,
                'attribute_options'=> []
            ];
        endforeach;

        return self::$product['attributes_all'] ?? null;
    }

    public function set_special_price(){
        return null;
    }

    public function set_product_discount(){
        return self::$product['discount'] ?? null;
    }

    public function set_total_sale(){
        return null;
    }

    public function set_saved_value(){
        return null;
    }

    public function set_configurable_options(){
        self::$product['configurable_options'] = self::$product['attributes_all'] ?? null;
        return self::$product['configurable_options'] ?? null;
    }

}
