<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Lib\ResponseHelper;
use App\Model\Images\ImagesModel;
use App\Repositories\Image\ImageCategoryRepositories;
use App\Repositories\Image\ImageRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    //
    public function index(Request $request)
    {
        $category_id = $request->get('category_id',-1);
        $images = ImagesModel::query();
        if ($category_id > -1) {
            $ids = ImageCategoryRepositories::getChildrenIds($category_id);
            $ids = array_merge([$category_id],$ids);
            $images->whereIn('categoryid',$ids);
        }

        $images = $images->orderBy('id','desc')->paginate(48);

        return ResponseHelper::success($images);
    }

    public function upload(Request $request)
    {
        $file = $request->file('files');
        $category_id = $request->get('category_id',0);
        return ImageRepository::imageUpload($file,$category_id);
    }

    public function delete(Request $request)
    {
        $ids = $request->get('ids','');
        if (empty($ids)) {
            return ResponseHelper::error('请选择要删除的图片');
        }

        $ids = explode(',',$ids);

        $result = ImagesModel::whereIn('id',$ids)->get();

        foreach ($result as $item) {
            if (!empty($item->url)) {
                $path = 'public/' . \Str::after($item->url, '/storage');
                Storage::delete($path);
                $item->delete();
            }
        }

        return ResponseHelper::success();
    }


}
