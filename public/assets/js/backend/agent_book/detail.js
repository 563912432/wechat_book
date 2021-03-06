define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'agent_book/detail/index' + location.search,
                    add_url: 'agent_book/detail/add',
                    edit_url: 'agent_book/detail/edit',
                    del_url: 'agent_book/detail/del',
                    multi_url: 'agent_book/detail/multi',
                    table: 'agent_book_detail',
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
                        {field: 'title', title: __('Title')},
                        {field: 'thumb', title: __('Thumb'), formatter:Table.api.formatter.image},
                        {field: 'weigh', title: __('Weigh')},
                        {field: 'agentbook.name', title: __('Agentbook.name')},
                        {field: 'agentbookchapter.title', title: __('Agentbookchapter.title')},
                        {field: 'url', title: __('Url')},
                        {
                            field: 'qrcode',
                            title: __('UrlImg'),
                            formatter: Controller.api.qrcode
                        },
                        // {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        // {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {
                            field: 'operate',
                            title: __('Operate'),
                            table: table,
                            events: Table.api.events.operate,
                            formatter: Table.api.formatter.operate,
                            buttons: [
                                {
                                    name: 'Qrcode',
                                    text: __('Qrcode'),
                                    classname: 'btn btn-xs btn-primary btn-ajax',
                                    icon: 'fa fa-qrcode',
                                    url: 'agent_book/detail/qrcode',
                                    refresh: true
                                }
                            ]
                        }
                    ]
                ]
            });
            $(document.body).on('click', '.qrcodeBig', function () {
                var img = $(this).data('img');
                layer.open({
                    content: "<div style='text-align: center'><img src='" +img+ "'/></div>",
                    area: ["620px", "650px"]
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
                url: 'agent_book/detail/recyclebin' + location.search,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'title', title: __('Title'), align: 'left'},
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
                                    url: 'agent_book/detail/restore',
                                    refresh: true
                                },
                                {
                                    name: 'Destroy',
                                    text: __('Destroy'),
                                    classname: 'btn btn-xs btn-danger btn-ajax btn-destroyit',
                                    icon: 'fa fa-times',
                                    url: 'agent_book/detail/destroy',
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
        qrcode: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            },
            qrcode: function (value, row, index) {
                if (row.qrcode) {
                    return '<div><a href="javascript:;"><img data-img="' +row.qrcode+ '" class="qrcodeBig" src="'+ row.qrcode + '" style="width: 30px;height: 30px"/></a><a download href="' +row.qrcode+ '" class="btn btn-xs btn-success btn-download"><i class="fa fa-download"></i></a></span></div>';
                } else {
                    return ''
                }
            }
        }
    };
    return Controller;
});
