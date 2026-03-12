<script>
{{-- Управление пользователями --}}

function openUsers() {
        closeStartMenu();
        
        if (openWindows['users']) {
            openWindows['users'].show();
            openWindows['users'].toFront();
            return;
        }
        
        Ext.onReady(function() {
            var store = Ext.create('Ext.data.Store', {
                fields: ['id', 'login', 'name', 'email', 'role', 'active', 'created_at'],
                proxy: {
                    type: 'ajax',
                    url: '/admin/users/?action=list',
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
                title: 'Управление пользователями',
                width: 1000,
                height: 600,
                layout: 'fit',
                maximizable: true,
                minimizable: true,
                constrain: true,
                x: 120,
                y: 70,
                listeners: {
                    close: function() {
                        delete openWindows['users'];
                        removeTaskbarButton('users');
                    },
                    activate: function() {
                        setActiveWindow('users');
                    },
                    minimize: function() {
                        this.hide();
                        var btn = document.getElementById('taskbar-users');
                        if (btn) {
                            btn.classList.add('minimized');
                            btn.classList.remove('active');
                        }
                    }
                },
                items: [{
                    xtype: 'grid',
                    store: store,
                    columns: [
                        { text: 'ID', dataIndex: 'id', width: 50 },
                        { text: 'Логин', dataIndex: 'login', width: 120 },
                        { text: 'Имя', dataIndex: 'name', flex: 1 },
                        { text: 'Email', dataIndex: 'email', flex: 1 },
                        { 
                            text: 'Роль', 
                            dataIndex: 'role', 
                            width: 100,
                            renderer: function(value) {
                                var roleNames = {
                                    'admin': 'Администратор',
                                    'editor': 'Редактор',
                                    'moderator': 'Модератор',
                                    'user': 'Пользователь'
                                };
                                return roleNames[value] || value;
                            }
                        },
                        { 
                            text: 'Статус', 
                            dataIndex: 'active', 
                            width: 80,
                            align: 'center',
                            renderer: function(value) {
                                return value == 1 
                                    ? '<span style="color: green; font-weight: bold;">Активен</span>' 
                                    : '<span style="color: red; font-weight: bold;">Заблокирован</span>';
                            }
                        },
                        { 
                            text: 'Создан', 
                            dataIndex: 'created_at', 
                            width: 150,
                            renderer: function(value) {
                                if (!value) return '';
                                var date = new Date(value);
                                return Ext.Date.format(date, 'd.m.Y H:i');
                            }
                        }
                    ],
                    listeners: {
                        itemdblclick: function(grid, record) {
                            showUserForm(record, store);
                        }
                    },
                    tbar: [
                        {
                            text: 'Добавить',
                            iconCls: 'add-icon',
                            handler: function() {
                                showUserForm(null, store);
                            }
                        },
                        {
                            text: 'Редактировать',
                            iconCls: 'edit-icon',
                            handler: function() {
                                var selection = this.up('grid').getSelectionModel().getSelection();
                                if (selection.length > 0) {
                                    showUserForm(selection[0], store);
                                } else {
                                    Ext.Msg.alert('Внимание', 'Выберите пользователя');
                                }
                            }
                        },
                        {
                            text: 'Сменить пароль',
                            iconCls: 'key-icon',
                            handler: function() {
                                var selection = this.up('grid').getSelectionModel().getSelection();
                                if (selection.length > 0) {
                                    showPasswordForm(selection[0], store);
                                } else {
                                    Ext.Msg.alert('Внимание', 'Выберите пользователя');
                                }
                            }
                        },
                        {
                            text: 'Активировать/Заблокировать',
                            iconCls: 'lock-icon',
                            handler: function() {
                                var selection = this.up('grid').getSelectionModel().getSelection();
                                if (selection.length > 0) {
                                    toggleUserActive(selection[0], store);
                                } else {
                                    Ext.Msg.alert('Внимание', 'Выберите пользователя');
                                }
                            }
                        },
                        '-',
                        {
                            text: 'Удалить',
                            iconCls: 'delete-icon',
                            handler: function() {
                                var selection = this.up('grid').getSelectionModel().getSelection();
                                if (selection.length > 0) {
                                    Ext.Msg.confirm('Подтверждение', 'Вы уверены, что хотите удалить пользователя?', function(btn) {
                                        if (btn === 'yes') {
                                            deleteUser(selection[0], store);
                                        }
                                    });
                                } else {
                                    Ext.Msg.alert('Внимание', 'Выберите пользователя');
                                }
                            }
                        },
                        '->',
                        {
                            text: 'Обновить',
                            iconCls: 'refresh-icon',
                            handler: function() {
                                store.load();
                            }
                        }
                    ]
                }]
            });
            
            openWindows['users'] = win;
            addTaskbarButton('users', 'Пользователи', win);
            win.show();
        });
    }
    
    {{-- Форма добавления/редактирования пользователя --}}
    function showUserForm(record, store) {
        var isEdit = record !== null;
        var formWindow = Ext.create('Ext.window.Window', {
            title: isEdit ? 'Редактировать пользователя' : 'Добавить пользователя',
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
                        xtype: 'textfield',
                        name: 'login',
                        fieldLabel: 'Логин',
                        allowBlank: false,
                        value: isEdit ? record.get('login') : ''
                    },
                    {
                        xtype: 'textfield',
                        name: 'password',
                        fieldLabel: 'Пароль',
                        inputType: 'password',
                        allowBlank: isEdit,
                        emptyText: isEdit ? 'Оставьте пустым, если не хотите менять' : ''
                    },
                    {
                        xtype: 'textfield',
                        name: 'name',
                        fieldLabel: 'Имя',
                        allowBlank: false,
                        value: isEdit ? record.get('name') : ''
                    },
                    {
                        xtype: 'textfield',
                        name: 'email',
                        fieldLabel: 'Email',
                        vtype: 'email',
                        value: isEdit ? record.get('email') : ''
                    },
                    {
                        xtype: 'combobox',
                        name: 'role',
                        fieldLabel: 'Роль',
                        store: [
                            ['admin', 'Администратор'],
                            ['editor', 'Редактор'],
                            ['moderator', 'Модератор'],
                            ['user', 'Пользователь']
                        ],
                        value: isEdit ? record.get('role') : 'user',
                        editable: false
                    },
                    {
                        xtype: 'checkbox',
                        name: 'active',
                        fieldLabel: 'Активен',
                        checked: isEdit ? record.get('active') == 1 : true
                    }
                ],
                buttons: [
                    {
                        text: 'Сохранить',
                        handler: function() {
                            var form = this.up('form').getForm();
                            if (form.isValid()) {
                                var values = form.getValues();
                                
                                var params = {
                                    action: isEdit ? 'edit' : 'add',
                                    login: values.login,
                                    name: values.name,
                                    email: values.email,
                                    role: values.role,
                                    active: values.active ? 1 : 0
                                };
                                
                                if (isEdit) {
                                    params.id = record.get('id');
                                } else {
                                    params.password = values.password;
                                }
                                
                                {{-- Если редактирование и пароль указан - сначала меняем пароль отдельно --}}
                                if (isEdit && values.password) {
                                    Ext.Ajax.request({
                                        url: '/admin/users/',
                                        params: {
                                            action: 'change_password',
                                            id: record.get('id'),
                                            password: values.password
                                        },
                                        success: function(response) {
                                            var result = Ext.decode(response.responseText);
                                            if (!result.success) {
                                                Ext.Msg.alert('Ошибка', result.message);
                                            }
                                        }
                                    });
                                }
                                
                                Ext.Ajax.request({
                                    url: '/admin/users/',
                                    params: params,
                                    success: function(response) {
                                        var result = Ext.decode(response.responseText);
                                        if (result.success) {
                                            store.load();
                                            formWindow.close();
                                        } else {
                                            Ext.Msg.alert('Ошибка', result.message);
                                        }
                                    },
                                    failure: function() {
                                        Ext.Msg.alert('Ошибка', 'Не удалось сохранить данные');
                                    }
                                });
                            }
                        }
                    },
                    {
                        text: 'Отмена',
                        handler: function() {
                            formWindow.close();
                        }
                    }
                ]
            }]
        });
        
        formWindow.show();
    }
    
    {{-- Форма смены пароля --}}
    function showPasswordForm(record, store) {
        var formWindow = Ext.create('Ext.window.Window', {
            title: 'Сменить пароль: ' + record.get('login'),
            width: 400,
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
                        xtype: 'displayfield',
                        fieldLabel: 'Пользователь',
                        value: record.get('name') + ' (' + record.get('login') + ')'
                    },
                    {
                        xtype: 'textfield',
                        name: 'password',
                        fieldLabel: 'Новый пароль',
                        inputType: 'password',
                        allowBlank: false
                    },
                    {
                        xtype: 'textfield',
                        name: 'password_confirm',
                        fieldLabel: 'Подтверждение',
                        inputType: 'password',
                        allowBlank: false
                    }
                ],
                buttons: [
                    {
                        text: 'Сохранить',
                        handler: function() {
                            var form = this.up('form').getForm();
                            if (form.isValid()) {
                                var values = form.getValues();
                                
                                if (values.password !== values.password_confirm) {
                                    Ext.Msg.alert('Ошибка', 'Пароли не совпадают');
                                    return;
                                }
                                
                                Ext.Ajax.request({
                                    url: '/admin/users/',
                                    params: {
                                        action: 'change_password',
                                        id: record.get('id'),
                                        password: values.password
                                    },
                                    success: function(response) {
                                        var result = Ext.decode(response.responseText);
                                        if (result.success) {
                                            Ext.Msg.alert('Успешно', result.message);
                                            formWindow.close();
                                        } else {
                                            Ext.Msg.alert('Ошибка', result.message);
                                        }
                                    },
                                    failure: function() {
                                        Ext.Msg.alert('Ошибка', 'Не удалось изменить пароль');
                                    }
                                });
                            }
                        }
                    },
                    {
                        text: 'Отмена',
                        handler: function() {
                            formWindow.close();
                        }
                    }
                ]
            }]
        });
        
        formWindow.show();
    }
    
    {{-- Активация/блокировка пользователя --}}
    function toggleUserActive(record, store) {
        var newStatus = record.get('active') == 1 ? 0 : 1;
        var action = newStatus == 1 ? 'активировать' : 'заблокировать';
        
        Ext.Msg.confirm('Подтверждение', 'Вы уверены, что хотите ' + action + ' пользователя?', function(btn) {
            if (btn === 'yes') {
                Ext.Ajax.request({
                    url: '/admin/users/',
                    params: {
                        action: 'toggle_active',
                        id: record.get('id'),
                        active: newStatus
                    },
                    success: function(response) {
                        var result = Ext.decode(response.responseText);
                        if (result.success) {
                            store.load();
                        } else {
                            Ext.Msg.alert('Ошибка', result.message);
                        }
                    },
                    failure: function() {
                        Ext.Msg.alert('Ошибка', 'Не удалось изменить статус');
                    }
                });
            }
        });
    }
    
    {{-- Удаление пользователя --}}
    function deleteUser(record, store) {
        Ext.Ajax.request({
            url: '/admin/users/',
            params: {
                action: 'delete',
                id: record.get('id')
            },
            success: function(response) {
                var result = Ext.decode(response.responseText);
                if (result.success) {
                    store.load();
                } else {
                    Ext.Msg.alert('Ошибка', result.message);
                }
            },
            failure: function() {
                Ext.Msg.alert('Ошибка', 'Не удалось удалить пользователя');
            }
        });
    }
</script>