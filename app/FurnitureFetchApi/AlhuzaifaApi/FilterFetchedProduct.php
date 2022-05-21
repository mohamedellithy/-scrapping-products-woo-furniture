<?php
namespace App\FurnitureFetchApi\AlhuzaifaApi;
use App\FurnitureFetchApi\WooProductProperties;
use Symfony\Component\DomCrawler\Crawler;
use App\FurnitureFetchApi\HandleFurnitureRequest;
class FilterFetchedProduct extends WooProductProperties{
    public const  prefix_url = "https://alhuzaifa.com/";
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

            if(self::$product["type_product"] != 'variate'):
                // get on product info by details from crawler
                $parent_product = $this->crawler->filter('script[data-cfasync="false"][type="text/javascript"]')->each(function (Crawler $node, $i) {
                    // preg match all data from craweller
                    if(preg_match_all("~\{(?:[^{}]|(?R))*\}~",$node->text(),$matches)){
                        $match = $matches[1] ?? $matches[0];
                        // remove first braces
                        array_shift($match);
                        // convert match to json
                        return json_decode($match[0],true);
                    }
                });

                // trnasfere product scrapping with api details
                self::$product['sku']        = $parent_product[0]['sku'] ?? null;
                self::$product['price']      = $parent_product[0]['price'] ?? null;
                self::$product['categories'] = $parent_product[0]['category'] ?? null;
                self::$product['variant']    = $parent_product[0]['variant'] ?? null;
                self::$product['quantity']   = $parent_product[0]['quantity'] ?? null;
                self::$product['isVariation']= $parent_product[0]['isVariation'] ?? null;
            endif;
        endif;
    }
    public function set_product_name(){
        return self::$product['title']['rendered'] ?? null;
    }

    public function set_product_sku(){
        // get sku from product
        return self::$product_details['sku'] ?? self::$product['id'];
    }

    public function set_product_id(){
        return self::$product['id'] ?? null;
    }

    public function set_stock_status(){
        return self::$product['quantity'] > 0 ? 'IN_STOCK' : 'OUT_OF_STOCK';
    }

    public function set_product_price(){
        return self::$product['price'] ?? null;
    }

    public function set_regular_price(){
        if(self::$product["type_product"] != 'variate'):
            self::$product['regular_price'] = preg_replace('/[a-zA-Z]/i','',$this->crawler->filter('del .woocommerce-Price-amount')->text(""));
        endif;

        if(self::$product["type_product"] == 'variate'):
            self::$product['regular_price'] = self::$product['price'] ?? null;
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
            $AdditionDetails  = $this->crawler->filter('.woocommerce-product-details__short-description.dimension_container')->html();
            $AdditionDetails .= $this->crawler->filter('.product_extra_popup')->html();
        endif;
        return self::$product['excerpt']['rendered'] ? self::$product['excerpt']['rendered'].$AdditionDetails : null;
    }

    public function set_product_categories(){
        // if categories is empty
        if(self::$product['categories'] != null){
            foreach(self::$product['categories'] as $category):
                self::$product['categories_all'][] = [
                    'name'        => $category,
                    'url_key'     => strtolower(str_replace(' ','-',$category)),
                    'breadcrumbs' => null
                ];
            endforeach;
        }

        return self::$product['categories_all'] ?? null;
    }

    public function set_product_variation(){
        // get on product info by details from crawler
        if(self::$product["type_product"] != 'variate'):
            // manual scrapping variation
            self::$product['product_variation'] = $this->crawler->filter('script[data-cfasync="false"][type="text/javascript"]')->each(function (Crawler $node, $i) {
                if(preg_match_all("~\{(?:[^{}]|(?R))*\}~",$node->text(),$matches)){
                    $match = $matches[1] ?? $matches[0];
                    array_shift($match);
                    return $match;
                }
            });

            // filter and handle variations
            // remove parent product
            array_shift(self::$product['product_variation']);
            // start for loop products variate
            if(self::$product['product_variation'] != null){
                // loop variate
                foreach(self::$product['product_variation'] as $variate){
                    // handle variate
                    $variate_array = json_decode($variate[0],true);
                    if(isset($variate_array['parentId'])):
                        $variate_array['title']['rendered']   = $variate_array['name'] ?? null;
                        $variate_array['content']['rendered'] = self::$product['content']['rendered'] ?? null;
                        $variate_array['excerpt']['rendered'] = self::$product['excerpt']['rendered'] ?? null;
                        $variate_array['type_product']        = 'variate';
                        $variate_array['featured_media']      = null;
                        $variate_array['slug']                = strtolower(str_replace(' ','-',$variate_array['name']));
                        $variate_array['isVariation']         = $variate_array['isVariation'] ?? null;
                        $variate_array['categories']          = $variate_array['category'] ?? null;
                        $variate_all[] = $variate_array;
                    endif;
                }
                // depend on variate
                self::$product['product_variation'] = $variate_all ?? null;
            }

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

        //if(!isset($featured_image->results['attachments_media'])){
            // get on product info by details from crawler
            // $galleries = $this->crawler->filter('.woocommerce-product-gallery__thumbs .woocommerce-product-gallery__image')->each(function (Crawler $node, $i) {
            //     return $node->filter('a')->attr('href');
            // });

            // foreach($galleries as $gallery):
            //     self::$product['product_gallery'][] = [
            //         'thumbnail' => ['url'  => strtok($gallery,"?")],
            //         'label'     => self::$product['title']['rendered'] ?? null
            //     ];
            // endforeach;
        //}

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
        if(self::$product['isVariation'] == true):
            // handle details of variations
            $attributes = explode(':', self::$product['variant']);
            // handle value of variations
            $attributes = array_combine(['attribute_label','attribute_value'],$attributes);
            // color set variante
        endif;
        
        // any dynamic attribute or default is color
        self::$product['attributes'][] = [
            'attribute_label' => isset($attributes['attribute_label']) ? strtolower($attributes['attribute_label']) : "color",
            'attribute_value' => isset($attributes['attribute_value']) ? trim($attributes['attribute_value']) : null,
            'attribute_type'  => 'select',
            'attribute_code'  => isset($attributes['attribute_label']) ? strtolower($attributes['attribute_label']) : "color",
            'attribute_options'=> []
        ];

        return self::$product['attributes'] ?? null;
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
        self::$product['configurable_options'] = self::$product['attributes'] ?? null;
        return self::$product['configurable_options'] ?? null;
    }

}