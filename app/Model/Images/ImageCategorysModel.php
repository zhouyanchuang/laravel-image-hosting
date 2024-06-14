<?php

namespace App\Model\Images;

use Illuminate\Database\Eloquent\Model;

class ImageCategorysModel extends Model
{
    //
    protected $table = 'image_category';
    // 设置黑名单
    protected $guarded = [];

    // 获取子分类
    public function children()
    {
        return $this->hasMany(ImageCategorysModel::class, 'parent_id', 'id');
    }
}
