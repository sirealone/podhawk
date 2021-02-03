<?php

class PO_Pagination_Calendar extends PO_Pagination_Webpage
{
	private $observeCategories = false;
	private $observeTags = false;
	private $observeAuthors = false;
	private $start;
	private $end;
	

	public function __construct($start, $end, $cats=false, $tags=false, $authors=false)
	{
		$this->start = $start;
		$this->end = $end;

		$this->observeCategories = $cats;
		$this->observeTags = $tags;
		$this->observeAuthors = $authors;

		parent::__construct();
	}

	public function getRows()
	{
		return $this->rowsForCurrentPage;
	}

	protected function buildWhereString()
	{
		$elements[] = $this->getDate();
		
		if ($this->observeCategories && isset($_GET['cat']))
		{
			$cat = $this->getCat();
			if (!empty($cat))
			{
				$elements[] = $cat;
			}
		}
		
		if ($this->observeTags && isset($_GET['tag']))
		{
			$elements[] = $this->getTag();
		}

		if ($this->observeAuthors && isset($_GET['author']))
		{
			$elements[] = $this->getAuthor();
		}

		$elements[] = $this->getStatus();

		$return = " WHERE " . implode(" AND ", $elements);

		return $return;
	}

	protected function buildOrderString()
	{
		return '';
	}

	protected function buildLimitString()
	{
		return '';
	}

	protected function findRequiredColumns()
	{
		return 'title, posted';
	}

	protected function getDate()
	{
		$return = "posted >= :start AND posted <= :end";

		$this->preparedStatementArray[':start'] = $this->start;
		$this->preparedStatementArray[':end'] = $this->end;

		return $return;
	}
	
}
?>
