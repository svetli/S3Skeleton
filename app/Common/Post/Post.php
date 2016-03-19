<?php
/**
*
*/

namespace App\Common\Post;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Post extends Eloquent
{
    protected $table = 'posts';

    protected $fillable = [
        'title',
        'excerpt',
        'body',
        'status',
        'user_id',
        'image',
        'category_id',
        'seo_url'
    ];

    public function user()
    {
        return $this->belongsTo('App\Common\User', 'user_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo('App\Common\Category', 'category_id', 'id');
    }

    public function getCategoryName()
    {
        return $this->category->name;
    }

    public function getUserName()
    {
        return $this->user->username;
    }
}
