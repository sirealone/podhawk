<?php

class FacebookPagination extends PO_Pagination_Webpage
{
	//private $appData = array(); // array of 'app_data' returned in the 'signed request'

	public function __construct()
	{
		parent::__construct();

		if (ACTION == 'facebook_page')
		{
			$this->baseURL = THIS_URL . "/podhawk/custom/plugins/facebook_plugin/index.php";
		}
		elseif (ACTION == 'facebook_apptab')
		{
			$this->baseURL = 'apptab.php';
		}
	}	

	protected function buildWhereString()
	{
		$return = ' WHERE ';

		if (isset($_GET['id']))
		{
			$this->preparedStatementArray[':id'] = $_GET['id'];
			
			$return .= "id = :id AND ";
		}
		else
		{
			$plugins = Plugins::instance();

			$catID = $plugins->getParam('facebook_plugin', 'cats');;

			if (!empty($catID))
			{
				$return .= "(category1_id = :id1 OR category2_id = :id2 OR category3_id = :id3 OR category4_id = :id4) AND ";

				$this->preparedStatementArray[':id1'] = $catID;
				$this->preparedStatementArray[':id2'] = $catID;
				$this->preparedStatementArray[':id3'] = $catID;
				$this->preparedStatementArray[':id4'] = $catID;
			}
		}
		
		$return .= $this->getStatus();

		return $return;
	}

}
?>
