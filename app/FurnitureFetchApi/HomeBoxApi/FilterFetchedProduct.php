<?php
namespace App\FurnitureFetchApi\HomeBoxApi;
use App\FurnitureFetchApi\WooProductProperties;
use Symfony\Component\DomCrawler\Crawler;
use App\FurnitureFetchApi\HandleFurnitureRequest;
class FilterFetchedProduct extends WooProductProperties{
    public const prefix_url = "https://www.homeboxstores.com/ae/en";
    public $crawler;
    public function set_product_item($product){
        // return product
        self::$product = $product ?? null;
    }
    public function set_product_name(){
        return self::$product['name']['en'] ?? self::$product['name']['ar'];
    }

    public function set_product_sku(){
        return self::$product['baseProductId'] ?? null;
    }

    public function set_product_id(){
        return self::$product['objectID'] ?? null;
    }

    public function set_stock_status(){
       return "IN_STOCK";
    }

    public function set_product_quantity(){
        return self::$product['stock_quantity'] ?? null;
    }

    public function set_product_price(){
        return self::$product['price'] ?? null;
    }

    public function set_regular_price(){
        return self::$product['wasPrice'] ?? null;
    }

    public function set_product_thumbnail(){
        self::$product['product_thumbnail'] = [
            'url'    => self::$product['thumbnailImg'] ?? null,
            'label'  => self::$product['name']['en'] ?? ''
        ];

        return self::$product['product_thumbnail'] ?? null;
    }

    public function set_product_currency(){
        return self::$product['currencyCode'] ?? null;
    }

    public function set_product_description(){
        return self::$product['summary']['en'] ?? null;
    }

    public function set_product_short_description(){
        // instant copy of single product
        //  *$fetch_single_product = new HandleFurnitureRequest();

        // access endpoint
        //  *$fetch_single_product->endpoint = self::prefix_url.self::$product['url'];
 
        // start fetch data in single product
        //  *$fetch_single_product->resolve_api($json= false,'single_product');

        //  *return $fetch_single_product->results;

        //  start fetch some data from crawler documents
        //  *return $this->crawler = new Crawler($fetch_single_product->results['single_product']);

        // get on product info by details from crawler
        // *$product_details = $this->crawler->filter('.tab-pane')->each(function (Crawler $node, $i) {
        // *   return $node->html();
        // *});
        // *$result = ($product_details ? implode(' ',$product_details) : '');
        return (self::$product['summary']['en'] ?? null);
    }

    public function set_product_categories(){
        $categories = [];
        return $categories ?? null;
    }

    public function set_product_variation(){
        return self::$product['gprDescription']['variants'] ?? null;
    }

    public function set_product_permalink(){
        $url = array_values(array_slice(self::$product['url'],0,1));
        self::$product['url'] = $url[0]['en'] ?? null;
        return self::prefix_url.self::$product['url'] ?? null;
    }

    public function set_product_gallery(){
        $galleries = self::$product['gallaryImages'];
        foreach($galleries as $gallery):
            self::$product['product_gallery'][] = [
                'thumbnail' => ['url'  => $gallery],
                'label'     => self::$product['name']['en'] ?? ''
            ];
        endforeach;

        return self::$product['product_gallery'] ?? null;
    }

    public function set_product_slug(){
        self::$product['slug'] = substr(strtolower(str_replace('/','-',self::$product['url'])),1);
        return self::$product['slug'] ?? null;
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


