define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'blog/category/index',
                    add_url: 'blog/category/add',
                    edit_url: 'blog/category/edit',
                    del_url: 'blog/category/del',
                    multi_url: 'blog/category/multi',
                    table: 'blog_category',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'weigh',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'pid', title: __('Pid'), formatter: Table.api.formatter.search, visible: false},
                        {field: 'name', title: __('Name')},
                        {field: 'nickname', title: __('Nickname')},
                        {
                            field: 'link', title: __('Url'), formatter: function (value, row, index) {
                                return '<div class=""><a href="' + row.url + '" class="btn btn-xs btn-default" target="_blank"><i class="fa fa-link"></i></a></div>';
                            }
                        },
                        {field: 'flag', title: __('Flag'), formatter: Table.api.formatter.flag},
                        {field: 'image', title: __('Image'), events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'keywords', title: __('Keywords')},
                        {field: 'description', title: __('Description')},
                        {field: 'diyname', title: __('Diyname')},
                        {field: 'createtime', title: __('Createtime'), formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange'},
                        {field: 'updatetime', title: __('Updatetime'), visible: false, formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange'},
                        {field: 'weigh', title: __('Weigh')},
                        {field: 'status', title: __('Status'), searchList: {"normal":__('Normal'),"hidden":__('Hidden')}, formatter: Table.api.formatter.status},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {

                $.validator.config({
                    rules: {
                        diyname: function (element) {
                            if (element.value.toString().match(/^\d+$/)) {
                                return __('Can not be digital');
                            }
                            return $.ajax({
                                url: 'blog/category/check_element_available',
                                type: 'POST',
                                data: {id: $("#category-id").val(), name: element.name, value: element.value},
                                dataType: 'json'
                            });
                        }
                    }
                });
                //获取栏目拼音
                var si;
                $(document).on("keyup", "#c-name", function () {
                    var value = $(this).val();
                    if (value != '' && !value.match(/\n/)) {
                        clearTimeout(si);
                        si = setTimeout(function () {
                            Fast.api.ajax({
                                loading: false,
                                url: "blog/ajax/get_title_pinyin",
                                data: {title: value}
                            }, function (data, ret) {
                                $("#c-diyname").val(data.pinyin);
                                return false;
                            }, function (data, ret) {
                                return false;
                            });
                        }, 200);
                    }
                });
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});
