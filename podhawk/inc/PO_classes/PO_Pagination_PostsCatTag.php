<?php

class PO_Pagination_PostsCatTag extends PO_Pagination
{
	private $searchType; // a search for a category ('cat') or a tag ('tag')?
	private $searchName; // the name of the category or tag we are looking for
	private $categoryFound = true; // have we found a category called $searchName?

	function __construct($type, $name, $alpha)
	{
		$this->searchType = $type;
		$this->searchName = $name;	

		$this->reg = Registry::instance();

		$this->log = LO_ErrorLog::instance();

		$this->table = $this->getTable();

		$this->whereString = $this->buildWhereString();

		$this->orderString = ($alpha) ? ' ORDER BY title ASC' : ' ORDER BY posted DESC';

		$this->requiredCols = $this->findRequiredCols();

		$this->rowsForCurrentPage = $this->findRowsForCurrentPage();			
	}	
	
	protected function findRowsPerPage()
	{
		// not needed
	}
		
	protected function getTable()
	{
		return DB_PREFIX . 'lb_postings';
	}
	
	protected function buildWhereString()
	{
		if ($this->searchType == 'cat')
		{
			$catID = $this->reg->getCategoryID($this->searchName);

			if (empty($catID)) // if no category $searchName exists, we want an empty return, not a return of all postings
			{
				$this->categoryFound = false;
				return '';
			}

			$return = $this->getCat($catID);
		}
		elseif ($this->searchType == 'tag')
		{
			$tagsToShow = explode('+', $this->searchName);

			$tagSQL = array();

			$i = 1;
			foreach ($tagsToShow as $tagToShow)
			{
				$tagToShow = entity_encode($tagToShow);
				$tagSQL[] = "tags LIKE :tag_$i";
				$this->preparedStatementArray[":tag_$i"] = '%' . $tagToShow . '%';
				$i++;
			}

			$return = '(' . implode(' OR ', $tagSQL) . ')';
		}
		return " WHERE " . $return . " AND status = '3' AND posted < '" . date("Y-m-d H:i:s") . "'";
	}
	
	protected function buildOrderString()
	{
		// not needed
	}
	
	protected function buildLimitString()
	{
		//not needed
	}
	
	protected function findRequiredCols()
	{
		return 'id, title';
	}

	protected function findRowsForCurrentPage()
	{
		if ($this->searchType == 'cat' && $this->categoryFound == false)
		{
			return array();
		}
		else
		{
			$result = parent::findRowsForCurrentPage();
			return $result;			
		}
	}
}
?>
