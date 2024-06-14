<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Lib\ResponseHelper;
use App\Model\Images\ImageCategorysModel;
use App\Repositories\Image\ImageCategoryRepositories;
use Illuminate\Http\Request;

class ImageCategoryController extends Controller
{
    //
    public function addCategory(Request $request)
    {
        $parent_id = $request->get('parent_id', 0);
        $title = $request->get('title', '');

        // 判断是否存在
        $imageCategory  = ImageCategorysModel::query()->where('title', $title)->where('parent_id', $parent_id)->first();
        if ($imageCategory) {
            return ResponseHelper::error('分类已存在');
        }

        $imageCategory = new ImageCategorysModel();
        $imageCategory->title = $title;
        $imageCategory->parent_id = $parent_id;
        $imageCategory->save();
        return ResponseHelper::success([],'添加成功');
    }

    // 获取所有分类
    public function getAllImageCategory(Request $request)
    {
        $tree = ImageCategoryRepositories::showCategories();
        return ResponseHelper::success(['tree'=>$tree]);
    }

    public function editCategory(Request $request)
    {
        $id = $request->get('id', 0);
        $title = $request->get('title', '');

        $imageCategory = ImageCategorysModel::query()->where('id', $id)->first();

        if (!$imageCategory) {
            return ResponseHelper::error('分类不存在');
        }
        // 判断是否存在
        $imageCategoryexist  = ImageCategorysModel::query()->where('title', $title)->where('parent_id', $imageCategory->parent_id)->whereNotIn('id',[$id])->first();
        if ($imageCategoryexist) {
            return ResponseHelper::error('分类已存在');
        }

        $imageCategory->title = $title;

        $imageCategory->save();

        return ResponseHelper::success([],'修改成功');
    }
}
