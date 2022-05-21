<?php

namespace App\FurnitureFetchApi\MarinaHomeInteriorsApi;
use App\FurnitureFetchApi\HandleFurnitureRequest;
use App\FurnitureFetchApi\InterfaceFurnitureApi\FurnitureProducts as FurnitureProducts;
class FetchProducts extends HandleFurnitureRequest implements FurnitureProducts{
    public $endpoint = "https://www.marinahomeinteriors.com/rest/V1/products?searchCriteria[pageSize]=300&searchCriteria[currentPage]=1";
    public function __construct(){
        $this->resolve_api();
    }

    public function get_page_info(){
        return $this->results['products']['total_count'] ?? [];
    }

    public function get_attributes(){
       return null;
    }

    public function get_slugs(){
        $slugs = $this->results ? $this->results['products']['items'] : [];
        return $slugs;
    }
}
