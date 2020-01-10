define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'book_tpl/index' + location.search,
                    add_url: 'book_tpl/add',
                    edit_url: 'book_tpl/edit',
                    del_url: 'book_tpl/del',
                    multi_url: 'book_tpl/multi',
                    table: 'book_tpl',
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
                        {field: 'name', title: __('Name')},
                        {field: 'no', title: __('No')},
                        {
                            field: 'thumb',
                            title: __('Thumb'),
                            // formatter: Table.api.formatter.images
                            formatter: Controller.api.thumb
                        },
                        // {field: 'remark', title: __('Remark')},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });
            $(document.body).on('click', '.imageLarge', function () {
                var thumbs = $(this).data('image')
                var images = thumbs.split(',')
                var html = ""
                $.each(images, function (i, v) {
                    html += "<img src='" +v+"' style='margin-right: 5px;margin-bottom: 10px'/>";
                })
                layer.open({
                    content: html,
                    area: ["80%", "85%"]
                });
            })
            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        recyclebin: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    'dragsort_url': ''
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: 'book_tpl/recyclebin' + location.search,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'name', title: __('Name'), align: 'left'},
                        {
                            field: 'deletetime',
                            title: __('Deletetime'),
                            operate: 'RANGE',
                            addclass: 'datetimerange',
                            formatter: Table.api.formatter.datetime
                        },
                        {
                            field: 'operate',
                            width: '130px',
                            title: __('Operate'),
                            table: table,
                            events: Table.api.events.operate,
                            buttons: [
                                {
                                    name: 'Restore',
                                    text: __('Restore'),
                                    classname: 'btn btn-xs btn-info btn-ajax btn-restoreit',
                                    icon: 'fa fa-rotate-left',
                                    url: 'book_tpl/restore',
                                    refresh: true
                                },
                                {
                                    name: 'Destroy',
                                    text: __('Destroy'),
                                    classname: 'btn btn-xs btn-danger btn-ajax btn-destroyit',
                                    icon: 'fa fa-times',
                                    url: 'book_tpl/destroy',
                                    refresh: true
                                }
                            ],
                            formatter: Table.api.formatter.operate
                        }
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
            },
            thumb: function (value, row, index) {
                var thumbs = row.thumb.split(',')
                if (thumbs.length > 0) {
                    var html = "<a class='imageLarge' data-image='"+row.thumb+"' href='javascript:;'>"
                    $.each(thumbs, function (i, v) {
                        html += "<img src='" +v+"' style='height: 30px;width: 30px;margin-right: 5px'/>"
                    })
                    html += '</a>'
                    return  html
                } else {
                    return ''
                }
            }
        }
    };
    return Controller;
});
