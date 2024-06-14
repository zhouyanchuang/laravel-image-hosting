<style>
    #editor—wrapper-{{ $id}}     {
        border: 1px solid #ccc;
        z-index: 100; /* 按需定义 */
    }

    #toolbar-container-{{$id}}     {
        border-bottom: 1px solid #ccc;
    }

    #editor-container-{{$id}}     {
        height: 400px;
    }
    .none-data {
        height: 200px;
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: #fff;
        color: #ccc;
        border: 1px dashed #ccc;
    }
    .image-max {
        width: 100%;
        height: 100%;
    }

    .public-margin {
        margin-left: 2%;
        margin-top: 2%;
    }

    .image-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-start; /* 左对齐 */
    }

    .image-container .image-item {
        width: calc(100% / 12 - 10px); /* 一行显示12个 */
        padding-top: calc(100% / 12 - 10px); /* 保持正方形比例 */
        margin-bottom: 20px;
        background-size: 70%;
        background-repeat: no-repeat;
        background-position: center;
        margin-left: 8px;
        border: 1px dashed #ccc;
        border-radius: 5px;
        cursor: pointer
    }

    .image-container .image-item:hover {
        border: 1px solid #1E9FFF;
    }

    .image-item.selected {
        border: 1px solid #1E9FFF; /* 选中时的边框颜色 */
    }

    .image-container {
        max-width: 100%;
        padding: 10px;
    }

    .pagination {
        display: flex;
        justify-content: center;
        padding: 10px 0;
    }

    #imageCategoryTree .layui-btn-group.layui-tree-btnGroup:first-of-type {
        display: none;
    }

    #page {
        position: absolute;
        bottom: 0;
    }
</style>
<div id="editor—wrapper-{{ $id}}">
    <div id="toolbar-container-{{$id}}"><!-- 工具栏 --></div>
    <div id="editor-container-{{$id}}"><!-- 编辑器 --></div>
    {{--  定义textarea  --}}
    <textarea id="textarea-{{$id}}" style="display:none;" name="{{$id}}"></textarea>
</div>

<script type="text/html" id="wangeditor-image-template-{{$id}}">
    <div class="layui-container image-max">
        <div class="layui-row image-max">
            <!-- 左边部分 -->
            <div class="layui-col-lg2" style="height: 99%;border-right: 1px dashed #ccc;overflow: auto;">
                <div class="layui-row">
                    <div class="public-margin layui-col-12" style="padding-top: 30px">
                        <div data-id="0" class="layui-tree-set layui-tree-setHide">
                            <div class="layui-tree-entry">
                                <div class="layui-tree-main">
                                    <span class="layui-tree-iconClick">
                                        <i class="layui-icon layui-icon-file"></i>
                                    </span>
                                    <span class="layui-tree-txt" @click="defaultGetAllImage">默认分类</span>
                                </div>
                            </div>
                        </div>
                        <div id="imageCategoryTree"></div>
                    </div>
                </div>
            </div>
            <!-- 右边部分 -->
            <div class="layui-col-lg10" style="height: 99%;">
                <div class="layui-row">
                    <div class="public-margin layui-col-12">
                        <button class="layui-btn layui-btn-sm  layui-btn-normal" @click="buttonAddImageCategory"><i
                                class="layui-icon layui-icon-addition"></i>添加顶级分类
                        </button>
                        <button class="layui-btn layui-btn-sm  layui-btn-normal" @click="triggerFileUpload"><i
                                class="layui-icon layui-icon-upload"></i>上传图片
                        </button>
                        <button class="layui-btn layui-btn-sm  layui-btn-danger" @click="delImage"><i
                                class="layui-icon layui-icon-delete"></i>删除图片
                        </button>
                        <!-- 隐藏的文件输入框 -->
                        <input
                            type="file"
                            ref="fileInput"
                            style="display: none"
                            @change="handleFileUpload"
                            multiple
                        />
                    </div>
                    <div class="layui-col-12">
                        <div class="image-container">
                            <div class="image-item" :style="{ backgroundImage: `url(${item.url})` }"
                                 v-for="item in imageList"
                                 @mouseover="(event)=>handleMouseOver(event,item.name)"
                                 @click="(event)=>handleImageClick(event,item)"
                                 :key="item.id"></div>
                        </div>
                        {{---无数据显示--}}
                        <div class="none-data layui-col-12" v-if="imageList.length===0">
                            <span>无数据</span>
                        </div>
                        <div class="public-margin" id="page"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</script>
<script>
    window.Laravel = {!! json_encode([
        'csrfToken' => csrf_token(),
    ]) !!};
</script>
<script src="{{asset('js/editor-index.js')}}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const {createEditor, createToolbar} = window.wangEditor
        const {createApp, ref, onMounted} = Vue;
        const {tree, laypage} = layui;
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': window.Laravel.csrfToken
            }
        });
        createApp({
            setup() {
                // 图片总数 用于分页
                const total = ref(0);
                // 图片分类
                const imageCategory = ref([]);
                // 图片列表
                const imageList = ref([]);
                // 选中图片分类
                const imageCategoryId = ref(-1);
                // 添加分类名称
                const addCategoryTitle = ref('未命名');
                // 添加分类父级id
                const addCategoryParentId = ref(0);
                // 编辑分类名称的ID
                const updateCategoryId = ref(0);
                // 编辑分类名称
                const updateCategoryTitle = ref('');
                // 文件input
                const fileInput = ref(null);
                // 点击选中的图片
                const clickSelectImages = ref([]);
                // 封装请求
                const request = (url, data, index) => {
                    $.post(url, data, function (resp) {
                        if (resp.code == 0) {
                            layer.alert(resp.msg, {
                                icon: 5
                            })
                            return
                        } else if (resp.code == 1) {
                            layer.alert(resp.msg, {icon: 6})
                            // 关闭弹窗
                            layer.close(index);
                        }
                    })
                }

                // 分页
                const renderPagination = () => {
                    laypage.render({
                        elem: 'page',
                        count: total.value,
                        theme: '#1E9FFF',
                        limit: 48,
                        layout: ['count', 'prev', 'page', 'next', 'skip']
                        , jump: function (obj, first) {
                            $.get('/admin/image?page=' + obj.curr, {
                                category_id: imageCategoryId.value,
                            }, function (res) {
                                if (res.code == 0) {
                                    layer.msg(res.msg, {icon: 2})
                                    return
                                }
                                imageList.value = res.data.data
                                total.value = res.data.total
                            })
                        }
                    });
                }

                // 初始化富文本框
                const init = () => {
                    // 初始化富文本框配置
                    var editorConfig = {
                        MENU_CONF: {
                            // 上传图片
                            uploadImage: {
                                // 自定义上传
                                customBrowseAndUpload(insertFn) {
                                    layer.open({
                                        type: 1,
                                        area: ["80%", "80%"],
                                        shadeClose: true,
                                        shade: 0.8,
                                        title: '上传图片',
                                        content: $('#wangeditor-image-template-{{$id}}').html(),
                                        btn: ['使用选中图片', '取消'],
                                        success: function (layero, index) {
                                            const tempApp = createApp({
                                                setup() {
                                                    // 鼠标移入图片时显示提示
                                                    const handleMouseOver = (event, name) => {
                                                        const target = event.currentTarget;
                                                        layer.tips(name, target, {time: 1000});

                                                    }
                                                    // 鼠标点击图片时选中图片和取消选中
                                                    const handleImageClick = (event, item) => {
                                                        const target = event.currentTarget;
                                                        target.classList.toggle('selected'); // 切换选中状态
                                                        if (target.classList.contains('selected')) {
                                                            clickSelectImages.value.push(item);
                                                        } else {
                                                            const index = clickSelectImages.value.findIndex(img => img.id === item.id);
                                                            if (index !== -1) {
                                                                clickSelectImages.value.splice(index, 1);
                                                            }
                                                        }
                                                    };
                                                    // ------添加顶级图片分类按钮-------------
                                                    const buttonAddImageCategory = () => {
                                                        layer.prompt({
                                                            title: '请输入图片分类名称',
                                                            formType: 0
                                                        }, function (title, index, elem) {
                                                            if (title.trim() == '') {
                                                                layer.msg('分类名称不能为空')
                                                                return
                                                            }
                                                            addCategoryParentId.value = 0;
                                                            addCategoryTitle.value = title;
                                                            addImageCategory(index)
                                                        })
                                                    }
                                                    // --------添加图片分类----------
                                                    const addImageCategory = (index) => {
                                                        $.get('/admin/image/addCategory', {
                                                            title: addCategoryTitle.value,
                                                            parent_id: addCategoryParentId.value
                                                        }, function (resp) {
                                                            if (resp.code == 0) {
                                                                layer.alert(resp.msg, {
                                                                    icon: 5
                                                                })
                                                            } else if (resp.code == 1) {
                                                                layer.alert(resp.msg, {icon: 6})
                                                                // 关闭弹窗
                                                                layer.close(index);
                                                            }
                                                            getAllImageCategory()
                                                        });
                                                    }
                                                    // -------------修改图片分类-----------------
                                                    const editImageCategory = (index) => {
                                                        $.get('/admin/image/editCategory', {
                                                            id: updateCategoryId.value,
                                                            title: updateCategoryTitle.value
                                                        }, function (resp) {
                                                            if (resp.code == 0) {
                                                                layer.alert(resp.msg, {
                                                                    icon: 5
                                                                })

                                                            } else if (resp.code == 1) {
                                                                layer.alert(resp.msg, {icon: 6})
                                                                // 关闭弹窗
                                                                layer.close(index);
                                                            }
                                                            getAllImageCategory()

                                                        })
                                                    }
                                                    // ------------获取图片分类-----------------
                                                    const getAllImageCategory = () => {
                                                        $.get('/admin/image/getAllImageCategory', {}, function (resp) {
                                                            if (resp.code == 1) {
                                                                imageCategory.value = resp.data.tree;
                                                                tree.render({
                                                                    elem: '#imageCategoryTree',
                                                                    data: imageCategory.value,
                                                                    edit: ['add', 'update', 'del'],
                                                                    operate: function (obj) {
                                                                        var type = obj.type; //得到操作类型：add、edit、del
                                                                        var data = obj.data; //得到当前节点的数据
                                                                        var id = data.id; //得到节点索引
                                                                        if (type === 'add') { //增加节点
                                                                            addCategoryParentId.value = data.id;
                                                                            addCategoryTitle.value = '未命名';
                                                                            addImageCategory();
                                                                            return
                                                                        }
                                                                        if (type === 'update') { //修改节点
                                                                            updateCategoryId.value = data.id;
                                                                            updateCategoryTitle.value = data.title;
                                                                            editImageCategory();
                                                                            return;
                                                                        }
                                                                        if (type === 'del') { //删除节点
                                                                            updateCategoryId.value = data.id;
                                                                            delImageCategory();
                                                                            return;
                                                                        }
                                                                    },
                                                                    click: function (obj) {
                                                                        imageCategoryId.value = obj.data.id;
                                                                        getAllImage();
                                                                    }

                                                                })
                                                            }
                                                        })
                                                    }
                                                    // -------------删除图片分类-------------------------
                                                    const delImageCategory = () => {
                                                        $.post('/admin/image/delImageCategory',{
                                                            id: updateCategoryId.value
                                                        },function (res) {
                                                            layer.msg(res.msg)
                                                            getAllImageCategory()
                                                            getAllImage()
                                                        })
                                                    }
                                                    // -------------上传图片事件触发-------------------------
                                                    const triggerFileUpload = () => {
                                                        fileInput.value.click();
                                                    }
                                                    // ------------上传图片-------------------------
                                                    const handleFileUpload = (event) => {
                                                        var files = event.target.files;
                                                        if (files) {
                                                            var formData = new FormData();
                                                            for (let i = 0; i < files.length; i++) {
                                                                formData.append('files[]', files[i]);
                                                            }
                                                            formData.set('category_id', imageCategoryId.value)
                                                            $.ajax({
                                                                url: '/admin/image/upload',
                                                                type: 'POST',
                                                                data: formData,
                                                                contentType: false,
                                                                processData: false,
                                                                headers: {
                                                                    'X-CSRF-TOKEN':"{!! csrf_token() !!}"
                                                                },
                                                                success: function (res) {
                                                                    getAllImage();
                                                                    layer.msg(res.msg)
                                                                },
                                                                error: function (res) {
                                                                    console.log(res)
                                                                }
                                                            })
                                                        }
                                                    };
                                                    // -------------获取所有图片-------------------------
                                                    const getAllImage = () => {
                                                        $.get('/admin/image', {
                                                            category_id: imageCategoryId.value,
                                                        }, function (res) {
                                                            if (res.code == 0) {
                                                                layer.msg(res.msg, {icon: 2})
                                                                return
                                                            }
                                                            imageList.value = res.data.data
                                                            total.value = res.data.total
                                                            renderPagination()
                                                        })
                                                    }
                                                    const defaultGetAllImage = () => {
                                                        imageCategoryId.value = -1;
                                                        getAllImage();
                                                    }
                                                    // -------------删除图片--------------------
                                                    const delImage = () => {
                                                        if (clickSelectImages.value.length == 0)  {
                                                            layer.msg('请选择要删除的图片', {icon: 0});
                                                            return
                                                        }
                                                        layer.confirm('确定要删除吗？删除前请确认是否有其他位置使用该图片', {}, function (index) {
                                                            $.post('/admin/image/delete', {
                                                                ids: clickSelectImages.value.map(item => item.id).join(','),
                                                            }, function (res) {
                                                                layer.msg(res.msg, {icon: 1})
                                                                getAllImage();
                                                            })
                                                        })
                                                    }
                                                    onMounted(() => {
                                                        clickSelectImages.value = [];
                                                        getAllImageCategory();
                                                        getAllImage();
                                                    })
                                                    return {
                                                        imageCategory,
                                                        fileInput,
                                                        imageList,
                                                        delImage,
                                                        handleMouseOver,
                                                        handleImageClick,
                                                        defaultGetAllImage,
                                                        buttonAddImageCategory,
                                                        triggerFileUpload,
                                                        handleFileUpload,
                                                    };
                                                }
                                            });

                                            tempApp.mount(layero[0]);
                                        },
                                        yes: function (index, layero) {
                                            if (clickSelectImages.value.length > 0) {
                                                clickSelectImages.value.forEach((item) => {
                                                    insertFn(item.url);
                                                })
                                                layer.close(index);
                                            } else {
                                                layer.msg('请先选择图片', {icon: 0});
                                            }
                                        }
                                    })
                                }
                            }
                        },
                        // 监听富文本框内容变化
                        onChange: function (value) {
                            $('#textarea-{{$id}}').val(editor.getHtml())
                        },
                        // 初始化富文本框内容
                        onCreated: function (editor) {
                            editor.setHtml('{!! $content !!}')
                            $('#textarea-{{$id}}').val('{!! $content !!}')
                        }
                    }
                    var editor = createEditor({
                        selector: '#editor-container-{{$id}}',
                        config: editorConfig,
                        mode: 'default',
                    })
                    var toolbar = createToolbar({
                        editor,
                        selector: '#toolbar-container-{{$id}}',
                        config: {
                            excludeKeys: ['group-video'],
                        },
                        mode: 'default',
                    })
                }
                onMounted(() => {
                    init()
                })
                return {
                    total,
                    imageCategory,
                    fileInput,
                }
            }
        }).mount('#editor—wrapper-{{ $id}}')
    })


</script>
