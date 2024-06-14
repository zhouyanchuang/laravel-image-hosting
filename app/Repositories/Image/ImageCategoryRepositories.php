<?php
namespace App\Repositories\Image;

use App\Model\Images\ImageCategorysModel;

class ImageCategoryRepositories
{
    /**
     * getCategoriesTree: 获取分类树
     * @param $categories
     * @param $prefix
     * @return array
     * @author 周小C
     * createTime:2024/6/6 14:15
     */
    public static function getCategoriesTree($categories, $prefix = '')
    {
        $tree = [];
        foreach ($categories as $category) {
            $tree[] = [
                'id' => $category->id,
                'title' => $prefix . $category->title,
                'parent_id' => $category->parent_id,
                'children' => self::getCategoriesTree($category->children, $prefix)
            ];
        }
        return $tree;
    }

    public static function showCategories()
    {
        $imageCategorys = ImageCategorysModel::with('children')->where('parent_id', 0)->get();
        $tree = self::getCategoriesTree($imageCategorys);
        return $tree;
    }

}
