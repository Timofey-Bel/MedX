<script>
{{-- ============================================ --}}
{{-- Permissions Management - Управление правами --}}
{{-- ============================================ --}}

function openPermissions() {
    closeStartMenu();
    
    if (openWindows['permissions']) {
        openWindows['permissions'].show();
        openWindows['permissions'].toFront();
        return;
    }
    
    {{-- Store для ролей --}}
    var rolesStore = Ext.create('Ext.data.Store', {
        fields: ['id', 'name', 'title', 'description'],
        proxy: {
            type: 'ajax',
            url: '/admin/permissions/?action=list_roles',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            },
            listeners: {
                exception: function(proxy, response, operation) {
                    {{-- Ошибка загрузки ролей --}}
                    Ext.Msg.alert('Ошибка загрузки ролей', 'Возможно, таблицы ACL не созданы.');
                }
            }
        },
        autoLoad: true,
        listeners: {
            load: function(store, records, successful) {
                {{-- Роли загружены --}}
            }
        }
    });
    
    {{-- Store для пользователей --}}
    var usersStore = Ext.create('Ext.data.Store', {
        fields: ['id', 'login', 'name', 'email', 'role', 'role_title', 'active', 'last_login'],
        proxy: {
            type: 'ajax',
            url: '/admin/permissions/?action=list_users',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            },
            listeners: {
                exception: function(proxy, response, operation) {
                    {{-- Ошибка загрузки пользователей --}}
                }
            }
        },
        autoLoad: true,
        listeners: {
            load: function(store, records, successful) {
                {{-- Пользователи загружены --}}
            }
        }
    });
        
        {{-- TabPanel с двумя вкладками --}}
        var tabs = Ext.create('Ext.tab.Panel', {
            activeTab: 0,
            items: [
                {
                    title: 'Пользователи',
                    layout: 'border',
                    items: [
                        {
                            region: 'west',
                            width: 300,
                            split: true,
                            title: 'Пользователи',
                            xtype: 'grid',
                            store: usersStore,
                            columns: [
                                { text: 'Логин', dataIndex: 'login', flex: 1 },
                                { text: 'Имя', dataIndex: 'name', flex: 1 }
                            ],
                            listeners: {
                                selectionchange: function(sm, selected) {
                                    if (selected.length > 0) {
                                        loadUserModuleAccess(selected[0].get('id'));
                                    }
                                }
                            }
                        },
                        {
                            region: 'center',
                            title: 'Доступ к модулям',
                            id: 'user-modules-container',
                            layout: 'fit',
                            html: '<div style="padding: 20px; text-align: center; color: #999;">Выберите пользователя для просмотра прав доступа</div>'
                        }
                    ]
                },
                {
                    title: 'Роли и права',
                    layout: 'border',
                    items: [
                        {
                            region: 'west',
                            width: 300,
                            split: true,
                            title: 'Роли',
                            xtype: 'grid',
                            store: rolesStore,
                            columns: [
                                { text: 'Название', dataIndex: 'title', flex: 1 },
                                { text: 'Код', dataIndex: 'name', width: 100 }
                            ],
                            listeners: {
                                selectionchange: function(sm, selected) {
                                    if (selected.length > 0) {
                                        loadRolePermissions(selected[0].get('id'));
                                    }
                                }
                            }
                        },
                        {
                            region: 'center',
                            title: 'Права доступа',
                            id: 'permissions-grid-container',
                            layout: 'fit',
                            html: '<div style="padding: 20px; text-align: center; color: #999;">Выберите роль для просмотра прав</div>'
                        }
                    ]
                }
                
            ]
        });
        
        var win = Ext.create('Ext.window.Window', {
            title: 'Управление правами доступа',
            width: 1200,
            height: 700,
            layout: 'fit',
            maximizable: true,
            minimizable: true,
            constrain: true,
            x: 50,
            y: 30,
            items: [tabs],
            listeners: {
                close: function() {
                    delete openWindows['permissions'];
                    removeTaskbarButton('permissions');
                },
                activate: function() {
                    setActiveWindow('permissions');
                },
                minimize: function() {
                    this.hide();
                    var btn = document.getElementById('taskbar-permissions');
                    if (btn) {
                        btn.classList.add('minimized');
                        btn.classList.remove('active');
                    }
                }
            }
        });
        
        openWindows['permissions'] = win;
        addTaskbarButton('permissions', 'Управление правами', win);
        win.show();
        
        {{-- Функция загрузки прав роли --}}
        function loadRolePermissions(roleId) {
            Ext.Ajax.request({
                url: '/admin/permissions/?action=get_role_permissions&role_id=' + roleId,
                success: function(response) {
                    var result = Ext.decode(response.responseText);
                    if (result.success) {
                        showPermissionsGrid(result.role, result.permissions);
                    } else {
                        Ext.Msg.alert('Ошибка', result.message);
                    }
                },
                failure: function() {
                    Ext.Msg.alert('Ошибка', 'Не удалось загрузить права');
                }
            });
        }
        
        {{-- Показать таблицу прав --}}
        function showPermissionsGrid(role, permissions) {
            var container = Ext.getCmp('permissions-grid-container');
            container.removeAll();
            
            {{-- Группируем права по модулям --}}
            var grouped = {};
            Ext.each(permissions, function(p) {
                if (!grouped[p.module]) {
                    grouped[p.module] = [];
                }
                grouped[p.module].push(p);
            });
            
            {{-- Создаем Store --}}
            var permissionsStore = Ext.create('Ext.data.Store', {
                fields: ['id', 'module', 'action', 'title', 'description', 'granted'],
                data: permissions
            });
            
            var grid = Ext.create('Ext.grid.Panel', {
                store: permissionsStore,
                selModel: {
                    selType: 'checkboxmodel',
                    checkOnly: true,
                    mode: 'MULTI',
                    listeners: {
                        select: function(sm, record) {
                            record.set('granted', 1);
                        },
                        deselect: function(sm, record) {
                            record.set('granted', 0);
                        }
                    }
                },
                columns: [
                    { text: 'Модуль', dataIndex: 'module', width: 120 },
                    { text: 'Действие', dataIndex: 'action', width: 100 },
                    { text: 'Название', dataIndex: 'title', flex: 1 },
                    { text: 'Описание', dataIndex: 'description', flex: 2 }
                ],
                tbar: [
                    {
                        text: 'Сохранить изменения',
                        handler: function() {
                            saveRolePermissions(role.id, permissionsStore);
                        }
                    },
                    '->',
                    '<b>Редактирование роли: ' + role.title + '</b>'
                ],
                listeners: {
                    afterrender: function(grid) {
                        {{-- Выбираем строки с granted=1 --}}
                        var sm = grid.getSelectionModel();
                        permissionsStore.each(function(record) {
                            if (record.get('granted') == 1) {
                                sm.select(record, true, true);
                            }
                        });
                    }
                }
            });
            
            container.add(grid);
        }
        
        {{-- Сохранить права роли --}}
        function saveRolePermissions(roleId, store) {
            var selectedIds = [];
            store.each(function(record) {
                if (record.get('granted') == 1) {
                    selectedIds.push(record.get('id'));
                }
            });
            
            Ext.Ajax.request({
                url: '/admin/permissions/?action=save_role_permissions',
                method: 'POST',
                jsonData: {
                    role_id: roleId,
                    permissions: selectedIds
                },
                success: function(response) {
                    var result = Ext.decode(response.responseText);
                    if (result.success) {
                        Ext.Msg.alert('Успешно', result.message);
                    } else {
                        Ext.Msg.alert('Ошибка', result.message);
                    }
                },
                failure: function() {
                    Ext.Msg.alert('Ошибка', 'Не удалось сохранить права');
                }
            });
        }
        
        {{-- Форма изменения роли пользователя --}}
        function showChangeRoleForm(userRecord, usersStore, rolesStore) {
            var form = Ext.create('Ext.form.Panel', {
                bodyPadding: 15,
                defaults: {
                    anchor: '100%',
                    labelWidth: 100
                },
                items: [
                    {
                        xtype: 'displayfield',
                        fieldLabel: 'Пользователь',
                        value: userRecord.get('name') + ' (' + userRecord.get('login') + ')'
                    },
                    {
                        xtype: 'combobox',
                        fieldLabel: 'Новая роль',
                        name: 'role',
                        store: rolesStore,
                        displayField: 'title',
                        valueField: 'name',
                        value: userRecord.get('role'),
                        editable: false,
                        allowBlank: false
                    }
                ]
            });
            
            var formWindow = Ext.create('Ext.window.Window', {
                title: 'Изменить роль пользователя',
                width: 400,
                modal: true,
                layout: 'fit',
                items: [form],
                buttons: [
                    {
                        text: 'Сохранить',
                        handler: function() {
                            var values = form.getValues();
                            Ext.Ajax.request({
                                url: '/admin/permissions/?action=update_user_role',
                                method: 'POST',
                                params: {
                                    user_id: userRecord.get('id'),
                                    role: values.role
                                },
                                success: function(response) {
                                    var result = Ext.decode(response.responseText);
                                    if (result.success) {
                                        Ext.Msg.alert('Успешно', result.message);
                                        usersStore.load();
                                        formWindow.close();
                                    } else {
                                        Ext.Msg.alert('Ошибка', result.message);
                                    }
                                },
                                failure: function() {
                                    Ext.Msg.alert('Ошибка', 'Не удалось изменить роль');
                                }
                            });
                        }
                    },
                    {
                        text: 'Отмена',
                        handler: function() {
                            formWindow.close();
                        }
                    }
                ]
            });
            
            formWindow.show();
        }

        {{-- Загрузить индивидуальные права пользователя --}}
        function loadUserModuleAccess(userId) {
            Ext.Ajax.request({
                url: '/admin/permissions/?action=get_user_module_access&user_id=' + userId,
                success: function(response) {
                    var result = Ext.decode(response.responseText);
                    if (result.success) {
                        showUserModulesGrid(result.user, result.modules);
                    } else {
                        Ext.Msg.alert('Ошибка', result.message);
                    }
                },
                failure: function() {
                    Ext.Msg.alert('Ошибка', 'Не удалось загрузить права пользователя');
                }
            });
        }

        {{-- Показать таблицу индивидуальных прав пользователя --}}
        function showUserModulesGrid(user, modules) {
            var container = Ext.getCmp('user-modules-container');
            container.removeAll();

            {{-- Создаем Store для модулей --}}
            var modulesStore = Ext.create('Ext.data.Store', {
                fields: ['module', 'role_access', 'user_access', 'is_custom'],
                data: modules
            });

            var grid = Ext.create('Ext.grid.Panel', {
                store: modulesStore,
                columns: [
                    { text: 'Модуль', dataIndex: 'module', flex: 1 },
                    {
                        text: 'Права роли',
                        dataIndex: 'role_access',
                        width: 100,
                        renderer: function(value) {
                            return value ? '<span style="color: green;">✓ Доступен</span>' : '<span style="color: red;">✗ Запрещен</span>';
                        }
                    },
                    {
                        xtype: 'checkcolumn',
                        text: 'Индивидуальные права',
                        dataIndex: 'user_access',
                        width: 160,
                        listeners: {
                            checkchange: function(checkColumn, rowIndex, checked) {
                                var record = modulesStore.getAt(rowIndex);
                                record.set('user_access', checked);
                                record.set('is_custom', true);
                            }
                        }
                    },
                    {
                        text: 'Статус',
                        dataIndex: 'is_custom',
                        width: 100,
                        renderer: function(value) {
                            return value ? '<span style="color: blue;">Индивидуально</span>' : '<span style="color: gray;">По роли</span>';
                        }
                    }
                ],
                tbar: [
                    {
                        text: 'Сохранить изменения',
                        handler: function() {
                            saveUserModuleAccess(user.id, modulesStore);
                        }
                    },
                    {
                        text: 'Сбросить индивидуальные права',
                        handler: function() {
                            Ext.Msg.confirm('Подтверждение', 'Сбросить индивидуальные права пользователя к правам роли?', function(btn) {
                                if (btn === 'yes') {
                                    resetUserModuleAccess(user.id, modulesStore);
                                }
                            });
                        }
                    },
                    '->',
                    '<b>Пользователь: ' + user.name + ' (' + user.login + ') | Роль: ' + user.role + '</b>'
                ],
                listeners: {
                    afterrender: function(grid) {
                        {{-- Выбираем чекбоксы согласно user_access --}}
                        var view = grid.getView();
                        modulesStore.each(function(record, index) {
                            var node = view.getNode(index);
                            if (node) {
                                var checkbox = node.querySelector('input[type="checkbox"]');
                                if (checkbox) {
                                    checkbox.checked = record.get('user_access');
                                }
                            }
                        });
                    }
                }
            });

            container.add(grid);
        }

        {{-- Сохранить индивидуальные права пользователя --}}
        function saveUserModuleAccess(userId, store) {
            var moduleAccess = {};
            store.each(function(record) {
                moduleAccess[record.get('module')] = record.get('user_access');
            });
            Ext.Ajax.request({
                url: '/admin/permissions/?action=save_user_module_access',
                method: 'POST',
                jsonData: {
                    user_id: userId,
                    modules: moduleAccess
                },
                success: function(response) {
                    var result = Ext.decode(response.responseText);
                    if (result.success) {
                        Ext.Msg.alert('Успешно', result.message);
                        store.each(function(record) {
                            if (record.get('user_access') != record.get('role_access')) {
                                record.set('is_custom', true);
                            } else {
                                record.set('is_custom', false);
                            }
                        });
                    } else {
                        Ext.Msg.alert('Ошибка', result.message);
                    }
                },
                failure: function() {
                    Ext.Msg.alert('Ошибка', 'Не удалось сохранить права');
                }
            });
        }

        {{-- Сбросить индивидуальные права к правам роли --}}
        function resetUserModuleAccess(userId, store) {
            Ext.Ajax.request({
                url: '/admin/permissions/?action=save_user_module_access',
                method: 'POST',
                jsonData: {
                    user_id: userId,
                    modules: {} {{-- Пустой объект сбросит все индивидуальные права --}}
                },
                success: function(response) {
                    var result = Ext.decode(response.responseText);
                    if (result.success) {
                        Ext.Msg.alert('Успешно', 'Индивидуальные права сброшены к правам роли');
                        store.load(); {{-- Перезагрузим данные --}}
                    } else {
                        Ext.Msg.alert('Ошибка', result.message);
                    }
                },
                failure: function() {
                    Ext.Msg.alert('Ошибка', 'Не удалось сбросить права');
                }
            });
        }
}
</script>