<script>
{{-- ============================================ --}}
{{-- Wholesaler Banner Management - Управление баннерами для оптовиков --}}
{{-- ============================================ --}}

{{-- ========================================================================== --}}
{{--                            БАННЕРЫ ОПТОВИКОВ                               --}}
{{-- ========================================================================== --}}

/**
 * Форма добавления/редактирования баннера (ОПТ)
 * 
 * @param {Object|null} record - Запись баннера для редактирования (null для создания нового)
 * @param {Ext.data.Store} store - Store для обновления после сохранения
 */
function showWholesalerBannerForm(record, store) {
    var isEdit = record !== null;
    var formWindow = Ext.create('Ext.window.Window', {
        title: isEdit ? 'Редактировать оптовый баннер' : 'Добавить оптовый баннер',
        width: 500,
        modal: true,
        layout: 'fit',
        items: [{
            xtype: 'form',
            padding: 10,
            defaults: {
                anchor: '100%',
                labelWidth: 120
            },
            items: [
                {
                    xtype: 'filefield',
                    name: 'file',
                    fieldLabel: 'Изображение',
                    buttonText: 'Выбрать...',
                    hidden: isEdit,
                    allowBlank: isEdit,
                    listeners: {
                        change: function(field, value) {
                            var form = field.up('form').getForm();
                            if (value && !form.findField('name').getValue()) {
                                var filename = value.replace(/^.*[\\\/]/, '').replace(/\.[^/.]+$/, "");
                                form.findField('name').setValue(filename);
                            }
                        }
                    }
                },
                {
                    xtype: 'textfield',
                    name: 'name',
                    fieldLabel: 'Название',
                    allowBlank: false,
                    value: isEdit ? record.get('name') : ''
                },
                {
                    xtype: 'textfield',
                    name: 'title',
                    fieldLabel: 'Заголовок',
                    value: isEdit ? record.get('title') : ''
                },
                {
                    xtype: 'textfield',
                    name: 'url',
                    fieldLabel: 'Ссылка (URL)', // Добавил, т.к. в баннерах часто нужна ссылка при клике
                    value: isEdit ? record.get('url') : '',
                    // Если url используется только для картинки, то скройте это поле или переименуйте
                    // В вашем примере url это путь к картинке, поэтому скрываем для create
                    hidden: true
                },
                {
                    xtype: 'numberfield',
                    name: 'sort',
                    fieldLabel: 'Порядок',
                    value: isEdit ? record.get('sort') : 10,
                    minValue: 0,
                    step: 10
                },
                isEdit ? {
                    xtype: 'hiddenfield',
                    name: 'id',
                    value: record.get('id')
                } : null
            ].filter(Boolean)
        }],
        buttons: [
            {
                text: 'Сохранить',
                handler: function() {
                    var form = this.up('window').down('form').getForm();
                    if (!form.isValid()) {
                        Ext.Msg.alert('Ошибка', 'Заполните все обязательные поля');
                        return;
                    }

                    if (isEdit) {
                        // Редактирование
                        Ext.Ajax.request({
                            url: '/admin/wholesaler_banners/?action=update',
                            params: form.getValues(),
                            success: function(response) {
                                var result = Ext.decode(response.responseText);
                                if (result.success) {
                                    Ext.Msg.alert('Успешно', result.message);
                                    formWindow.close();
                                    store.load();
                                } else {
                                    Ext.Msg.alert('Ошибка', result.message);
                                }
                            },
                            failure: function() {
                                Ext.Msg.alert('Ошибка', 'Ошибка обновления');
                            }
                        });
                    } else {
                        // Добавление
                        formWindow.setLoading('Загрузка файла...');
                        form.submit({
                            url: '/admin/wholesaler_banners/?action=upload',
                            success: function(form, action) {
                                var uploadResult = action.result;
                                if (uploadResult.success) {
                                    var values = form.getValues();
                                    Ext.Ajax.request({
                                        url: '/admin/wholesaler_banners/?action=create',
                                        params: {
                                            url: uploadResult.url, // Путь к картинке
                                            name: values.name,
                                            title: values.title,
                                            sort: values.sort
                                        },
                                        success: function(response) {
                                            formWindow.setLoading(false);
                                            var result = Ext.decode(response.responseText);
                                            if (result.success) {
                                                Ext.Msg.alert('Успешно', 'Баннер добавлен');
                                                formWindow.close();
                                                store.load();
                                            } else {
                                                Ext.Msg.alert('Ошибка', result.message);
                                            }
                                        },
                                        failure: function() {
                                            formWindow.setLoading(false);
                                            Ext.Msg.alert('Ошибка', 'Ошибка создания записи');
                                        }
                                    });
                                } else {
                                    formWindow.setLoading(false);
                                    Ext.Msg.alert('Ошибка', uploadResult.message);
                                }
                            },
                            failure: function(form, action) {
                                formWindow.setLoading(false);
                                Ext.Msg.alert('Ошибка', action.result ? action.result.message : 'Ошибка загрузки файла');
                            }
                        });
                    }
                }
            },
            {
                text: 'Отмена',
                handler: function() {
                    this.up('window').close();
                }
            }
        ]
    });

    formWindow.show();
}

/**
 * Удаление баннера (ОПТ)
 * 
 * @param {Object} record - Запись баннера для удаления
 * @param {Ext.data.Store} store - Store для обновления после удаления
 */
function deleteWholesalerBanner(record, store) {
    Ext.Msg.confirm('Удаление', 'Вы уверены, что хотите удалить баннер "' + record.get('name') + '"?', function(btn) {
        if (btn === 'yes') {
            Ext.Ajax.request({
                url: '/admin/wholesaler_banners/?action=delete',
                params: {
                    id: record.get('id')
                },
                success: function(response) {
                    var result = Ext.decode(response.responseText);
                    if (result.success) {
                        Ext.Msg.alert('Успешно', result.message);
                        store.load();
                    } else {
                        Ext.Msg.alert('Ошибка', result.message);
                    }
                },
                failure: function() {
                    Ext.Msg.alert('Ошибка', 'Ошибка удаления баннера');
                }
            });
        }
    });
}

/**
 * Открытие окна баннеров (ОПТ)
 * Создает окно с grid для управления баннерами оптовиков
 */
function openWholesalerBanners() {
    closeStartMenu();

    if (openWindows['wholesaler_banners']) {
        openWindows['wholesaler_banners'].show();
        openWindows['wholesaler_banners'].toFront();
        return;
    }

    Ext.onReady(function() {

        var store = Ext.create('Ext.data.Store', {
            fields: ['id', 'url', 'name', 'title', 'sort'],
            proxy: {
                type: 'ajax',
                url: '/admin/wholesaler_banners/?action=list',
                reader: {
                    type: 'json',
                    root: 'data',
                    successProperty: 'success',
                    totalProperty: 'total'
                }
            },
            autoLoad: true
        });

        var win = Ext.create('Ext.window.Window', {
            title: 'Баннеры для оптовиков',
            width: 480,
            height: 750,
            layout: 'fit',
            maximizable: true,
            minimizable: true,
            constrain: true,
            x: 120, // Немного сместим, чтобы не перекрывало обычные баннеры
            y: 70,
            listeners: {
                close: function() {
                    delete openWindows['wholesaler_banners'];
                    removeTaskbarButton('wholesaler_banners');
                },
                activate: function() {
                    setActiveWindow('wholesaler_banners');
                },
                minimize: function() {
                    this.hide();
                    var btn = document.getElementById('taskbar-wholesaler_banners');
                    if (btn) {
                        btn.classList.add('minimized');
                        btn.classList.remove('active');
                    }
                }
            },
            items: [{
                xtype: 'grid',
                store: store,
                selModel: {
                    selType: 'rowmodel',
                    mode: 'SINGLE'
                },
                viewConfig: {
                    plugins: {
                        ptype: 'gridviewdragdrop',
                        dragText: 'Перетащите для изменения порядка'
                    },
                    listeners: {
                        drop: function(node, data, overModel, dropPosition) {
                            var order = [];
                            store.each(function(record) {
                                order.push(record.get('id'));
                            });

                            Ext.Ajax.request({
                                url: '/admin/wholesaler_banners/?action=updateSort',
                                params: {
                                    order: JSON.stringify(order)
                                },
                                success: function(response) {
                                    store.load();
                                }
                            });
                        }
                    }
                },
                columns: [
                    { text: 'ID', dataIndex: 'id', width: 50 },
                    {
                        text: 'Порядок',
                        dataIndex: 'sort',
                        width: 80,
                        align: 'center'
                    },
                    {
                        text: 'Превью',
                        dataIndex: 'url',
                        width: 150,
                        renderer: function(value) {
                            var imgSrc = value.startsWith('http') ? value : value;
                            return '<img src="' + imgSrc + '" width="130" height="60" style="object-fit: cover; border-radius: 4px;" />';
                        }
                    },
                    { text: 'Название', dataIndex: 'name', flex: 1 },
                    { text: 'Заголовок', dataIndex: 'title', flex: 1 }
                ],
                listeners: {
                    itemdblclick: function(grid, record) {
                        showWholesalerBannerForm(record, store);
                    }
                },
                tbar: [
                    {
                        text: 'Добавить',
                        handler: function() {
                            showWholesalerBannerForm(null, store);
                        }
                    },
                    {
                        text: 'Редактировать',
                        handler: function() {
                            var selection = this.up('grid').getSelectionModel().getSelection();
                            if (selection.length === 0) {
                                Ext.Msg.alert('Ошибка', 'Выберите баннер');
                                return;
                            }
                            showWholesalerBannerForm(selection[0], store);
                        }
                    },
                    {
                        text: 'Удалить',
                        handler: function() {
                            var selection = this.up('grid').getSelectionModel().getSelection();
                            if (selection.length === 0) {
                                Ext.Msg.alert('Ошибка', 'Выберите баннер');
                                return;
                            }
                            deleteWholesalerBanner(selection[0], store);
                        }
                    },
                    '->',
                    {
                        text: 'Обновить',
                        handler: function() {
                            store.load();
                        }
                    }
                ]
            }]
        });

        openWindows['wholesaler_banners'] = win;
        addTaskbarButton('wholesaler_banners', 'Баннеры (Опт)', win);
        win.show();
    });
}
</script>