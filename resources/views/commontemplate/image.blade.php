<style>
    .layui-inline-image {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .image-add {
        width: 100px;
        height: 100px;
        border: 1px dashed #ccc;
        /*    内容居中*/
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: #fff;
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

    .image-add:hover {
        border: 1px dashed #009688;
        cursor: pointer;
    }

    .select-image {
        position: relative;
        width: 100px;
        height: 100px;
        background-size: 70%;
        background-position: center;
        background-repeat: no-repeat;
        border: 1px dashed #ccc;
        margin-right: 2px;
        background-color: #fff;
    }

    .select-image:hover {
        border: 1px dashed #009688;
        cursor: pointer;
    }

    .select-image .close-btn {
        position: absolute;
        top: 0px;
        right: 0px;
        background: rgba(0, 0, 0, 0.5);
        color: white;
        padding: 2px 5px;
        border-radius: 50%;
        font-size: 14px;
        display: none;
    }

    .select-image:hover .close-btn {
        display: block;
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
<div class="layui-inline-image" id="{{$id}}">
    <div class="select-image" :style="{ backgroundImage: `url(${item.url})` }"
         v-for="item in selectImages"
         :key="item.id">
        <span class="close-btn" @click.stop="removeSelectImage(item)">×</span>
    </div>
    <div class="image-add" id="image-add-{{$id}}" @click="uploadImage">
        <i class="layui-icon layui-icon-upload" style="font-size: 50px;color:#666"></i>
    </div>
    <input type="hidden" name="{{$name}}" :value="selectImages.map(item => item.url).join(',')">
</div>
<script type="text/html" id="image-template-{{$id}}">
    <div class="layui-container image-max">
        <div class="layui-row image-max">
            <!-- 左边部分 -->
            <div class="layui-col-lg2" style="height: 99%;border-right: 1px dashed #ccc;overflow: auto;">
                <div class="layui-row">
                    <div class="public-margin layui-col-12" style="padding-top: 30px">
                        <div data-id="0" class="layui-tree-set layui-tree-setHide">
                            <div class="layui-tree-entry">
                                <div class="layui-tree-main"><span class="layui-tree-iconClick"><i
                                            class="layui-icon layui-icon-file"></i></span><span class="layui-tree-txt"
                                                                                                @click="defaultGetAllImage">默认分类</span>
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
                        <button class="layui-btn layui-btn-sm  layui-btn-danger"><i
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
    document.addEventListener('DOMContentLoaded', function () {
        const {createApp, ref, onMounted, watch} = Vue;
        const {table, form, laydate, tree, laypage} = layui;

        createApp({
            setup() {
                const num = ref({!! $num !!});
                const total = ref(0);
                const imageCategory = ref([]);
                const imageList = ref([]);
                const imageCategoryId = ref(-1);
                const addCategoryTitle = ref('未命名');
                const addCategoryParentId = ref(0);
                const updateCategoryId = ref(0);
                const updateCategoryTitle = ref('');
                const fileInput = ref(null);
                const selectImages = ref({!! $value !!});
                const clickSelectImages = ref([]);
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
                const uploadImage = () => {
                    layer.open({
                        type: 1,
                        area: ["80%", "80%"],
                        shadeClose: true,
                        shade: 0.8,
                        title: '上传图片',
                        content: $('#image-template-{{$id}}').html(),
                        btn: ['使用选中图片', '取消'],
                        success: function (layero, index) {
                            const tempApp = createApp({
                                setup() {

                                    const handleMouseOver = (event, name) => {
                                        const target = event.currentTarget;
                                        layer.tips(name, target, {time: 1000});

                                    }
                                    const init = () => {

                                    }
                                    const handleImageClick = (event, item) => {
                                        const target = event.currentTarget;
                                        target.classList.toggle('selected'); // 切换选中状态
                                        if (target.classList.contains('selected')) {
                                            clickSelectImages.value.push(item);
                                            console.log(clickSelectImages.value)
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
                                            parent_id: addCategoryParentId.value,
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
                                    // -------------上传图片-------------------------
                                    const triggerFileUpload = () => {
                                        fileInput.value.click();
                                    }
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
                                                headers:{
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
                                            console.log(res)
                                            imageList.value = res.data.data
                                            total.value = res.data.total
                                            renderPagination()
                                        })
                                    }
                                    const defaultGetAllImage = () => {
                                        imageCategoryId.value = -1;
                                        getAllImage();
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
                                        selectImages,
                                        removeSelectImage,
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
                                if (clickSelectImages.value.length > num.value || selectImages.value.length >= num.value) {
                                    layer.msg('最多选择' + num.value + '张图片', {icon: 0});
                                    return;
                                }

                                if (num.value === 1) {
                                    selectImages.value = [...clickSelectImages.value];
                                    layer.close(index);
                                    return;
                                }
                                selectImages.value = [...clickSelectImages.value, ...selectImages.value];
                                layer.close(index);
                            } else {
                                layer.msg('请先选择图片', {icon: 0});
                            }
                        }
                    })
                }
                const removeSelectImage = (item) => {
                    const index = selectImages.value.findIndex(img => img.id === item.id);
                    if (index !== -1) {
                        selectImages.value.splice(index, 1);
                    }
                }
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
                };
                onMounted(() => {
                })
                return {
                    removeSelectImage,
                    uploadImage,
                    total,
                    imageCategory,
                    fileInput,
                    selectImages
                }
            }
        }).mount('#{{$id}}');
    })
</script>
