<?php

ini_set('max_execution_time', 300);

// include classes
spl_autoload_register(function ($class_name) {
    include $class_name.'.php';
});


/**
 * Создание необходимой таблицы в БД
 *
 * @throws Cann't create bew table in DB Невозможно создать таблицу в БД
 */
try
{
	$result = DB::set("CREATE TABLE IF NOT EXISTS `xml_test` (
															  `id` INT(11) PRIMARY KEY AUTO_INCREMENT,
															  `name` VARCHAR(250) NOT NULL DEFAULT '',
															  `code` VARCHAR(50)  NOT NULL DEFAULT '',
															  `weight` INT(11) NOT NULL DEFAULT '0',
															  `usage` TEXT NULL
															)"
					);

	if (!$result) throw new Exception("Невозможно создать таблицу в БД.");
}
catch (Exception $e)
{
	exit('Ошибка! ' . $e->getMessage());
}

/**
 * Импорт и сохранение данных в БД
 *
 */

$xmlFiles = new ScanDir_XML('data');

$loader = new ImportXMLData($xmlFiles->files());

$loader->fetchData();

$loader->saveData();

echo "Success! <br> Total time = ".$loader->totalTime();

exit();
