<script>
{{-- ============================================ --}}
{{-- Menu/Pages Management - Управление меню (страницами) --}}
{{-- ============================================ --}}

{{-- Открытие окна управления меню (страницами) --}}
{{-- Реализация по образцу engine.js --}}
function openMenu() {
    closeStartMenu();
    
    if (openWindows['menu']) {
        openWindows['menu'].show();
        openWindows['menu'].toFront();
        return;
    }
    
    Ext.onReady(function() {
        {{-- TreeStore для страниц --}}
        var pagesStore = Ext.create('Ext.data.TreeStore', {
            fields: [
                {name: 'id', type: 'int'},
                'text', 
                'name', 
                'title', 
                {name: 'parent_id', type: 'int'},
                {name: 'sort', type: 'int'},
                {name: 'status', type: 'int'},
                {name: 'leaf', type: 'boolean'}
            ],
            proxy: {
                type: 'ajax',
                url: '/admin/menu/?action=list_pages',
                reader: {
                    type: 'json',
                    root: 'children'
                }
            },
            root: {
                text: 'Страницы',
                id: 'root',
                expanded: true
            },
            folderSort: false,
            autoLoad: true
        });
        
        {{-- Функция получения порядка дочерних узлов (как getParentOrder в engine.js) --}}
        var getParentOrder = function(node) {
            var order = [];
            if (node && node.parentNode) {
                node.parentNode.eachChild(function(child) {
                    var childId = null;
                    if (child.getId) {
                        childId = parseInt(child.getId(), 10);
                    } else if (child.get) {
                        childId = parseInt(child.get('id'), 10);
                    } else if (child.id) {
                        childId = parseInt(child.id, 10);
                    }
                    if (childId && !isNaN(childId)) {
                        order.push(childId);
                    }
                });
            }
            return Ext.encode(order);
        };
        
        {{-- Tree Panel для страниц с drag-and-drop --}}
        var treePanel = Ext.create('Ext.tree.Panel', {
            title: 'Страницы',
            region: 'west',
            width: 350,
            split: true,
            useArrows: true,
            rootVisible: false,
            animate: true,
            enableDD: true,
            containerScroll: true,
            store: pagesStore,
            viewConfig: {
                plugins: {
                    ptype: 'treeviewdragdrop',
                    containerScroll: true,
                    allowParentInserts: true,
                    expandDelay: 200
                },
                listeners: {
                    {{-- При drop на узел - определяем куда перемещать --}}
                    beforedrop: function(node, data, overModel, dropPosition, dropHandlers) {
                        var draggedNode = data.records[0];
                        var targetId = overModel ? parseInt(overModel.getId(), 10) : 0;
                        
                        {{-- Родитель перетаскиваемого узла --}}
                        var draggedParentId = draggedNode.parentNode ? draggedNode.parentNode.getId() : 'root';
                        {{-- Родитель целевого узла --}}
                        var targetParentId = overModel && overModel.parentNode ? overModel.parentNode.getId() : 'root';
                        
                        {{-- Если целевой узел на КОРНЕВОМ уровне - разрешаем sibling --}}
                        {{-- (чтобы можно было вынести страницу на верхний уровень) --}}
                        if (targetParentId === 'root') {
                            return true; // Стандартное поведение
                        }
                        
                        {{-- Если перетаскиваем на ВЛОЖЕННЫЙ узел из ДРУГОГО родителя и позиция before/after - --}}
                        {{-- принудительно делаем append (внутрь), а не sibling --}}
                        if ((dropPosition === 'before' || dropPosition === 'after') && 
                            targetId > 0 && 
                            draggedParentId !== targetParentId) {
                            
                            {{-- Отменяем стандартный drop --}}
                            dropHandlers.cancelDrop();
                            
                            {{-- Делаем целевой узел не-листом (папкой) --}}
                            overModel.set('leaf', false);
                            
                            {{-- Перемещаем узел внутрь целевого --}}
                            overModel.appendChild(draggedNode);
                            
                            {{-- Раскрываем родительский узел чтобы показать дочерний --}}
                            overModel.expand();
                            
                            {{-- Сохраняем на сервер --}}
                            var nodeId = parseInt(draggedNode.getId(), 10);
                            var newParentId = targetId;
                            var order = [];
                            overModel.eachChild(function(child) {
                                var childId = parseInt(child.getId(), 10);
                                if (childId && !isNaN(childId)) {
                                    order.push(childId);
                                }
                            });
                            
                            Ext.Ajax.request({
                                url: '/admin/menu/?action=saveOrder',
                                method: 'POST',
                                params: {
                                    node_id: nodeId,
                                    parent_id: newParentId,
                                    order: Ext.encode(order)
                                },
                                failure: function() {
                                    Ext.Msg.alert('Ошибка', 'Не удалось сохранить');
                                    pagesStore.load();
                                }
                            });
                            
                            return false;
                        }
                        
                        return true;
                    }
                }
            },
            tbar: [{
                text: 'Добавить',
                iconCls: 'icon-add',
                handler: function() {
                    showPageFormForNode(null, null);
                }
            }, '-', {
                text: 'Подстраница',
                handler: function() {
                    var selection = treePanel.getSelectionModel().getSelection();
                    if (!selection || selection.length === 0) {
                        Ext.Msg.alert('Ошибка', 'Выберите страницу');
                        return;
                    }
                    var node = selection[0];
                    var parentId = node.getId ? parseInt(node.getId(), 10) : parseInt(node.id, 10);
                    if (!parentId || isNaN(parentId)) {
                        Ext.Msg.alert('Ошибка', 'Не удалось определить ID страницы');
                        return;
                    }
                    showPageFormForNode(null, parentId);
                }
            }, '-', {
                text: 'Удалить',
                iconCls: 'icon-delete',
                handler: function() {
                    var selection = treePanel.getSelectionModel().getSelection();
                    if (!selection || selection.length === 0) {
                        Ext.Msg.alert('Ошибка', 'Выберите страницу для удаления');
                        return;
                    }
                    deletePageNode(selection[0]);
                }
            }, '->', {
                text: 'Обновить',
                iconCls: 'icon-refresh',
                handler: function() {
                    pagesStore.load();
                }
            }],
            listeners: {
                itemclick: function(view, record) {
                    var nodeId = record.getId ? parseInt(record.getId(), 10) : parseInt(record.id, 10);
                    if (nodeId && !isNaN(nodeId)) {
                        loadPageContent(nodeId);
                    }
                },
                {{-- Контекстное меню (правый клик) --}}
                itemcontextmenu: function(view, record, item, index, e) {
                    e.stopEvent();
                    
                    var nodeId = parseInt(record.getId(), 10);
                    var parentNode = record.parentNode;
                    var isRoot = !parentNode || parentNode.getId() === 'root';
                    
                    var menuItems = [];
                    
                    {{-- Опция "На верхний уровень" - только если страница не на верхнем уровне --}}
                    if (!isRoot) {
                        menuItems.push({
                            text: 'На верхний уровень',
                            iconCls: 'icon-arrow-up',
                            handler: function() {
                                {{-- Перемещаем на корневой уровень --}}
                                var rootNode = pagesStore.getRootNode();
                                
                                {{-- Удаляем из текущего родителя --}}
                                record.parentNode.removeChild(record);
                                
                                {{-- Добавляем в корень --}}
                                rootNode.appendChild(record);
                                
                                {{-- Получаем порядок корневых узлов --}}
                                var order = [];
                                rootNode.eachChild(function(child) {
                                    var childId = parseInt(child.getId(), 10);
                                    if (childId && !isNaN(childId)) {
                                        order.push(childId);
                                    }
                                });
                                
                                {{-- Сохраняем на сервер --}}
                                Ext.Ajax.request({
                                    url: '/admin/menu/?action=saveOrder',
                                    method: 'POST',
                                    params: {
                                        node_id: nodeId,
                                        parent_id: 0,
                                        order: Ext.encode(order)
                                    },
                                    success: function(response) {
                                        var result = Ext.decode(response.responseText);
                                        if (!result.success) {
                                            Ext.Msg.alert('Ошибка', result.message || 'Не удалось переместить');
                                            pagesStore.load();
                                        }
                                    },
                                    failure: function() {
                                        Ext.Msg.alert('Ошибка', 'Не удалось переместить');
                                        pagesStore.load();
                                    }
                                });
                            }
                        });
                        menuItems.push('-');
                    }
                    
                    {{-- Удалить --}}
                    menuItems.push({
                        text: 'Удалить',
                        iconCls: 'icon-delete',
                        handler: function() {
                            deletePageNode(record);
                        }
                    });
                    
                    var contextMenu = Ext.create('Ext.menu.Menu', {
                        items: menuItems
                    });
                    contextMenu.showAt(e.getXY());
                },
                {{-- Событие при перемещении узла (drag-and-drop) --}}
                itemmove: function(node, oldParent, newParent, index, eOpts) {
                    var nodeId = parseInt(node.getId(), 10);
                    
                    {{-- ID нового родителя (0 для корня) --}}
                    var newParentId = 0;
                    if (newParent && newParent.getId() !== 'root') {
                        newParentId = parseInt(newParent.getId(), 10);
                    }
                    if (isNaN(newParentId)) newParentId = 0;
                    
                    {{-- Порядок дочерних узлов нового родителя --}}
                    var order = [];
                    if (newParent && newParent.eachChild) {
                        newParent.eachChild(function(child) {
                            var childId = parseInt(child.getId(), 10);
                            if (childId && !isNaN(childId)) {
                                order.push(childId);
                            }
                        });
                    }
                    
                    {{-- Сохраняем на сервер --}}
                    Ext.Ajax.request({
                        url: '/admin/menu/?action=saveOrder',
                        method: 'POST',
                        params: {
                            node_id: nodeId,
                            parent_id: newParentId,
                            order: Ext.encode(order)
                        },
                        success: function(response) {
                            var result = Ext.decode(response.responseText);
                            if (!result.success && result.message) {
                                Ext.Msg.alert('Ошибка', result.message);
                                pagesStore.load();
                            }
                        },
                        failure: function() {
                            Ext.Msg.alert('Ошибка', 'Не удалось сохранить порядок');
                            pagesStore.load();
                        }
                    });
                }
            }
        });
        
        {{-- Функция показа формы для создания страницы --}}
        function showPageFormForNode(node, parentId) {
            {{-- Функция сохранения страницы --}}
            var savePage = function(form) {
                if (form.isValid()) {
                    var values = form.getValues();
                    values.parent_id = parentId || '';
                    
                    Ext.Ajax.request({
                        url: '/admin/menu/?action=create',
                        method: 'POST',
                        params: values,
                        success: function(response) {
                            var result = Ext.decode(response.responseText);
                            if (result.success) {
                                formWindow.close();
                                pagesStore.load();
                            } else {
                                Ext.Msg.alert('Ошибка', result.message);
                            }
                        }
                    });
                }
            };
            
            var formWindow = Ext.create('Ext.window.Window', {
                title: 'Создать страницу',
                width: 400,
                modal: true,
                layout: 'fit',
                defaultFocus: 'nameField', {{-- Автофокус на поле названия --}}
                items: [{
                    xtype: 'form',
                    padding: 10,
                    defaults: {
                        anchor: '100%',
                        labelWidth: 100,
                        enableKeyEvents: true,
                        listeners: {
                            {{-- Сохранение по Enter --}}
                            specialkey: function(field, e) {
                                if (e.getKey() === e.ENTER) {
                                    savePage(field.up('form').getForm());
                                }
                            }
                        }
                    },
                    items: [{
                        xtype: 'textfield',
                        itemId: 'nameField',
                        name: 'name',
                        fieldLabel: 'Название',
                        allowBlank: false
                    }, {
                        xtype: 'textfield',
                        name: 'title',
                        fieldLabel: 'Заголовок'
                    }],
                    buttons: [{
                        text: 'Отмена',
                        handler: function() {
                            formWindow.close();
                        }
                    }, {
                        text: 'Создать',
                        formBind: true,
                        handler: function() {
                            savePage(this.up('form').getForm());
                        }
                    }]
                }],
                listeners: {
                    {{-- Дополнительно устанавливаем фокус после показа окна --}}
                    show: function(win) {
                        var nameField = win.down('#nameField');
                        if (nameField) {
                            nameField.focus(false, 100);
                        }
                    }
                }
            });
            formWindow.show();
        }
        
        {{-- Функция удаления страницы --}}
        function deletePageNode(node) {
            var nodeId = null;
            if (node.getId) {
                nodeId = parseInt(node.getId(), 10);
            } else if (node.get) {
                nodeId = parseInt(node.get('id'), 10);
            } else if (node.id) {
                nodeId = parseInt(node.id, 10);
            }
            
            if (!nodeId || isNaN(nodeId)) {
                Ext.Msg.alert('Ошибка', 'Не удалось определить ID страницы');
                return;
            }
            
            var nodeName = (node.get && node.get('text')) || (node.data && node.data.name) || 'страницу';
            
            Ext.Msg.confirm('Подтверждение', 'Удалить страницу "' + nodeName + '"?', function(btn) {
                if (btn === 'yes') {
                    Ext.Ajax.request({
                        url: '/admin/menu/?action=delete',
                        method: 'POST',
                        params: { id: nodeId },
                        success: function(response) {
                            var result = Ext.decode(response.responseText);
                            if (result.success) {
                                pagesStore.load();
                                var container = Ext.getCmp('page-editor-container');
                                if (container) {
                                    container.update('<div style="padding: 20px; text-align: center; color: #999;">Выберите страницу для редактирования</div>');
                                }
                            } else {
                                Ext.Msg.alert('Ошибка', result.message);
                            }
                        }
                    });
                }
            });
        }
        
        {{-- WYSIWYG редактор для содержимого страницы --}}
        var editorPanel = Ext.create('Ext.panel.Panel', {
            title: 'Редактирование страницы',
            region: 'center',
            layout: 'fit',
            id: 'page-editor-container',
            bodyStyle: 'background: #f5f5f5;',
            html: '<div style="padding: 40px; text-align: center; color: #999; font-size: 14px;">Выберите страницу для редактирования</div>'
        });
        
        {{-- Флаг для предотвращения двойного вызова loadPageContent --}}
        var isLoadingPage = false;
        
        {{-- Функция загрузки содержимого страницы --}}
        function loadPageContent(pageId) {
            var pageIdNum = parseInt(pageId, 10);
            if (!pageIdNum || isNaN(pageIdNum) || pageIdNum <= 0) {
                return;
            }
            
            if (isLoadingPage) return;
            isLoadingPage = true;
            
            Ext.Ajax.request({
                url: '/admin/menu/?action=get_page',
                method: 'GET',
                params: { id: pageIdNum },
                success: function(response) {
                    isLoadingPage = false;
                    var result = Ext.decode(response.responseText);
                    if (result.success && result.data) {
                        showPageEditor(result.data, pagesStore);
                    } else {
                        {{-- Ошибка загрузки страницы --}}
                        Ext.Msg.alert('Ошибка', result.message || 'Не удалось загрузить страницу');
                    }
                },
                failure: function(response) {
                    isLoadingPage = false;
                    {{-- AJAX ошибка при загрузке страницы --}}
                    Ext.Msg.alert('Ошибка', 'Не удалось загрузить страницу. Проверьте консоль для деталей.');
                }
            });
        }
        
        {{-- Функция отображения редактора страницы --}}
        function showPageEditor(pageData, store) {
            var container = Ext.getCmp('page-editor-container');
            if (!container) return;
            
            {{-- Обновляем заголовок с ID страницы --}}
            container.setTitle('Редактирование страницы #' + pageData.id);
            
            {{-- Удаляем предыдущий редактор, если есть --}}
            container.removeAll();
            
            {{-- Создаем форму редактирования --}}
            var form = Ext.create('Ext.form.Panel', {
                layout: {
                    type: 'vbox',
                    align: 'stretch'
                },
                bodyPadding: 10,
                items: [{
                    xtype: 'hidden',
                    name: 'id',
                    value: pageData.id
                }, {
                    xtype: 'textfield',
                    name: 'name',
                    fieldLabel: 'Название',
                    value: pageData.name,
                    allowBlank: false,
                    labelWidth: 80
                }, {
                    xtype: 'textfield',
                    name: 'title',
                    fieldLabel: 'Заголовок',
                    value: pageData.title || '',
                    labelWidth: 80
                }, {
                    xtype: 'htmleditor',
                    name: 'content',
                    fieldLabel: 'Содержимое',
                    value: pageData.content || '',
                    flex: 1,
                    labelWidth: 80,
                    labelAlign: 'top'
                }],
                buttons: [{
                    text: 'Сохранить',
                    handler: function() {
                        var form = this.up('form').getForm();
                        if (form.isValid()) {
                            var values = form.getValues();
                            
                            {{-- Используем parent_id из данных страницы --}}
                            values.parent_id = pageData.parent_id || null;
                            
                            Ext.Ajax.request({
                                url: '/admin/menu/?action=update',
                                method: 'POST',
                                params: values,
                                success: function(response) {
                                    var result = Ext.decode(response.responseText);
                                    if (result.success) {
                                        Ext.Msg.alert('Успех', result.message);
                                        {{-- Обновляем название в дереве --}}
                                        var node = treePanel.getNodeById(pageData.id);
                                        if (node) {
                                            node.set('text', values.name);
                                            node.set('name', values.name);
                                            node.set('title', values.title);
                                        }
                                        {{-- Перезагружаем дерево для обновления структуры --}}
                                        pagesStore.load();
                                    } else {
                                        Ext.Msg.alert('Ошибка', result.message);
                                    }
                                },
                                failure: function() {
                                    Ext.Msg.alert('Ошибка', 'Не удалось сохранить страницу');
                                }
                            });
                        }
                    }
                }]
            });
            
            container.add(form);
            container.doLayout();
        }
        
        var win = Ext.create('Ext.window.Window', {
            title: 'Управление меню (страницы)',
            width: 1200,
            height: 700,
            layout: 'border',
            maximizable: true,
            minimizable: true,
            constrain: true,
            x: 50,
            y: 30,
            items: [treePanel, editorPanel],
            listeners: {
                close: function() {
                    delete openWindows['menu'];
                    removeTaskbarButton('menu');
                },
                activate: function() {
                    setActiveWindow('menu');
                },
                minimize: function() {
                    this.hide();
                    var btn = document.getElementById('taskbar-menu');
                    if (btn) {
                        btn.classList.add('minimized');
                        btn.classList.remove('active');
                    }
                }
            }
        });
        
        openWindows['menu'] = win;
        addTaskbarButton('menu', 'Управление меню', win);
        win.show();
    });
}
</script>