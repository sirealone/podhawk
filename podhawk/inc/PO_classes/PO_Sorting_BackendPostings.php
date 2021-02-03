<?php

class PO_Sorting_BackendPostings extends PO_Sorting
{
	protected $sortby = 'posted'; // the field to sort by
	protected $sortdir = '1'; // code in URL representing sorting direction
	protected $order = 'DESC'; // ASC or DESC 
	protected $tableHeadings = array(); // for constructing links for resorting in table headings

	public function getTableHeadings()
	{
		$this->makeTableHeadings();
		return $this->tableHeadings;
	}

	protected function makeTableHeadings()
	{
		//default values for sorting direction in new url-requests
		$dirpost = "1";
		$dirauth = "0";
		$dirtitl = "0";
		$diraudi = "1";
		$dirstat = "0";
		 
		//reverse the sorting order for the current sorting criterion
		$n = 'dir'.substr($this->sortby,0,4);

		$$n = ($this->sortdir == '1') ? '0' : '1';
		

		//bring together info about sorting direction for the table headings
		$this->tableHeadings = array(	'posted'		=> $dirpost."posted",
										'author' 		=> $dirauth."author_id",
										'title' 		=> $dirtitl."title",
										'audio_length' 	=> $diraudi."audio_length",
										'status' 		=> $dirstat."status"
										);
	}
}
?>
