<?php

ini_set('max_execution_time', 300);

// include classes
spl_autoload_register(function ($class_name) {
    include $class_name.'.php';
});

$page = $_GET['page'] ?? 0;
$per_page = 10;

$prev = ($page - $per_page > 0) ? $page - $per_page : 0;
$next = $page + $per_page;

$cols = DB::getColumn("SHOW COLUMNS FROM `xml_test`");

$items = DB::getAll("SELECT * FROM `xml_test` LIMIT $page,$per_page");

echo "<a href='index.php?page=".$prev."'><<<</a> <a href='index.php?page=".$next."'>>>></a>";

echo "<table border=1 cellspacing=0 cellpadding=3>";

echo "<tr align='center'>";

foreach ($cols as $col)
{
	echo "<th>".$col."</th>";
}

echo "</tr>";

foreach ($items as $item)
{

	echo "<tr align='center'>";

	foreach ($cols as $col)
	{

		echo "<td>".$item[$col]."</td>";
	}

	echo "</tr>";

}

echo "</table>";