<?php

Class ImportXMLData
{


	private $xmlFiles = [];
	private $data = [];
	private $startTime;
	private $cities = [];

	/**
	 * Выбирает из папки файлы XML
	 * @param string $dir
	 * @return array $xmlFiles
	 * @throws Directory not exists Папка не существует
	 */
	public function __construct($files)
	{
		$this->xmlFiles = $files;
	}

	public function fetchData()
	{
		$this->startTime = time();

		foreach ($this->xmlFiles as $file)
		{
			preg_match("/(import|offers)(\d+_\d+)/",$file,$fileParts); //$fileParts[1]=import|offers, $fileParts[2]=2_1
			list($city_n, $number) = explode("_",$fileParts[2]);
			if (!in_array($city_n,$this->cities)) $this->cities[] = $city_n;

			$xml = new XMLReader;
			$xml->open('data/'.$file);

			switch ($fileParts[1])
			{
				case ('import') :

					// while ($xml->read() && $xml->name !== 'Наименование');
					// preg_match("/\(([^)]+)\)/", $xml->readInnerXML(), $m);
					// $city = $m[1]; echo $city;

					while ($xml->read() && $xml->name !== 'Товар');

					while ($xml->name === 'Товар')
					{
					    $node = new SimpleXMLElement($xml->readOuterXML());

					    #echo "<pre>";var_dump($node); echo "</pre>";

					    $code = (string)$node->Код;

					    $vz1 = [];

					    foreach ($node->Взаимозаменяемости->Взаимозаменяемость as $vz)
					    {
						   $vz1[] = implode(" - ",(array)$vz);
						}

						$vzz = implode(" | ",$vz1);

					    #$vzz = 0;

						$this->data[$code]['name'] = (string)$node->Наименование;
						$this->data[$code]['weight'] = (int)$node->Вес;
						$this->data[$code]['usage'] = $vzz;

					    $xml->next('Товар');
					}

					break;

				case ('offers') :

					while ($xml->read() && $xml->name !== 'Предложение');

					while ($xml->name === 'Предложение')
					{
					    $node = new SimpleXMLElement($xml->readOuterXML());

					    #echo "<pre>";var_dump($node); echo "</pre>";

					    $code = (string)$node->Код;
						$this->data[$code]['quantity_'.$city_n] = (int)$node->Количество;
						$this->data[$code]['price_'.$city_n] = (int)$node->Цены->Цена->ЦенаЗаЕдиницу;

					    $xml->next('Предложение');
					}

					break;
			}

			#echo "<pre>"; var_dump($this->data); echo "</pre>";
			#die();
		}

	}

	public function addColumns()
	{
		foreach ($this->cities as $city_n)
		{
			$result1 = DB::set("SHOW COLUMNS FROM `xml_test` LIKE 'quantity_'".$city_n);
			if (!$result1) $result = DB::set("ALTER TABLE `xml_test` ADD COLUMN `quantity_".$city_n."` INT(11) NOT NULL DEFAULT '0'");

			$result2 = DB::set("SHOW COLUMNS FROM `xml_test` LIKE 'price_'".$city_n);
			if (!$result2) $result = DB::set("ALTER TABLE `xml_test` ADD COLUMN `price_".$city_n."` INT(11) NOT NULL DEFAULT '0'");
		}
	}

	public function saveData()
	{
		$this->addColumns();

		foreach ($this->data as $code=>$data)
		{
			#echo "<br>".$code;
			#echo "<pre>"; var_dump($data); echo "</pre>";

				$result = DB::getRow("SELECT * FROM `xml_test` WHERE `code` = ?", $code);

				if ($result)
				{
					$item = DB::set("UPDATE `xml_test` SET `name` = ".$data['code'].", `weight` = ".$data['weight']." WHERE `code` = ".$code." LIMIT 1");
				}
				else
				{
					$query1 = $query2 = '';
					foreach ($this->cities as $city_n)
					{
						if (!empty($data['quantity_'.$city_n]))
						{
							$query1 .= ",`quantity_".$city_n."`";
							$query2 .= ",".$data['quantity_'.$city_n];
						}
						if (!empty($data['price_'.$city_n]))
						{
							$query1 .= ",`price_".$city_n."`";
							$query2 .= ",".$data['price_'.$city_n];
						}
					}

					$sql = "INSERT INTO `xml_test` (`name`,`code`,`weight`,`usage`".$query1.") VALUES ('".$data['name']."','".$code."',".$data['weight'].",'".$data['usage']."'".$query2.")";
					#echo "<br>".$sql;
					$item = DB::set($sql);
				}


		}
	}

	public function totalTime()
	{
		return gmdate("H:i:s", time() - $this->startTime);
	}
}
