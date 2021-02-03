<?php

class PO_Sorting_BackendComments extends PO_Sorting_BackendPostings
{
	protected function makeTableHeadings()
	{
		//default values for sorting direction in new url-requests
		$dirpost = "1";
		$dirname = "0";
		$dirmess = "0";
		$dirbelo = "1";
		$diraudi = "0";
		 
		//reverse the sorting order for the current sorting criterion		
		$n = ($this->sortby == 'posting_id') ? 'dirbelo' : 'dir'.substr($this->sortby,0,4);

		$$n = ($this->sortdir == '1') ? '0' : '1';
		

		//bring together info about sorting direction for the table headings
		$this->tableHeadings = array(	'posted'		=> $dirpost."posted",
										'name'			=> $dirname."name",
										'message' 		=> $dirmess."message_input",
										'posting_id' 	=> $dirbelo."posting_id",
										'audio_length' 	=> $diraudi."audio_length"
										);

	}
}
?>
