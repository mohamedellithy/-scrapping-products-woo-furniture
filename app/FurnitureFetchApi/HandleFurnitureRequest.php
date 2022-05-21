<?php

namespace App\FurnitureFetchApi;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
class HandleFurnitureRequest{
    public $results;
    public $endpoint = "https:://localhost:8000/1";
    public function resolve_api($json = true,$params = 'products',$type="get",$body=null){
        $user_agent = " Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.90 Safari/537.36 RuxitSynthetic/1.0 v4759262290 t7871929063389756869 athfa3c3975 altpub cvcv=2 smf=0";
        // here create api request
        $response = Http::withHeaders([
            'User-Agent' => $user_agent
        ])->withOptions([
            'verify'           => false,
            'decode_content'   => $json
        ]);

        if($body != null){
            $response = $response->withBody($body,'json');
        }

        $response = $response->$type($this->endpoint);

        if($response->successful()){
            $this->results  = [
                'status'  => $response->status(),
                'headers' => $response->headers(),
                $params   => $json ? $response->json() : $response->body(),
            ];
        }

        if($response->failed()){
            $this->results  = [
                'status'  => $response->status(),
                'headers' => $response->headers(),
                $params   => $response->body(),
            ];
        }

    }

    public function set_time_out_for_long_resuest(){
        set_time_limit(2000000000);
    }
}
