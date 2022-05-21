<?php
namespace App\FurnitureFetchApi\HomeCentreApi;
use App\FurnitureFetchApi\WooProductProperties;
use Symfony\Component\DomCrawler\Crawler;
use App\FurnitureFetchApi\HandleFurnitureRequest;
class FilterFetchedProduct extends WooProductProperties{
    public const prefix_url = "https://www.homecentre.com";
    public $crawler;
    public function set_product_item($product){
        // return product
        self::$product = $product ?? null;
    }
    public function set_product_name(){
        return self::$product['name']['en'] ?? self::$product['name']['ar'];
    }

    public function set_product_sku(){
        return self::$product['baseProductId'] ?? self::$product['objectID'];
    }

    public function set_product_id(){
        return self::$product['objectID'] ?? null;
    }

    public function set_stock_status(){
        return (isset(self::$product['inStock'])) && (self::$product['inStock'] == 1) ? "IN_STOCK":"OUT_OF_STOCK";
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
            'url'       => strtok(self::$product['333WX493H'] ?? self::$product['thumbnailImg'],'?') ?? null,
            'label'     => self::$product['name']['en'] ?? ''
        ];
        return self::$product['product_thumbnail'] ?? null;
    }

    public function set_product_currency(){
        return 'AED';
    }

    public function set_product_description(){
        return self::$product['summary']['en'] ?? null;
    }

    public function set_product_short_description(){
        return self::$product['summary']['en'] ?? null;
    }

    public function set_product_categories(){
        $categories = self::$product['categoryName']['en'] ?? [];
        foreach($categories as $category):
            $item = [
                'name'        => $category ?? null,
                'url_key'     => strtolower(str_replace(' ','-',$category ?? null)),
                'breadcrumbs' => null
            ];
            self::$product['categories'][] = $item;
        endforeach;
        return self::$product['categories'] ?? null;
    }

    public function set_product_variation(){
        return self::$product['variants'] ?? null;
    }

    public function set_product_permalink(){
        $url = array_values(array_slice(self::$product['url'],0,1));
        self::$product['url'] = $url[0]['en'] ?? null;
        return self::prefix_url.self::$product['url'] ?? null;
    }

    public function set_product_gallery(){
        array_shift(self::$product['gallaryImages']);
        $galleries = self::$product['gallaryImages']; 
        foreach($galleries as $gallery):
            self::$product['product_gallery'][] = [
                'thumbnail' => ['url'  => strtok($gallery ?? null,'?') ],
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


