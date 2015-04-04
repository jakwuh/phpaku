Всего 3 простых шага для того, чтобы запустить сайт на локальном сервере:
<br>
- Скопировать проект в любую папку на локальном сервере
```
    git clone https://github.com/akwuh/test.git
```
- Импортировать БД `test.sql` из папки `sql\`
<br>
- При необходимости изменить следующие настройки в файле конфигураций `config\config.yml`:
```
db_host: localhost
db_name: test
db_user: root
db_password: 
```
<hr>
Готово!