<?php
/**
*
*/

namespace App\Common\Post;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Category extends Eloquent {

    protected $table = 'categories';

    protected $fillable = [
        'name'
    ];


    public function posts()
    {
        return $this->hasMany('App\Common\Post');
    }
}
