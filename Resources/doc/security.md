Security
========

ThraceDataGrid uses ajax post requests to get, put and delete data from the server.

- Data url (used to fetch data): */_thrace-datagrid/data/{name}*  
- Row action url (used to add/edit/delete): */_thrace-datagrid/row-action/{name}* 
- Mass action url (used for mass actions) */_thrace-datagrid/mass-action/{name}*
- Sortable url (used to sort rows) */_thrace-datagrid/sortable/{name}*

**Note:** {name} parameter is datagrid name

### Securing Specific URL Patterns

``` yaml
# app/config/security.yml
security:
    # ...
    access_control:
        - { path: ^/_thrace-datagrid/data/your-grid-name, roles: ROLE_ADMIN }
        - { path: ^/_thrace-datagrid/row-action/your-grid-name, roles: ROLE_ADMIN }
        - { path: ^/_thrace-datagrid/mass-action/your-grid-name, roles: ROLE_ADMIN }
        - { path: ^/_thrace-datagrid/sortable/your-grid-name, roles: ROLE_ADMIN }
```

**Note:** This way is very flexible but you have to secure every url.

Or you can secure *^/admin* section and then prefix the bundle routing.

``` yaml
# app/config/routing.yml

thrace_data_grid:
    resource: "@ThraceDataGridBundle/Resources/config/routing.xml"
    prefix:   /admin

```
**Note:** This way is convinient if you do not need to apply specific acl on every datagrid.

[For more information go to symfony documentation](http://symfony.com/doc/current/book/security.html#securing-specific-url-patterns)

[back to home](index.md)

