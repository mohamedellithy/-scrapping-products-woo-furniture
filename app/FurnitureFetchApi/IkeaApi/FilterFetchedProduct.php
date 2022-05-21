<?php
namespace App\FurnitureFetchApi\IkeaApi;
use App\FurnitureFetchApi\WooProductProperties;
use Symfony\Component\DomCrawler\Crawler;
use App\FurnitureFetchApi\HandleFurnitureRequest;
class FilterFetchedProduct extends WooProductProperties{
    public const prefix_url = "https://www.ikea.com/ae/en/";
    public $crawler;
    public function set_product_item($product){
        // return product
        self::$product = $product ?? null;
    }
    public function set_product_name(){
        return self::$product['name'] ?? null;
    }

    public function set_product_sku(){
        return self::$product['itemNo'] ?? null;
    }

    public function set_product_id(){
        return self::$product['id'] ?? null;
    }

    public function set_stock_status(){
        return 'IN_STOCK';
    }

    public function set_product_price(){
        return self::$product['priceNumeral'] ?? null;
    }

    public function set_regular_price(){
        return self::$product['price']['wholeNumber'] ?? null;
    }

    public function set_product_thumbnail(){
        self::$product['product_thumbnail'] = [
            'url'  => self::$product['contextualImageUrl'] ?? self::$product['mainImageUrl'],
            'label'=> self::$product['mainImageAlt'] ?? ''
        ];
        return self::$product['product_thumbnail'] ?? null;
    }

    public function set_product_currency(){
        return self::$product['currencyCode'] ?? null;
    }

    public function set_product_description(){
        return self::$product[0]['description'] ?? null;
    }

    public function set_product_short_description(){
        return self::$product[0]['description']['html'] ?? null;
    }

    public function set_product_categories(){
        return self::$product['categories'] ?? null;
    }

    public function set_product_variation(){
        return self::$product['gprDescription']['variants'] ?? null;
    }

    public function set_product_permalink(){
        return self::$product['pipUrl'] ?? null;
    }

    public function set_product_gallery(){
         // instant copy of single product
         $fetch_single_product = new HandleFurnitureRequest();

         // access endpoint
         $fetch_single_product->endpoint = self::prefix_url."p/".self::$product['url_key'];
 
         // start fetch data in single product
         $fetch_single_product->resolve_api($json= false,'single_product');

         // start fetch some data from crawler documents
         $this->crawler = new Crawler($fetch_single_product->results['single_product']);

        // get on product info by details from crawler
        $galleries = $this->crawler->filter('.pip-media-grid__grid .pip-media-grid__media-container img')->each(function (Crawler $node, $i) {
            return $node->attr('src');
        });

        foreach($galleries as $gallery):
            self::$product['product_gallery'][] = [
                'thumbnail' => ['url'  => strtok($gallery,"?")],
                'label' => self::$product['mainImageAlt'] ?? ''
            ];
        endforeach;

        return self::$product['product_gallery'] ?? null;
    }

    public function set_product_slug(){
        $full_slug   = parse_url(self::$product['pipUrl'],PHP_URL_PATH);
        $handle_slug = explode('/', $full_slug);
        return self::$product['url_key'] = $handle_slug[ count($handle_slug) - 2 ];
    }

    public function set_related_products(){
        return null;
    }

    public function set_attributes(){
        self::$product['attributes'][] = [
            'attribute_label' => 'type-name',
            'attribute_value' => self::$product['typeName'],
            'attribute_type'  => 'select',
            'attribute_code'  => 'type-name',
            'attribute_options'=> []
        ];

        self::$product['attributes'][] = [
            'attribute_label' => 'item-measure-reference-text',
            'attribute_value' => self::$product['itemMeasureReferenceText'],
            'attribute_type'  => 'select',
            'attribute_code'  => 'item-measure-reference-text',
            'attribute_options'=> []
        ];

        $variate = explode(',',self::$product['mainImageAlt']);
        $color   = ($variate[1] ?? null);

        if( ($variate[2] != null) && (trim($variate[2]) != trim(self::$product['itemMeasureReferenceText'])) ){
            $color .='/'.$variate[2];
        }

        self::$product['attributes'][] = [
            'attribute_label' => 'colors',
            'attribute_value' => $color ?: '',
            'attribute_type'  => 'select',
            'attribute_code'  => 'colors',
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
        $variate = explode(',',self::$product['mainImageAlt']);
        $color   = $variate[1];

        if( ($variate[2] != null) && (trim($variate[2]) != trim(self::$product['itemMeasureReferenceText'])) ){
            $color .='/'.$variate[2];
        }

        self::$product['configurable_options'][] = [
            'attribute_label' => 'item-measure-reference-text',
            'attribute_value' => $variate[2] ?? self::$product['itemMeasureReferenceText'],
            'attribute_type'  => 'select',
            'attribute_code'  => 'item-measure-reference-text',
            'attribute_options'=> []
        ];


        self::$product['configurable_options'][] = [
            'attribute_label' => 'colors',
            'attribute_value' => $color ?? '',
            'attribute_type'  => 'select',
            'attribute_code'  => 'colors',
            'attribute_options'=> []
        ];


        return self::$product['configurable_options'] ?? null;
    }

}


