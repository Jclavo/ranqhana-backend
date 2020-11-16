<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;

class LanguageService{

    const SYSTEM_ID = '';
    const SYSTEM_MODEL = 'App\Models\System';

    const URL = 'getTranslation';
    private $client = null;


    function __construct()
    {
    }

    private function setBasicConfigurations(){
        return new Client( ['base_uri' => env('APP_URL_TAAPAQ'),
                            'headers' =>  [ 'Authorization' => "Bearer " . Auth::user()->api_token ]
        ]);
    }

    public function getSystemMessage($key, $locale = null){

        empty($locale) ? $locale = App::getLocale() : null;
        
        return $this->getTranslation($key,LanguageService::SYSTEM_ID,LanguageService::SYSTEM_MODEL,$locale);

    }

    private function getTranslation($key,$translationable_id,$translationable_type,$locale){

        //set basic configurations
        $this->client = $this->setBasicConfigurations();

        $translation = '';

        $response = $this->client->request('POST', LanguageService::URL, [

            'form_params' => [
                'key' => $key,
                // 'translationable_id' => $translationable_id,
                'translationable_type' => $translationable_type,
                'locale' => $locale,
            ]
        ]);
        
        $body = $response->getBody()->getContents();;
        $body = json_decode($body);

        if($body->status){
            if(!empty($body->result)){
                $translation = $body->result[0]->value;
            }   
        }

        if(empty($translation)) return $key;
        else return $translation; 
    }

    // public function getTranslation(){

    //     $client = new Client( ['headers' => 
    //         [
    //             'Authorization' => "Bearer " . Auth::user()->api_token
    //         ]
    //     ]);

    //     $response = $client->get($this->url);

    //     $body = $response->getBody()->getContents();;
    //     $body = json_decode($body);
    //     $status = $body->status;

    //     if (true) {
    //         # code...
    //     }
    // }

}