<?php

class PO_Pagination_Feed extends PO_Pagination_Webpage

{
	protected function findRowsPerPage()
	{
		return $this->reg->findSetting('rss_postings');
	}

	public function lastUpdate()
	{
		// $this->rowsForCurrentPage is ordered by posting time
		$rows = $this->rowsForCurrentPage;

		reset ($rows);

		$latestPosting = current($rows);

		$date = strtotime($latestPosting['posted']);

		return date('r', $date);
	}

	protected function buildOrderString()
	{
		if (isset($_GET['id']))
		{
			return '';
		}
		else
		{
			return " ORDER BY posted DESC";
		}
	}
}
?>
