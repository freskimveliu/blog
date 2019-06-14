<?php

Route::any('/{any}', function (){
    return view('app');
})->where('any','.*');
