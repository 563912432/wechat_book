define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'agent_book/error/index' + location.search,
                    add_url: 'agent_book/error/add',
                    edit_url: 'agent_book/error/edit',
                    del_url: 'agent_book/error/del',
                    multi_url: 'agent_book/error/multi',
                    table: 'agent_book_error',
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
                        // {field: 'agent_id', title: __('Agent_id')},
                        // {field: 'book_id', title: __('Book_id')},
                        // {field: 'chapter_id', title: __('Chapter_id')},
                        {field: 'agentbook.name', title: __('Agentbook.name')},
                        {field: 'agentbookchapter.title', title: __('Agentbookchapter.title')},
                        {field: 'thumb', title: __('Thumb'), formatter: Table.api.formatter.image},
                        {field: 'info', title: __('Info')},
                        {field: 'status', title: __('Status')},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        // {field: 'agentbook.id', title: __('Agentbook.id')},
                        // {field: 'agentbook.agent_id', title: __('Agentbook.agent_id')},
                        // {field: 'agentbook.thumb', title: __('Agentbook.thumb')},
                        // {field: 'agentbook.brief', title: __('Agentbook.brief')},
                        // {field: 'agentbook.createtime', title: __('Agentbook.createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        // {field: 'agentbook.updatetime', title: __('Agentbook.updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        // {field: 'agentbook.deletetime', title: __('Agentbook.deletetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        // {field: 'agentbookchapter.id', title: __('Agentbookchapter.id')},
                        // {field: 'agentbookchapter.book_id', title: __('Agentbookchapter.book_id')},
                        // {field: 'agentbookchapter.pid', title: __('Agentbookchapter.pid')},
                        // {field: 'agentbookchapter.weigh', title: __('Agentbookchapter.weigh')},
                        // {field: 'agentbookchapter.remark', title: __('Agentbookchapter.remark')},
                        // {field: 'agentbookchapter.createtime', title: __('Agentbookchapter.createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        // {field: 'agentbookchapter.updatetime', title: __('Agentbookchapter.updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        // {field: 'agentbookchapter.deletetime', title: __('Agentbookchapter.deletetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

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
                url: 'agent_book/error/recyclebin' + location.search,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
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
                                    url: 'agent_book/error/restore',
                                    refresh: true
                                },
                                {
                                    name: 'Destroy',
                                    text: __('Destroy'),
                                    classname: 'btn btn-xs btn-danger btn-ajax btn-destroyit',
                                    icon: 'fa fa-times',
                                    url: 'agent_book/error/destroy',
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
            }
        }
    };
    return Controller;
});
