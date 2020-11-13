<?php

use Dotenv\Repository\RepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Zttp\Zttp;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/city/weather', function() {
    $apiKey = config('services.weatherbit.key');

    // $country = request('country');
    $city = request('city');
    $state = request('state');

    $response = Zttp::get("https://api.weatherbit.io/v2.0/current?city=$city&country=MY&state=$state&key=$apiKey");
    
    /**
     * Return direct data
     * $result = $response->json();
     * return $result['data'][0];
     */

    return $response->json();
});

Route::get('/city/forecast', function() {
    $apiKey = config('services.weatherbit.key');

    // $country = request('country');
    $city = request('city');
    $state = request('state');

    $response = Zttp::get("https://api.weatherbit.io/v2.0/forecast/daily?city=$city&country=MY&state=$state&days=6&key=$apiKey");
    
    return $response->json();
});