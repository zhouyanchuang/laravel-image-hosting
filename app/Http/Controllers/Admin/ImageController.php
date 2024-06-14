<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Lib\ResponseHelper;
use App\Model\Images\ImagesModel;
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
            $images->where('categoryid',$category_id);
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


}
