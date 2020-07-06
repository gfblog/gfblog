define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'blog/comment/index',
                    add_url: 'blog/comment/add',
                    edit_url: 'blog/comment/edit',
                    del_url: 'blog/comment/del',
                    multi_url: 'blog/comment/multi',
                    table: 'blog_comment',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'post.title', title: __('Post'), formatter: Table.api.formatter.search},
                        {field: 'username', title: __('Username'), formatter: Table.api.formatter.search},
                        {field: 'email', title: __('Email'), formatter: Table.api.formatter.search},
                        {field: 'website', title: __('Website'), formatter: Table.api.formatter.url},
                        {field: 'comments', title: __('Comments')},
                        {field: 'ip', title: __('Ip'), formatter: Table.api.formatter.search},
                        {field: 'subscribe', title: __('Subscribe')},
                        {field: 'createtime', title: __('Createtime'), formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange'},
                        {field: 'updatetime', title: __('Updatetime'), visible: false, formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange'},
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
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});