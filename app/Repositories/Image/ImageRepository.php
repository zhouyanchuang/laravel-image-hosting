<?php
namespace App\Repositories\Image;
use App\Lib\ResponseHelper;
use App\Model\Images\ImagesModel;
use Illuminate\Support\Facades\Storage;

class ImageRepository
{
    public static function imageUpload($file = [],$category_id = 0)
    {
        if ($category_id<0) {
            $category_id =  0;
        }
        $folderPath = 'public/image/'.date('Ymd',time());
        $folder = Storage::directories($folderPath);
        if(!$folder){
            Storage::makeDirectory($folderPath);
        }
        $data = [];

        foreach($file as $key => $value){
            // 判断是否是图片
            if(!in_array($value->getClientMimeType(),['image/jpeg','image/png','image/gif','image/jpg'])){
                return ResponseHelper::error('文件类型错误');
            }
            $data[$key]['url'] = Storage::url(Storage::putFile($folderPath,$value));
            $data[$key]['name'] = $value->getClientOriginalName();
            $data[$key]['categoryid'] = $category_id;
        }
        $imageModel = ImagesModel::query()->insert($data);
        if (!$imageModel) {
            return ResponseHelper::error('上传失败');
        }

        return ResponseHelper::success([]);

    }
}
