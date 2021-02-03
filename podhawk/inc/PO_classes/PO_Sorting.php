<?php

class PO_Sorting
{
	public function __construct()
	{
		if (isset($_REQUEST['sort']))
		{
			$this->sortby = substr($_REQUEST['sort'],1);
    		$this->sortdir = substr($_REQUEST['sort'],0,1);
			$this->order = ($this->sortdir == '0') ? 'ASC' : 'DESC'; 
		}
	}

	public function getSortField()
	{
		return $this->sortby;
	}

	public function getSortDir()
	{
		return $this->sortdir;
	}

	public function getSortOrder()
	{
		return $this->order;
	}
}
?>
