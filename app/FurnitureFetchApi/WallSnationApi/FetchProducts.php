<?php

namespace App\FurnitureFetchApi\WallSnationApi;
use App\FurnitureFetchApi\HandleFurnitureRequest;
use App\FurnitureFetchApi\InterfaceFurnitureApi\FurnitureProducts as FurnitureProducts;
class FetchProducts extends HandleFurnitureRequest implements FurnitureProducts{
    public $endpoint = "https://www.wallsnation.com/collections/sofas.oembed";
    public function __construct(){
        $this->resolve_api();
    }

    public function get_page_info(){
        // others categories
        return [
            'beds',
            'armchairs',
            'chaise-lounges',
            'ottomans-poufs',
            'outdoor-furniture',
            'dressers',
            't-v-units',
            'consoles',
            'night-stands',
            'dining-tables',
            'coffee-tables',
            'side-tables'
        ];
    }

    public function get_attributes(){
        return [];
    }

    public function get_slugs(){
        if(!empty($this->results['products']['products'])){
            return ["category" => str_replace(" ","-",strtolower($this->results['products']['title'])),
                    "slugs"    => collect($this->results['products']['products'])->pluck('product_id')->toArray()];
        }
    }
}
