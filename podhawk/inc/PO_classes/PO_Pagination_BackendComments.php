<?php

class PO_Pagination_BackendComments extends PO_Pagination
{

	public $sorting; // instance of PO_Sorting
	protected $URLPageToken = 'nr';
	protected $baseURL = "index.php?page=comments";
	private $editing = FALSE;

	public function __construct()
	{
		$this->sorting = new PO_Sorting_BackendComments();

		if (isset($_GET['subpage']) && $_GET['subpage'] == 'edit' && isset($_GET['edit_id']))
		{
			$this->editing = TRUE;
		}
		
		parent::__construct();

		if (empty($this->rowsForCurrentPage))
		{
			$this->message = 'no_comments';
		}

	}

	protected function findRowsPerPage()
	{
		return $this->reg->findSetting('showpostings');
	}

	protected function getTable()
	{
		return DB_PREFIX . 'lb_comments, ' . DB_PREFIX . 'lb_postings';
	}

	protected function buildWhereString()
	{
		// single comment for deletion or editing
		if ($this->editing == TRUE)
		{
			$string = " WHERE " . DB_PREFIX . "lb_comments.id = :id AND (" . DB_PREFIX . "lb_comments.posting_id = " . DB_PREFIX ."lb_postings.id)";

			$this->preparedStatementArray[':id'] = $_GET['edit_id'];
		}
		else
		{
			$string = " WHERE (" . DB_PREFIX . "lb_comments.posting_id = " . DB_PREFIX . "lb_postings.id)";
	 
			if (isset($_GET['posting_id'])) // request for comments for a single post
			{
				$string .= " AND posting_id = :id";
				$this->preparedStatementArray[':id'] = $_GET['posting_id'];
				$this->message = 'found_comments';
			}
		}
		return $string;
	}

	protected function buildOrderString()
	{
		if ($this->editing == TRUE)
		{
			return '';
		}
		else
		{
			$sortby = $this->sorting->getSortField();
			$order = $this->sorting->getSortOrder();
		
			return " ORDER BY {$sortby} {$order}";
		}
	}

	protected function buildLimitString()
	{
		if ($this->editing == TRUE)
		{
			return '';
		}
		else
		{
			return " LIMIT {$this->rowsPerPage} OFFSET {$this->getOffset()}";
		}
	}


	protected function findRequiredCols()
	{
		return 	DB_PREFIX . 'lb_comments.*, '
				. DB_PREFIX . "lb_postings.title AS posting_title, "
				. DB_PREFIX . "lb_postings.author_id AS author_id";
	}

	protected function buildPageURL($page)
	{
		$url = parent::buildPageURL($page);

		$j = (strpos($url, '?') !== false) ? '&amp;' : '?'; // does the URL already have a query string?

		if (isset($_REQUEST['posting_id']) || isset($_REQUEST['posting_id_revert']))
		{
			$posting_id = (isset($_REQUEST['posting_id'])) ? $_REQUEST['posting_id'] : $_REQUEST['posting_id_revert'];
			$url .= $j . "posting_id=$posting_id";
		}
		if (isset($_REQUEST['sort']) || isset($_REQUEST['sort_revert']))
		{
			$sort = (isset($_REQUEST['sort'])) ? $_REQUEST['sort'] : $_REQUEST['sort_revert'];
			$url .= $j . "sort=$sort";
		}

		return $url;
	}

	protected function getHandler()
	{
		return false;
	}
}
?>
