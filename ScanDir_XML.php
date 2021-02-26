<?php

Class ScanDir_XML
{
	private $xmlFiles=[];

	/**
	 * Выбирает из папки файлы XML
	 * @param string $dir
	 * @return array $xmlFiles
	 * @throws Directory not exists Папка не существует
	 */
	public function __construct($dir)
	{
		try
		{
			$dir_contents = scandir($dir);
			if (!$dir_contents) throw new Exception("Папка $dir не существует.");

			foreach($dir_contents as $dir_content)
			{
				if (!is_dir($dir_content) and preg_match("/(\.xml)$/i", $dir_content))
				{
					$this->xmlFiles[] = $dir_content;
				}
			}

		}
		catch (Exception $e)
		{
		    die('Ошибка! '.$e->getMessage());
		}
	}

	public function files()
	{
		return $this->xmlFiles;
	}
}
