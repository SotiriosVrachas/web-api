<?php

use Illuminate\Http\Request;

use App\Artist;
use App\Http\Resources\CatalogResource;

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

Route::namespace('Api')->group(function() {

    /*
     * Artists
     */
    Route::prefix('artists')->group(function () {
        Route::get('/', 'ArtistController@index')->name('artist.index');
        Route::get('/{id}', 'ArtistController@show')->name('artist.show');
        Route::post('/create', 'ArtistController@store')->name('artist.store');
        Route::put('/{id}', 'ArtistController@update')->name('artist.update');
        Route::delete('/{id}', 'ArtistController@destroy')->name('artist.destroy');
    });

});
