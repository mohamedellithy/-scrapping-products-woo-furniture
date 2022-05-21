<?php 
namespace App\FurnitureFetchApi\InterfaceFurnitureApi;
interface FurnitureProducts {
    //public function startFetchData();
    //public function FetchData();

    // public function FilterFetchedData();

    // public function InsterFetchedData();
    public function get_slugs();
    public function get_page_info();
}