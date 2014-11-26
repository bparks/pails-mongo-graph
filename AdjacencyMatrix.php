<?php
namespace MongoGraph;

class AdjacencyMatrix
{
	private $collection_name;

	public function __construct($collection)
	{
		$this->collection_name = $collection;
	}

	public /* bool */ function areAdjacent($source, $target)
	{
		if ($source == $target) return true;
		if ($source > $target) return $this->areAdjacent($target, $source);

		$collection = $this->getMongoCollection();
		$doc = $collection->findOne(array('_id' => $source));
		return in_array($target, $doc[$this->collection_name]);
	}

	public /* array */function getAdjacents($source)
	{
		$collection = $this->getMongoCollection();
		$doc = $collection->findOne(array('_id' => $source));
		$adjacents = (isset($doc[$this->collection_name])) ? $doc[$this->collection_name] : array();

		$others = $collection->find(
			array('_id' => array('$lt' => $source), $this->collection_name => $source),
			array('_id' => 1)
			);

		return array_merge($adjacents, array_map(function ($item) { return $item['_id']; }, array_values(iterator_to_array($others))));
	}

	public /* void */ function setAdjacent($source, $target)
	{
		if ($source == $target) return;
		if ($source > $target)
		{
			$this->setAdjacent($target, $source);
			return;
		}

		$collection = $this->getMongoCollection();
		$doc = $collection->update(
			array('_id' => $source),
			array('$addToSet' => array($this->collection_name => $target)),
			array('upsert' => true));
		return;
	}

	private /* resource */ function getMongoCollection()
	{
		$connection = new \MongoClient(Config::$opts['connection_string']);
		return $connection->top_of_mind->{$this->collection_name};
	}
}