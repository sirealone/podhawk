<?php

class PO_Pagination_LastPostings extends PO_Pagination
{
	private $categoryID = 0;
	private $dateRange = '';

	public function __construct ($number, $alpha = false, $cat = NULL, $dateRange='')
	{
		$this->reg = Registry::instance();

		$this->rowsPerPage = $number;

		$this->dateRange = $dateRange;

		if ($cat)
		{
			$this->categoryID = $this->reg->getCategoryID($cat);
		}
		
		$this->table = $this->getTable();

		$this->requiredCols = $this->findRequiredCols();

		$this->orderString = ($alpha) ? ' ORDER BY title ASC' : ' ORDER BY posted DESC';

		$this->whereString = $this->buildWhereString();

		$this->limitString = $this->buildLimitString();

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
	
		
		$string = " WHERE status = '3' AND posted < '" . date("Y-m-d H:i:s") . "'";
		
		if (!empty($this->dateRange))
		{
			$dateString = $this->getDate();
			$string .= ' AND ' . $dateString;
		}

		if ($this->categoryID)
		{
			$catString = $this->getCat($this->categoryID);
			$string .= ' AND ' . $catString;
		}
		return $string;		
	}

	protected function buildOrderString()
	{
		// not needed
	}

	protected function buildLimitString()
	{
		return " LIMIT {$this->rowsPerPage}";
	}

	protected function findRequiredCols()
	{
		return 'id, author_id, title, posted, tags, summary, message_html, image';
	}

	protected function getDate()
	{
		$date = $this->dateRange;
		$return = '';

		$d = $this->buildDateQuery($date);

		if (isset($d['from']) && isset($d['to']))
		{
			$this->preparedStatementArray[':from'] = $d['from'];
			$this->preparedStatementArray[':to']  = $d['to'];
			$return = "posted >= :from AND posted <= :to";
		}
		return $return;
	}
}
?>
