<?php
namespace MongoGraph;

require_once (dirname(__FILE__) . '/AdjacencyMatrix.php');

class Config
{
	public static $opts;

	public static function init($opts)
	{
		self::$opts = array_merge(array(
			'connection_string' => 'mongo://localhost'
		), $opts);
	}
}