<?php

if (!function_exists('utils_select_image')) {
    /**
     * utils_select_image:
     * @param $id   vue挂载的id
     * @param $name 隐藏的input的name
     * @param $num 最多选择图片数量
     * @param $value [['url'=>'/storage/2024/06/14/10/27/1686706316.png']]
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author 周小C
     * createTime:2024/6/14 10:27
     */
    function utils_select_image($id='',$name='',$num = 1,$value='') {
        if ($id == '' || $name == '')  {
            dd('utils_select_imagehu方法id和name不能为空');
        }
        if (empty($value)) {
            $value = json_encode([]);
        }
        return view('commontemplate.image',compact('id','name','num','value'));
    }


    if (!function_exists('utils_wange_editor'))  {
        /**
         * utils_wange_editor: wangeditor富文本编辑器
         * @param $id vue挂载的id 也是textarea的name
         * @param $content 初始化内容
         * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
         * @author 周小C
         * createTime:2024/6/14 10:36
         */
        function utils_wange_editor($id='',$content='') {
            if (empty($id)) {
                dd('utils_wange_editor富文本id不能为空');
            }
            return view('commontemplate.wangeditor',compact('id','content'));
        }
    }
}
