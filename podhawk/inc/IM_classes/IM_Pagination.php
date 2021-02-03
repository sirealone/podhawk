<?php

class IM_Pagination extends PO_Pagination_BackendComments
{
	private $listManager; // instance of IM_ListManager.

	public function __construct()
	{
		$this->URLPageToken = 'nr';
		$this->baseURL = 'index.php?page=images&amp;action=list';

		if (!empty($_REQUEST['auth']))
		{
			$this->baseURL .= "&amp;auth=" . $_REQUEST['auth'];
		}

		$this->reg = Registry::instance();
		$this->log = LO_ErrorLog::instance();

		$this->listManager = new IM_ImageList();

		$this->currentPage = $this->findCurrentPage();

		$this->rowsPerPage = $this->findRowsPerPage();

		$this->totalRows = $this->listManager->countList();

		$this->totalPages = ceil($this->totalRows/$this->rowsPerPage);

		$this->rowsForCurrentPage = $this->findRowsForCurrentPage();
	}

	protected function findRowsForCurrentPage()
	{
		$imageData = array();

		$allImages = $this->listManager->getList();

		$imagesForThisPage = array_slice($allImages, $this->getOffset(), $this->rowsPerPage);

		foreach ($imagesForThisPage as $image)
		{
			$imageManager = new IM_Image($image);

			$imageData[] = $imageManager->getImageData();
		}
		
		return $imageData;

	}
}

?>
