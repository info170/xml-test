# xml-test

<b>Порядок работы:</b>
<li>Скачать репозиторий
<li>В папку /data добавить файлы данных offersX_Y.xml и importX_Y.xml, где X-индекс города, Y-порядковый номер
<li>Настроить доступ к БД в файле DB.php - отредактировать имя БД, адрес БД, имя, пароль
<pre>
  Пример:
	public static $dsn = 'mysql:dbname=test;host=127.0.0.1:3306';
	public static $user = 'root';
	public static $pass = '';
</pre>
<li>Запустить import.php для импорта данных в БД
<li>index.php для постраничного вывода данных в браузере
