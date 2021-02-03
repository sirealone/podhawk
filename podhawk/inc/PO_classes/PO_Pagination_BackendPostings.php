<?php

class PO_Pagination_BackendPostings extends PO_Pagination
{
	private $like = 'LIKE'; // for db queries
	public $sorting; // instance of PO_Sorting
	protected $URLPageToken = 'nr';
	protected $baseURL = 'index.php?page=postings';

	public function __construct()
	{
		$this->sorting = new PO_Sorting_BackendPostings();
		
		parent::__construct();
	}

	public function getRows()
	{
		if (empty($this->rowsForCurrentPage))
		// no point in remembering search criteria if they have returned no results
		{
			unset ($_SESSION['find_criterion']);
			unset ($_SESSION['prepared_statement_array']);
		}
		
		return $this->rowsForCurrentPage;
	}

	public function getSortField()
	{
		return $this->sorting->getSortField();
	}

	protected function findRowsPerPage()
	{
		return $this->reg->findSetting('showpostings');
	}

	protected function getTable()
	{
		return DB_PREFIX . "lb_postings";
	}

	protected function buildWhereString()
	{
		$string = '';

		//postgres 'ILIKE' is needed for case insensitive searches
		$this->like = (DB_TYPE == 'postgres8' || DB_TYPE == 'postgres7') ? 'ILIKE' : 'LIKE';

		if (isset($_POST['month']) && isset($_POST['year']))
		{
			$string = $this->getDate();
		}
		elseif (isset($_POST['author']))
		{
			$string = $this->getAuthor();
		}
		elseif (isset($_POST['cat']))
		{
			$string = $this->getCat($_POST['cat']);
		}
		elseif (isset($_POST['tag']))
		{
			$string = $this->getTag();
		}
		elseif (isset($_POST['title1']))
		{
			$string = $this->getTitle1();
		}
		elseif (isset($_POST['title2']))
		{
			$string = $this->getTitle2();
		}
		
		if (!empty($string)) // store the where string in a session variable
		{
			$string 								= " WHERE $string";
			$_SESSION['find_criterion'] 			= $string;
			$_SESSION['prepared_statement_array'] 	= $this->preparedStatementArray;
			$this->message							= 'found_posts';
		}
		elseif (!empty($_SESSION['find_criterion'])) // else try to retrieve the where string from session variable
		{
			$this->message 					= "found_posts";
			$string 						= $_SESSION['find_criterion'];
			$this->preparedStatementArray 	= $_SESSION['prepared_statement_array'];
		}
		return $string;
	}

	protected function buildOrderString()
	{
		$sortby = $this->sorting->getSortField();
		$order = $this->sorting->getSortOrder();
		
		return " ORDER BY sticky {$order}, {$sortby} {$order}";
	}

	protected function buildLimitString()
	{
		return " LIMIT {$this->rowsPerPage} OFFSET {$this->getOffset()}";
	}

	protected function findRequiredCols()
	{
		return "id, author_id, posted, title, filelocal, audio_file, audio_type, audio_size, audio_length, status, sticky";
	}

	protected function getHandler() // $_GET does not affect which page we are on
	{
		return false;
	}

	private function getDate()
	{
		$lastDay = mktime(0, 0, 0, $_POST['month'] + 1, 0, $_POST['year']);
		$maxDays = date('d', $lastDay);

		$this->preparedStatementArray[':start']	= "{$_POST['year']}-{$_POST['month']}-01 00:00:00";
		$this->preparedStatementArray[':end']	= "{$_POST['year']}-{$_POST['month']}-$maxDays 23:59:59";

		return "posted >= :start AND posted <= :end";
	}

	private function getAuthor()
	{
		$this->preparedStatementArray[':author_id'] = $_POST['author'];
		return 'author_id = :author_id';
	}

	private function getTag()
	{
		$tag = '%' . entity_encode($_POST['tag']) . '%';

		$this->preparedStatementArray[':tag'] = $tag;

		return "tags {$this->like} :tag";
	}

	private function getTitle1()
	{
		$this->preparedStatementArray[':title'] = entity_encode($_POST['title1']) . '%';
		
		return "title {$this->like} :title";
	}

	private function getTitle2()
	{
		$this->preparedStatementArray[':title'] = '%' . entity_encode($_POST['title2']) . '%';

		return "title {$this->like} :title";
	}

}
?>
