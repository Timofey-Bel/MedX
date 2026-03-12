<script>
{{-- ============================================ --}}
{{-- Banner Management - Управление баннерами --}}
{{-- ============================================ --}}

/**
 * Показать превью баннера в модальном окне
 * Создает временное изображение для получения размеров
 * и отображает его в окне с сохранением пропорций
 * 
 * @param {string} imageUrl - URL изображения баннера
 */
function showBannerPreview(imageUrl) {
    // Создаем временное изображение для получения размеров
    var img = new Image();
    img.onload = function() {
        var imgWidth = this.width;
        var imgHeight = this.height;
        
        // Максимальные размеры окна (80% от экрана)
        var maxWidth = window.innerWidth * 0.8;
        var maxHeight = window.innerHeight * 0.8;
        
        // Вычисляем размеры с сохранением пропорций
        var ratio = Math.min(maxWidth / imgWidth, maxHeight / imgHeight, 1);
        var winWidth = Math.floor(imgWidth * ratio);
        var winHeight = Math.floor(imgHeight * ratio);
        
        // Закрываем предыдущее окно, если оно открыто
        if (bannerPreviewWindow) {
            bannerPreviewWindow.close();
        }
        
        // Создаем новое модальное окно
        bannerPreviewWindow = Ext.create('Ext.window.Window', {
            title: 'Просмотр баннера',
            width: winWidth,
            height: winHeight,
            modal: true,
            layout: 'fit',
            closeAction: 'destroy',
            resizable: true,
            maximizable: true,
            html: '<div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; background:#000;">' +
                  '<img src="' + imageUrl + '" style="max-width:100%; max-height:100%; object-fit:contain;" />' +
                  '</div>',
            listeners: {
                destroy: function() {
                    bannerPreviewWindow = null;
                }
            }
        });
        
        bannerPreviewWindow.show();
    };
    img.src = imageUrl;
}

/**
 * Форма добавления/редактирования баннера
 * 
 * @param {Object|null} record - Запись баннера для редактирования (null для создания нового)
 * @param {Ext.data.Store} store - Store для обновления после сохранения
 */
function showBannerForm(record, store) {
    var isEdit = record !== null;
    var formWindow = Ext.create('Ext.window.Window', {
        title: isEdit ? 'Редактировать баннер' : 'Добавить баннер',
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
                } : null,
                isEdit ? {
                    xtype: 'hiddenfield',
                    name: 'url',
                    value: record.get('url')
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
                        // Редактирование - просто обновляем данные
                        Ext.Ajax.request({
                            url: '/admin/banners/?action=update',
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
                        // Добавление - сначала загружаем файл, потом создаем запись
                        formWindow.setLoading('Загрузка файла...');
                        form.submit({
                            url: '/admin/banners/?action=upload',
                            success: function(form, action) {
                                var uploadResult = action.result;
                                if (uploadResult.success) {
                                    // Файл загружен, создаем запись
                                    var values = form.getValues();
                                    Ext.Ajax.request({
                                        url: '/admin/banners/?action=create',
                                        params: {
                                            url: uploadResult.url,
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
 * Удаление баннера
 * 
 * @param {Object} record - Запись баннера для удаления
 * @param {Ext.data.Store} store - Store для обновления после удаления
 */
function deleteBanner(record, store) {
    Ext.Msg.confirm('Удаление', 'Вы уверены, что хотите удалить баннер "' + record.get('name') + '"?', function(btn) {
        if (btn === 'yes') {
            Ext.Ajax.request({
                url: '/admin/banners/?action=delete',
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
 * Открытие окна управления баннерами
 * Создает окно с grid для управления баннерами (добавление, редактирование, удаление, сортировка)
 */
function openBanners() {
    closeStartMenu();
    
    if (openWindows['banners']) {
        openWindows['banners'].show();
        openWindows['banners'].toFront();
        return;
    }
    
    Ext.onReady(function() {
        
        var store = Ext.create('Ext.data.Store', {
            fields: ['id', 'url', 'name', 'title', 'sort'],
            proxy: {
                type: 'ajax',
                url: '/admin/banners/?action=list',
                reader: {
                    type: 'json',
                    root: 'data',
                    successProperty: 'success',
                    totalProperty: 'total'
                },
                listeners: {
                    exception: function(proxy, response, operation) {
                        // Ошибка загрузки данных
                    }
                }
            },
            autoLoad: true,
            listeners: {
                load: function(store, records, successful, operation) {
                    // Данные загружены
                }
            }
        });
        
        var win = Ext.create('Ext.window.Window', {
            title: 'Управление баннерами',
            width: 480,
            height: 750,
            layout: 'fit',
            maximizable: true,
            minimizable: true,
            constrain: true,
            x: 100,
            y: 50,
            listeners: {
                close: function() {
                    delete openWindows['banners'];
                    removeTaskbarButton('banners');
                },
                activate: function() {
                    setActiveWindow('banners');
                },
                minimize: function() {
                    this.hide();
                    var btn = document.getElementById('taskbar-banners');
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
                            // Собираем новый порядок ID
                            var order = [];
                            store.each(function(record) {
                                order.push(record.get('id'));
                            });
                            
                            
                            // Отправляем на сервер
                            Ext.Ajax.request({
                                url: '/admin/banners/?action=updateSort',
                                params: {
                                    order: JSON.stringify(order)
                                },
                                success: function(response) {
                                    var result = Ext.decode(response.responseText);
                                    if (!result.success) {
                                        Ext.Msg.alert('Ошибка', result.message);
                                        store.load(); // Откатываем изменения
                                    } else {
                                        // Успешное сохранение - обновляем данные с сервера
                                        store.load();
                                    }
                                },
                                failure: function() {
                                    Ext.Msg.alert('Ошибка', 'Не удалось сохранить порядок');
                                    store.load(); // Откатываем изменения
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
                            // Проверяем, локальный ли путь или внешний
                            var imgSrc = value.startsWith('http') ? value : value;
                            return '<img src="' + imgSrc + '" width="130" height="60" style="object-fit: cover; border-radius: 4px; cursor: pointer;" onclick="showBannerPreview(\'' + imgSrc + '\');" />';
                        }
                    },
                    { text: 'Название', dataIndex: 'name', flex: 1 },
                    { text: 'Заголовок', dataIndex: 'title', flex: 1 }
                ],
                listeners: {
                    itemdblclick: function(grid, record) {
                        showBannerForm(record, store);
                    }
                },
                tbar: [
                    {
                        text: 'Добавить',
                        handler: function() {
                            showBannerForm(null, store);
                        }
                    },
                    {
                        text: 'Редактировать',
                        handler: function() {
                            var selection = this.up('grid').getSelectionModel().getSelection();
                            if (selection.length === 0) {
                                Ext.Msg.alert('Ошибка', 'Выберите баннер для редактирования');
                                return;
                            }
                            showBannerForm(selection[0], store);
                        }
                    },
                    {
                        text: 'Удалить',
                        handler: function() {
                            var selection = this.up('grid').getSelectionModel().getSelection();
                            if (selection.length === 0) {
                                Ext.Msg.alert('Ошибка', 'Выберите баннер для удаления');
                                return;
                            }
                            deleteBanner(selection[0], store);
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
        
        openWindows['banners'] = win;
        addTaskbarButton('banners', 'Управление баннерами', win);
        win.show();
    });
}
</script>