<?php

abstract class PO_Pagination
{

	protected $currentPage = 1;
	protected $reg; // instance of Registry
	protected $rowsPerPage = 0;
	protected $whereString = '';
	protected $limitString = '';
	protected $orderString = '';
	protected $totalRows = 0;
	protected $rowsForCurrentPage = array();
	protected $paginationString = '';
	protected $preparedStatementArray = array();
	protected $table = ''; // the DB table we want to query
	protected $requiredCols = ''; // the column or columns we want to return
	protected $log; // instance of logWriter
	protected $URLPageToken = 'page'; // how URL or POST data indicates pages
	protected $baseURL = 'index.php'; // the base for building links to other pages
	protected $message = ''; // message to display on screen

	public function __construct()
	{
		$this->reg = Registry::instance();

		$this->log = LO_ErrorLog::instance();

		$this->currentPage = $this->findCurrentPage();

		$this->rowsPerPage = $this->findRowsPerPage();

		$this->table = $this->getTable();

		$this->whereString = $this->buildWhereString();

		$this->totalRows = $this->countRows();

		$this->totalPages = ceil($this->totalRows/$this->rowsPerPage);

		$this->orderString = $this->buildOrderString();

		$this->limitString = $this->buildLimitString();

		$this->requiredCols = $this->findRequiredCols();

		$this->rowsForCurrentPage = $this->findRowsForCurrentPage();		

	}	
	
	abstract protected function findRowsPerPage();
		
	abstract protected function getTable();
	
	abstract protected function buildWhereString();
	
	abstract protected function buildOrderString();
	
	abstract protected function buildLimitString();
	
	abstract protected function findRequiredCols();

	public function getRows()
	{
		return $this->rowsForCurrentPage;
	}

	public function getCurrentPage()
	{
		return $this->currentPage;
	}

	public function getNextPage()
	{
		$maxRow = $this->rowsPerPage * $this->currentPage;

		if ($maxRow < $this->totalRows)
		{
			return $this->currentPage + 1;
		}
		else
		{
			return false;
		}
	}

	public function getPreviousPage()
	{
		if ($this->currentPage > 1)
		{
			return $this->currentPage - 1;
		}
		else
		{
			return false;
		}
	}

	public function getNextPageURL()
	{
		$thePage = $this->getNextPage();

		return $this->buildPageURL($thePage);
	}

	public function getPreviousPageURL()
	{
		$thePage = $this->getPreviouspage();

		return $this->buildPageURL($thePage);
	}

	public function getBaseURL()
	{
		return $this->baseURL;
	}

	public function getPaginationString()
	{
		$this->makePaginationString();
		
		return $this->paginationString;
	}

	public function getMessage()
	{
		return $this->message;
	}

	protected function getCat($cat)
	{
		$this->preparedStatementArray[':cat1'] = $cat;
		$this->preparedStatementArray[':cat2'] = $cat;
		$this->preparedStatementArray[':cat3'] = $cat;
		$this->preparedStatementArray[':cat4'] = $cat;

		return '(category1_id = :cat1 OR category2_id = :cat2 OR category3_id = :cat3 OR category4_id = :cat4)';
	}

	protected function countRows()
	{
		try
		{
			$return = $this->getHandler();

			if(!$return)
			{
				$dosql = "SELECT COUNT(*) FROM " . $this->table . $this->whereString;

				$GLOBALS['lbdata']->prepareStatement($dosql);

				$row = $GLOBALS['lbdata']->executePreparedStatement($this->preparedStatementArray);
		
				$return = (isset($row[0]['COUNT(*)'])) ? $row[0]['COUNT(*)'] : $row[0]['count']; //needed for postgres
			}
			return $return;
		}
		catch (Exception $e)
		{
			$class = get_class($this);
			$this->log->write("Error in executing 'countRows()' method in object of class $class. Returned error message was {$e->getMessage()}.");
			$this->message = 'dberror';
			return false;
		}
	}

	protected function getHandler() // $_GET['id'] indicates a single page
	{
		if (isset($_GET['id'])) return 1;

		else return false;
	}

	protected function findRowsForCurrentPage()
	{
		try
		{
			$dosql = "SELECT {$this->requiredCols} FROM " . $this->table . $this->whereString . $this->orderString . $this->limitString;

			$GLOBALS['lbdata']->prepareStatement($dosql);

			$result = $GLOBALS['lbdata']->executePreparedStatement($this->preparedStatementArray);

			return $result;
		}
		catch (Exception $e)
		{
			$class = get_class($this);
			$this->log->write("Error in executing 'findRowsForCurrentPage()' method in object of class $class. Returned error message was {$e->getMessage()}.");
			$this->message = 'dberror';
			return false;
		}
	}


	protected function findCurrentPage()
	{
		if (isset($_REQUEST[$this->URLPageToken]))
		{
			if (!ctype_digit($_REQUEST[$this->URLPageToken]) || $_REQUEST[$this->URLPageToken] == 0)
			{
				return 1;
			}
			else
			{
				return $_REQUEST[$this->URLPageToken];
			}
		}
		else
		{
			return 1;
		}
	}

	protected function getOffset()
	{
		return $this->rowsPerPage * ($this->currentPage - 1);
	}

	protected function makePaginationString()
	{
		$trans = new TR_TranslationWebpage($this->reg->findSetting('template'));

		$transPages = $trans->getTrans('pages');

		$string ="<span class=\"pages\">$transPages : ";

		// calculate the central links around current page
		if ($this->currentPage >= 3)
		{
			$start = $this->currentPage - 2;
		}
		elseif ($this->currentPage == 2)
		{
			$start = $this->currentPage - 1;
		}
		else
		{
			$start = $this->currentPage;
		}

		if ($this->totalPages >= $start + 4)
		{
			$end = $start + 4;
		}
		else
		{
			$end = $this->totalPages;
			$start = $end-4;
			if ($start < 1)
			{
				$start = 1;
			}
		}

		// link to first page
		if ($start > 1)
		{
			$string .= $this->makePaginationLink(1, '[' . $trans->getTrans('first') . ']') .'...';
		}

		$i = 10;
		
		// links to every 10th page
		while ($i < $start)
		{
			$string .= $this->makePaginationLink($i, "[$i]") . '...';

			$i = $i+10;
		}
	
		$ii = $start;

		$string = '&nbsp;' . $string;

		while ($ii <= $end)
		{
			if ($ii == $this->currentPage)
			{
				$string .= "<span class=\"active\">$ii </span>";
			}
			else
			{
				$string .= $this->makePaginationLink($ii, "$ii") . '&nbsp;';
			}
			$ii++;
		}

		// remove any white space at the end of the pagination string
		$string = trim($string);

		// increment the tens counter if necessary
		if ($i <= $end) $i = $i+10;

		while ($i < $this->totalPages)
		{
			$string .= '...' . $this->makePaginationLink($i, "[$i]");
			$i = $i+10;
		}

		if ($this->totalPages > $end)
		{
			$string .= '...' . $this->makePaginationLink($this->totalPages, '[' . $trans->getTrans('last') . ']');
		}

		$string .= "</span>";

		$this->paginationString = $string;

	}

	protected function buildPageURL($page)
	{
		if (ACTION == 'webpage')
		{
			$url = ($page) ? $this->baseURL . addToUrl($this->URLPageToken, $page) : '';
		}
		else
		{
			$j = (strpos($this->baseURL, '?') !== false) ? '&amp;' : '?'; // does the base URL already have a query string?
			$url = ($page) ? $this->baseURL . $j . $this->URLPageToken . "=$page" : $this->baseURL;
		}

		return $url;
	}

	protected function makePaginationLink($page, $text)
	{
		$url = $this->buildPageURL($page);

		$return = "<a href=\"" . $url . "\">$text</a>";
		return $return;
	}

	protected function buildDateQuery($date)
	{
		$return = array();

		if (empty($date)) return $return;		
		
		switch (strlen($date))
		{
        	case 4:
            //show us a year!
            $return['from'] = $date . "-01-01 00:00:00";
            $return['to']   = $date . "-12-31 23:59:59";            
			break;

        	case 7:
            //show us a month!
			//postgres will not allow dates like "31 Feb"
			//so we need to calculate the last day of the month
			$year 		= substr($date,0,4);
			$month 		= substr($date,5,2);
			$lastDay 	= mktime(0, 0, 0, $month + 1, 0, $year);
			$maxDays 	= date('d', $lastDay);

            $return['from'] = $date . "-01 00:00:00";
            $return['to']   = $date . "-".$maxDays." 23:59:59";
            break;

        	case 10:
            //show us a day!
            $return['from'] = $date . " 00:00:00";
            $return['to']   = $date . " 23:59:59";
            break;

			default:
			$this->log->write("Malformed date component {$_GET['date']} in URL query string");
			break;

        }
		return $return;
	}
		
}
?>
