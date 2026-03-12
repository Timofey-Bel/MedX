/**
 * Section Builder - Конструктор секций (аналог Tilda)
 * Использует GrapesJS для WYSIWYG редактирования секций
 */

/**
 * Форматирование HTML с отступами
 */
function formatHtml(html) {
    if (!html) return '';
    
    var formatted = '';
    var indent = 0;
    var indentStr = '  '; // 2 пробела
    
    // Разбиваем на теги
    html.split(/(<[^>]+>)/g).forEach(function(node) {
        if (!node.trim()) return;
        
        // Закрывающий тег - уменьшаем отступ перед добавлением
        if (node.match(/^<\/\w/)) {
            indent--;
            formatted += '\n' + indentStr.repeat(Math.max(0, indent)) + node;
        }
        // Открывающий тег
        else if (node.match(/^<\w[^>]*[^\/]>$/)) {
            formatted += '\n' + indentStr.repeat(indent) + node;
            indent++;
        }
        // Самозакрывающийся тег или комментарий
        else if (node.match(/^<\w[^>]*\/>$/) || node.match(/^<!--/)) {
            formatted += '\n' + indentStr.repeat(indent) + node;
        }
        // Текстовый узел
        else if (node.trim()) {
            formatted += node;
        }
    });
    
    return formatted.trim();
}

/**
 * Копирование текста в буфер обмена
 */
function copyToClipboard(text) {
    // Современный способ через Clipboard API
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(function() {
            console.log('Текст скопирован через Clipboard API');
        }).catch(function(err) {
            console.error('Ошибка копирования через Clipboard API:', err);
            // Fallback к старому методу
            copyToClipboardFallback(text);
        });
    } else {
        // Fallback для старых браузеров
        copyToClipboardFallback(text);
    }
}
/**
 * Fallback метод копирования для старых браузеров
 */
function copyToClipboardFallback(text) {
    var textArea = document.createElement('textarea');
    textArea.value = text;
    textArea.style.position = 'fixed';
    textArea.style.left = '-9999px';
    textArea.style.top = '-9999px';
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    
    try {
        var successful = document.execCommand('copy');
        if (successful) {
            console.log('Текст скопирован через execCommand');
        } else {
            console.error('Не удалось скопировать текст');
        }
    } catch (err) {
        console.error('Ошибка копирования:', err);
    }
    
    document.body.removeChild(textArea);
}

/**
 * Показать уведомление о копировании
 */
function showCopyNotification(guid) {
    // Создаем временное уведомление
    var notification = document.createElement('div');
    notification.innerHTML = '<strong>✅ Скопировано:</strong> <code style="background: white; padding: 2px 6px; border-radius: 3px;">' + guid + '</code>';
    notification.style.cssText = 
        'position: fixed; ' +
        'top: 20px; ' +
        'right: 20px; ' +
        'background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); ' +
        'color: white; ' +
        'padding: 15px 20px; ' +
        'border-radius: 8px; ' +
        'box-shadow: 0 4px 12px rgba(0,0,0,0.15); ' +
        'z-index: 100000; ' +
        'font-family: Arial, sans-serif; ' +
        'font-size: 14px; ' +
        'transition: all 0.3s; ' +
        'opacity: 0; ' +
        'transform: translateY(-20px);';
    
    document.body.appendChild(notification);
    
    // Анимация появления
    setTimeout(function() {
        notification.style.opacity = '1';
        notification.style.transform = 'translateY(0)';
    }, 10);
    
    // Автоматическое исчезновение через 2 секунды
    setTimeout(function() {
        notification.style.opacity = '0';
        notification.style.transform = 'translateY(-20px)';
        
        // Удаление из DOM через 300ms после начала анимации исчезновения
        setTimeout(function() {
            if (notification.parentNode) {
                document.body.removeChild(notification);
            }
        }, 300);
    }, 2000);
}
function openSectionBuilder() {
    closeStartMenu();
    
    // Проверяем, не открыто ли уже окно через глобальный объект
    if (openWindows['section_builder']) {
        openWindows['section_builder'].show();
        openWindows['section_builder'].toFront();
        return;
    }

    // Создаем окно с редактором
    var editorWindow = Ext.create('Ext.window.Window', {
        id: 'section-builder-window',
        title: '<span class="material-icons" style="vertical-align: middle; margin-right: 8px;">view_module</span>Конструктор секций - Section Builder',
        width: Math.min(1400, Ext.getBody().getViewSize().width - 40),
        height: Math.min(900, Ext.getBody().getViewSize().height - 40),
        modal: false,
        layout: 'border',
        maximizable: true,
        minimizable: true,
        constrain: true,
        renderTo: Ext.getBody(),
        
        items: [{
            // Левая панель - список секций
            region: 'west',
            title: 'Секции',
            width: 250,
            split: true,
            collapsible: true,
            layout: 'fit',
            bodyStyle: 'background: #f5f5f5;',
            items: [{
                xtype: 'grid',
                id: 'sections-grid',
                border: false,
                bodyStyle: 'background: white;',
                store: Ext.create('Ext.data.Store', {
                    fields: [
                        {name: 'id', type: 'int'},
                        {name: 'guid', type: 'string'},
                        {name: 'name', type: 'string'},
                        {name: 'slug', type: 'string'},
                        {name: 'category', type: 'string'},
                        {name: 'active', type: 'int'},
                        {name: 'updated_at', type: 'string'}
                    ],
                    proxy: {
                        type: 'ajax',
                        url: '/admin/section_builder/?action=list',
                        reader: {
                            type: 'json',
                            root: 'sections',  // В ExtJS 4.x используется 'root', а не 'rootProperty'
                            successProperty: 'success'
                        }
                    },
                    idProperty: 'id',
                    autoLoad: true
                }),
                viewConfig: {
                    plugins: {
                        ptype: 'gridviewdragdrop',
                        dragText: 'Перетащите для изменения порядка секций'
                    },
                    listeners: {
                        drop: function(node, data, overModel, dropPosition) {
                            var grid = Ext.getCmp('sections-grid');
                            var store = grid.getStore();
                            
                            // Собираем новый порядок секций
                            var orders = [];
                            var position = 1;
                            
                            store.each(function(record) {
                                orders.push({
                                    id: record.get('id'),
                                    sort: position++
                                });
                            });
                            
                            console.log('Новый порядок секций:', orders);
                            var ordersJson = JSON.stringify(orders);
                            console.log('JSON строка для отправки:', ordersJson);
                            
                            // Получаем CSRF токен
                            var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                            
                            // Отправляем на сервер через jQuery AJAX для совместимости с legacy
                            $.ajax({
                                url: '/admin/section_builder',
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': csrfToken
                                },
                                data: {
                                    action: 'update_sort',
                                    orders: ordersJson
                                },
                                dataType: 'json',
                                success: function(result) {
                                    var timestamp = new Date().toLocaleTimeString();
                                    console.log('[' + timestamp + '] Ответ сервера:', result);
                                    if (result.success) {
                                        console.log('[' + timestamp + '] ✅ Порядок секций обновлен успешно!');
                                    } else {
                                        console.error('[' + timestamp + '] ❌ Ошибка сохранения порядка:', result.error);
                                        Ext.Msg.alert('Ошибка', result.error || 'Не удалось сохранить порядок');
                                        store.load();
                                    }
                                },
                                error: function(xhr, status, error) {
                                    console.error('Ошибка AJAX:', {
                                        status: status,
                                        error: error,
                                        responseText: xhr.responseText,
                                        statusCode: xhr.status
                                    });
                                    Ext.Msg.alert('Ошибка', 'Ошибка сервера при сохранении порядка: ' + xhr.status);
                                    store.load();
                                }
                            });
                        }
                    }
                },
                columns: [{
                    text: 'GUID',
                    dataIndex: 'guid',
                    width: 80,
                    align: 'center',
                    sortable: false,
                    renderer: function(value) {
                        if (!value) return '<span style="color: #999;">—</span>';
                        return '<code style="font-size: 11px; background: #f5f5f5; padding: 2px 6px; border-radius: 3px; font-weight: 600; cursor: pointer; transition: all 0.2s;" title="Кликните для копирования">' + value + '</code>';
                    }
                }, {
                    text: 'Название',
                    dataIndex: 'name',
                    flex: 1,
                    sortable: false,
                    editor: {
                        xtype: 'textfield',
                        allowBlank: false,
                        selectOnFocus: true
                    }
                }],
                plugins: [
                    Ext.create('Ext.grid.plugin.CellEditing', {
                        clicksToEdit: 2,
                        listeners: {
                            edit: function(editor, e) {
                                var sectionId = e.record.get('id');
                                var newName = e.value;
                                var oldName = e.originalValue;
                                
                                if (newName !== oldName) {
                                    Ext.Ajax.request({
                                        url: '/admin/section_builder/?action=update',
                                        method: 'POST',
                                        params: {
                                            id: sectionId,
                                            name: newName
                                        },
                                        success: function(response) {
                                            var data = Ext.decode(response.responseText);
                                            if (data.success) {
                                                console.log('✅ Название секции обновлено:', newName);
                                                if (data.slug) {
                                                    e.record.set('slug', data.slug);
                                                }
                                                e.record.commit();
                                            } else {
                                                Ext.Msg.alert('Ошибка', data.error || 'Не удалось обновить название');
                                                e.record.reject();
                                            }
                                        },
                                        failure: function() {
                                            Ext.Msg.alert('Ошибка', 'Ошибка сервера при обновлении названия');
                                            e.record.reject();
                                        }
                                    });
                                }
                            }
                        }
                    })
                ],
                listeners: {
                    selectionchange: function(model, records) {
                        if (records.length > 0) {
                            var sectionId = records[0].get('id');
                            loadSectionIntoEditor(sectionId);
                        }
                    },
                    cellclick: function(view, td, cellIndex, record, tr, rowIndex, e) {
                        if (cellIndex === 0) {
                            var guid = record.get('guid');
                            if (guid) {
                                copyToClipboard(guid);
                                showCopyNotification(guid);
                            }
                        }
                    },
                    itemcontextmenu: function(view, record, item, index, e) {
                        e.stopEvent(); // Предотвращаем стандартное контекстное меню браузера
                        
                        // Создаем контекстное меню
                        var contextMenu = Ext.create('Ext.menu.Menu', {
                            items: [{
                                text: 'Копировать GUID',
                                iconCls: 'icon-copy',
                                handler: function() {
                                    var guid = record.get('guid');
                                    if (guid) {
                                        copyToClipboard(guid);
                                        showCopyNotification(guid);
                                        console.log('✅ GUID скопирован:', guid);
                                    }
                                }
                            }, {
                                text: 'Дублировать',
                                iconCls: 'icon-duplicate',
                                handler: function() {
                                    duplicateSection(record.get('id'), record.get('name'));
                                }
                            }, '-', {
                                text: 'Удалить',
                                iconCls: 'icon-delete',
                                handler: function() {
                                    Ext.Msg.confirm('Подтверждение', 'Удалить секцию "' + record.get('name') + '"?', function(btn) {
                                        if (btn === 'yes') {
                                            deleteSection(record.get('id'));
                                        }
                                    });
                                }
                            }]
                        });
                        
                        // Показываем меню в позиции курсора
                        contextMenu.showAt(e.getXY());
                    }
                },
                tbar: [{
                    text: 'Новая секция',
                    iconCls: 'icon-add',
                    handler: function() {
                        Ext.Msg.prompt('Создать секцию', 'Введите название:', function(btn, text) {
                            if (btn === 'ok' && text) {
                                createNewSection(text);
                            }
                        });
                    }
                }]
            }]
        }, {
            // Центральная панель - редактор
            region: 'center',
            layout: 'card',
            id: 'editor-card-panel',
            activeItem: 0,
            bodyStyle: 'background: white;',
            tbar: [{
                text: 'Сохранить',
                iconCls: 'icon-save',
                scale: 'medium',
                handler: function() {
                    saveCurrentSection();
                }
            }, '-', {
                text: 'Визуально',
                id: 'visual-mode-btn',
                iconCls: 'icon-design',
                enableToggle: true,
                pressed: true,
                toggleGroup: 'editor-mode',
                handler: function() {
                    switchEditorMode('visual');
                }
            }, {
                text: 'Код',
                id: 'code-mode-btn',
                iconCls: 'icon-code',
                enableToggle: true,
                toggleGroup: 'editor-mode',
                handler: function() {
                    switchEditorMode('code');
                }
            }, '->', {
                xtype: 'tbtext',
                id: 'current-section-name',
                text: 'Выберите секцию для редактирования'
            }],
            items: [{
                // Визуальный редактор (GrapesJS)
                xtype: 'panel',
                id: 'visual-editor-panel',
                border: false,
                bodyStyle: 'overflow: hidden; background: white;',
                html: '<div id="gjs-editor" style="width: 100%; height: 100%;"></div>',
                listeners: {
                    afterrender: function(panel) {
                        setTimeout(function() {
                            try {
                                initGrapesJS();
                            } catch (e) {
                                console.error('Ошибка инициализации GrapesJS:', e);
                                Ext.Msg.alert('Ошибка', 'Не удалось инициализировать редактор: ' + e.message);
                            }
                        }, 500);
                    }
                }
            }, {
                // Редактор кода (HTML, CSS, JS)
                xtype: 'panel',
                id: 'code-editor-panel',
                layout: {
                    type: 'vbox',
                    align: 'stretch'
                },
                bodyStyle: 'padding: 10px; background: #f5f5f5;',
                autoScroll: true,
                items: [{
                    xtype: 'panel',
                    title: 'HTML',
                    flex: 1,
                    layout: 'fit',
                    margin: '0 0 10 0',
                    items: [{
                        xtype: 'textarea',
                        id: 'code-html-editor',
                        style: 'font-family: "Courier New", monospace; font-size: 13px;',
                        emptyText: 'HTML код секции...'
                    }]
                }, {
                    xtype: 'panel',
                    title: 'CSS',
                    flex: 1,
                    layout: 'fit',
                    margin: '0 0 10 0',
                    items: [{
                        xtype: 'textarea',
                        id: 'code-css-editor',
                        style: 'font-family: "Courier New", monospace; font-size: 13px;',
                        emptyText: 'CSS стили секции...'
                    }]
                }, {
                    xtype: 'panel',
                    title: 'JavaScript',
                    flex: 1,
                    layout: 'fit',
                    items: [{
                        xtype: 'textarea',
                        id: 'code-js-editor',
                        style: 'font-family: "Courier New", monospace; font-size: 13px;',
                        emptyText: 'JavaScript код секции...'
                    }]
                }]
            }]
        }],
        listeners: {
            close: function() {
                delete openWindows['section_builder'];
                removeTaskbarButton('section_builder');
            },
            activate: function() {
                setActiveWindow('section_builder');
            },
            beforeshow: function() {
                setActiveWindow('section_builder');
            },
            destroy: function() {
                if (window.grapesjsEditor) {
                    window.grapesjsEditor.destroy();
                    window.grapesjsEditor = null;
                }
            }
        }
    });

    // Регистрируем окно в глобальном объекте
    openWindows['section_builder'] = editorWindow;
    
    // Добавляем кнопку на панель задач
    addTaskbarButton('section_builder', 'Конструктор секций', editorWindow);
    
    editorWindow.show();
    editorWindow.center();
}

/**
 * Инициализация GrapesJS редактора
 */
function initGrapesJS() {
    console.log('Инициализация GrapesJS...');
    
    if (typeof grapesjs === 'undefined') {
        console.error('GrapesJS не загружен!');
        Ext.Msg.alert('Ошибка', 'Библиотека GrapesJS не загружена. Перезагрузите страницу.');
        return;
    }
    
    var container = document.querySelector('#gjs-editor');
    if (!container) {
        console.error('Контейнер #gjs-editor не найден!');
        return;
    }
    
    try {
        window.grapesjsEditor = grapesjs.init({
            container: '#gjs-editor',
            height: '100%',
            width: '100%',
            fromElement: false,
            storageManager: false,
            
            deviceManager: {
                devices: [{
                    name: 'Desktop',
                    width: ''
                }, {
                    name: 'Tablet',
                    width: '768px'
                }, {
                    name: 'Mobile',
                    width: '320px'
                }]
            },
            
            canvas: {
                styles: [
                    'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css'
                ]
            },
            
            blockManager: {
                blocks: [{
                    id: 'section',
                    label: '<div>Секция</div>',
                    content: '<section class="section"><div class="container">Новая секция</div></section>',
                    category: 'Основные'
                }, {
                    id: 'text',
                    label: '<div>Текст</div>',
                    content: '<div>Вставьте ваш текст здесь</div>',
                    category: 'Основные'
                }]
            }
        });
        
        console.log('✓ GrapesJS успешно инициализирован');
        
    } catch (e) {
        console.error('Ошибка при инициализации GrapesJS:', e);
        Ext.Msg.alert('Ошибка', 'Не удалось инициализировать GrapesJS: ' + e.message);
    }
}
/**
 * Переключение режима редактора (визуальный / код)
 */
function switchEditorMode(mode) {
    var cardPanel = Ext.getCmp('editor-card-panel');
    if (!cardPanel) return;
    
    if (mode === 'code') {
        syncFromVisualToCode();
        cardPanel.getLayout().setActiveItem(1);
    } else {
        syncFromCodeToVisual();
        cardPanel.getLayout().setActiveItem(0);
    }
}

/**
 * Синхронизация данных из визуального редактора в редактор кода
 */
function syncFromVisualToCode() {
    if (!window.grapesjsEditor) return;
    
    var htmlEditor = Ext.getCmp('code-html-editor');
    var cssEditor = Ext.getCmp('code-css-editor');
    var jsEditor = Ext.getCmp('code-js-editor');
    
    if (htmlEditor) {
        var html = window.grapesjsEditor.getHtml();
        htmlEditor.setValue(formatHtml(html));
    }
    if (cssEditor) {
        cssEditor.setValue(window.grapesjsEditor.getCss());
    }
    if (jsEditor) {
        jsEditor.setValue(window.currentSectionJs || '');
    }
}

/**
 * Синхронизация данных из редактора кода в визуальный редактор
 */
function syncFromCodeToVisual() {
    if (!window.grapesjsEditor) return;
    
    var htmlEditor = Ext.getCmp('code-html-editor');
    var cssEditor = Ext.getCmp('code-css-editor');
    var jsEditor = Ext.getCmp('code-js-editor');
    
    if (htmlEditor && htmlEditor.getValue()) {
        window.grapesjsEditor.setComponents(htmlEditor.getValue());
    }
    if (cssEditor && cssEditor.getValue()) {
        window.grapesjsEditor.setStyle(cssEditor.getValue());
    }
    if (jsEditor) {
        window.currentSectionJs = jsEditor.getValue();
    }
}

/**
 * Загрузить секцию в редактор
 */
function loadSectionIntoEditor(sectionId) {
    if (!sectionId) return;
    
    Ext.Ajax.request({
        url: '/admin/section_builder/?action=get&id=' + sectionId,
        success: function(response) {
            var data = Ext.decode(response.responseText);
            
            if (data.success && data.section) {
                var section = data.section;
                
                // Обновляем название в тулбаре
                var nameField = Ext.getCmp('current-section-name');
                if (nameField) {
                    nameField.setText('<b>' + section.name + '</b>');
                }
                
                // Сохраняем текущий ID секции
                window.currentSectionId = section.id;
                window.currentSectionName = section.name;
                
                // Загружаем данные в редактор
                if (window.grapesjsEditor) {
                    window.grapesjsEditor.setComponents(section.html || '');
                    window.grapesjsEditor.setStyle(section.css || '');
                    window.currentSectionJs = section.js || '';
                    
                    // Также загружаем данные в редактор кода
                    var htmlEditor = Ext.getCmp('code-html-editor');
                    var cssEditor = Ext.getCmp('code-css-editor');
                    var jsEditor = Ext.getCmp('code-js-editor');
                    
                    if (htmlEditor) htmlEditor.setValue(section.html || '');
                    if (cssEditor) cssEditor.setValue(section.css || '');
                    if (jsEditor) jsEditor.setValue(section.js || '');
                }
            } else {
                Ext.Msg.alert('Ошибка', data.error || 'Не удалось загрузить секцию');
            }
        },
        failure: function() {
            Ext.Msg.alert('Ошибка', 'Ошибка сервера при загрузке секции');
        }
    });
}
/**
 * Сохранить текущую секцию
 */
function saveCurrentSection() {
    if (!window.currentSectionId) {
        Ext.Msg.alert('Внимание', 'Сначала выберите секцию для редактирования');
        return;
    }
    
    var html, css, js;
    
    // Определяем активный режим редактора
    var cardPanel = Ext.getCmp('editor-card-panel');
    var activeItem = cardPanel ? cardPanel.getLayout().getActiveItem() : null;
    var isCodeMode = activeItem && activeItem.id === 'code-editor-panel';
    
    if (isCodeMode) {
        // Берем данные из редактора кода
        var htmlEditor = Ext.getCmp('code-html-editor');
        var cssEditor = Ext.getCmp('code-css-editor');
        var jsEditor = Ext.getCmp('code-js-editor');
        
        html = htmlEditor ? htmlEditor.getValue() : '';
        css = cssEditor ? cssEditor.getValue() : '';
        js = jsEditor ? jsEditor.getValue() : '';
    } else {
        // Берем данные из визуального редактора
        if (!window.grapesjsEditor) {
            Ext.Msg.alert('Ошибка', 'Редактор не инициализирован');
            return;
        }
        
        html = formatHtml(window.grapesjsEditor.getHtml());
        css = window.grapesjsEditor.getCss();
        js = window.currentSectionJs || '';
    }
    
    Ext.Ajax.request({
        url: '/admin/section_builder/?action=save',
        method: 'POST',
        params: {
            id: window.currentSectionId,
            name: window.currentSectionName,
            html: html,
            css: css,
            js: js
        },
        success: function(response) {
            var data = Ext.decode(response.responseText);
            if (data.success) {
                Ext.Msg.alert('Успех', 'Секция сохранена!');
                
                // Обновляем список секций
                Ext.getCmp('sections-grid').getStore().load();
            } else {
                Ext.Msg.alert('Ошибка', data.error || 'Не удалось сохранить секцию');
            }
        },
        failure: function() {
            Ext.Msg.alert('Ошибка', 'Ошибка сервера при сохранении');
        }
    });
}

/**
 * Создать новую секцию
 */
function createNewSection(name) {
    Ext.Ajax.request({
        url: '/admin/section_builder/?action=create',
        method: 'POST',
        params: {
            name: name
        },
        success: function(response) {
            var data = Ext.decode(response.responseText);
            
            if (data.success) {
                Ext.Msg.alert('Успех', 'Секция создана');
                
                // Обновляем список
                var grid = Ext.getCmp('sections-grid');
                grid.getStore().load({
                    callback: function(records, operation, success) {
                        // Находим и выбираем новую секцию
                        var record = grid.getStore().findRecord('id', data.id);
                        if (record) {
                            grid.getSelectionModel().select(record);
                        }
                    }
                });
            } else {
                Ext.Msg.alert('Ошибка', data.error || 'Не удалось создать секцию');
            }
        },
        failure: function() {
            Ext.Msg.alert('Ошибка', 'Ошибка сервера при создании секции');
        }
    });
}

/**
 * Удалить секцию
 */
function deleteSection(sectionId) {
    Ext.Ajax.request({
        url: '/admin/section_builder/?action=delete',
        method: 'POST',
        params: {
            id: sectionId
        },
        success: function(response) {
            var data = Ext.decode(response.responseText);
            if (data.success) {
                Ext.Msg.alert('Успех', 'Секция удалена');
                
                // Обновляем список
                Ext.getCmp('sections-grid').getStore().load();
                
                // Очищаем редактор если удалена текущая секция
                if (window.currentSectionId === sectionId) {
                    window.currentSectionId = null;
                    window.currentSectionName = null;
                    if (window.grapesjsEditor) {
                        window.grapesjsEditor.setComponents('');
                        window.grapesjsEditor.setStyle('');
                    }
                    Ext.getCmp('current-section-name').setText('Выберите секцию для редактирования');
                }
            } else {
                Ext.Msg.alert('Ошибка', data.error || 'Не удалось удалить секцию');
            }
        },
        failure: function() {
            Ext.Msg.alert('Ошибка', 'Ошибка сервера при удалении');
        }
    });
}

/**
 * Дублировать секцию
 */
function duplicateSection(sectionId, sectionName) {
    Ext.Msg.prompt('Дублировать секцию', 'Введите название для копии:', function(btn, newName) {
        if (btn === 'ok' && newName) {
            Ext.Ajax.request({
                url: '/admin/section_builder/?action=duplicate',
                method: 'POST',
                params: {
                    id: sectionId,
                    new_name: newName
                },
                success: function(response) {
                    var data = Ext.decode(response.responseText);
                    if (data.success) {
                        Ext.Msg.alert('Успех', 'Секция "' + newName + '" создана');
                        
                        // Обновляем список
                        var grid = Ext.getCmp('sections-grid');
                        var store = grid.getStore();
                        store.load({
                            callback: function() {
                                // Находим и выделяем новую секцию
                                if (data.id) {
                                    var newRecord = store.findRecord('id', data.id);
                                    if (newRecord) {
                                        grid.getSelectionModel().select(newRecord);
                                    }
                                }
                            }
                        });
                    } else {
                        Ext.Msg.alert('Ошибка', data.error || 'Не удалось дублировать секцию');
                    }
                },
                failure: function() {
                    Ext.Msg.alert('Ошибка', 'Ошибка сервера при дублировании');
                }
            });
        }
    }, this, false, sectionName + ' (копия)');
}
