<?php

namespace App\FurnitureFetchApi\EbarzaApi;
use App\FurnitureFetchApi\HandleFurnitureRequest;
use App\FurnitureFetchApi\InterfaceFurnitureApi\FurnitureProducts as FurnitureProducts;
class FetchProducts extends HandleFurnitureRequest implements FurnitureProducts{
    public $endpoint = "https://www.ebarza.com/collections/all-furniture.oembed";
    public function __construct(){
        $this->resolve_api();
    }

    public function get_page_info(){
        return [];
    }

    public function get_attributes(){
        return [];
    }

    public function get_slugs(){
        $slugs = $this->results ? $this->results['products']['products'] : [];
        if(!empty($slugs)){
            return collect($slugs)->pluck('product_id')->toArray();
        }
    }
}
