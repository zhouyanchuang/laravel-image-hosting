<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('layui/css/layui.css') }}">
    <link rel="stylesheet" href="{{ asset('css/editor-style.css')  }}">
    <script type="text/javascript" src="{{asset('js/vue@3.4.20/vue.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/jquery.min.js')}}"></script>
    <script src="{{asset('layui/layui.js')}}"></script>
    <title>Document</title>
</head>
<body>
<div style="width: 50%;height:80%;margin: 0 auto;margin-top: 100px;">
    <div class="layui-row">
        <form class="layui-form" method="post" action="{{ url('admin/doAdd')}}">
            @csrf
            <div class="layui-form-item">
                <label class="layui-form-label">
                    单张
                </label>
                <div class="layui-inline">
                    {{utils_select_image('test1','test1Name')}}
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">
                    多张
                </label>
                <div class="layui-inline">
                    {{utils_select_image('test2','test2Name',3)}}
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">
                    富文本
                </label>
                {{utils_wange_editor('content')}}
            </div>
            <div class="layui-form-item">
                <label for="L_repass" class="layui-form-label">
                </label>
                <button  class="layui-btn" lay-filter="add" lay-submit="">
                    保存
                </button>
            </div>
        </form>
    </div>
</div>
</body>
</html>
