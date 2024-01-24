<?php

Route::group(['prefix'=>'file-manager'], function () {
   Route::get('test', function(){
       return 'test';
   });
});
