<?php

class PO_Pagination_Webpage extends PO_Pagination
{

	public function getRows() // we want an array with post ids as key
	{
		$return = array();

		foreach ($this->rowsForCurrentPage as $row)
		{
			$id = $row['id'];
			$return[$id] = $row;
		}
		return $return;
	}
		
	public function jwPlayerRequired()
	{
		$return = false;

		foreach ($this->rowsForCurrentPage as $row)

		{
			$data = DataTables::AudioTypeData($row['audio_type']);
			$playerType  = (isset($data['player'])) ? $data['player'] : '';

			$audioPlayerType = $this->reg->findPlayersSetting('audio_player_type');

			if ($playerType == 'jwvideo' || ($playerType == 'flash' && $audioPlayerType == 'jwaudioplayer'))
			{
				$return = true;
			}
		}

		return $return;
	}

	public function lightboxRequired()
	{
		$lightboxRequired = false;

		foreach ($this->rowsForCurrentPage as $row)
		{
			if ($row['audio_type'] == '24') $lightboxRequired = true; // xml slideshow

			if (strpos($row['message_html'], 'rel="lightbox') !== false) $lightboxRequired = true; // image tag with lightbox
		}
		
		return $lightboxRequired;
	}

	protected function findRowsPerPage()
	{
		return $this->reg->findSetting('posts_per_page');
	}

	protected function getTable()
	{
		return DB_PREFIX . 'lb_postings';
	}

	protected function buildWhereString()
	{
		if (isset($_GET['id']))
		{
			$this->preparedStatementArray[':id'] = $_GET['id'];
			
			$return = " WHERE id = :id";

			$preview = $this->reg->findSetting('previews');

			if ($preview == false || empty($_GET['preview']))
			{
				$return .= " AND " . $this->getStatus();
			}

			return $return;
		}

		$elements = array();

		if (isset($_GET['date']))
		{
			$date = $this->getDate();

			if (!empty($date))
			{
				$elements[] = $date;
			}
		}

		if (isset($_GET['cat']))
		{
			$cat = $this->getCat();
			if (!empty($cat))
			{
				$elements[] = $cat;
			}
		}

		if (isset($_GET['tag']))
		{
			$elements[] = $this->getTag();
		}
		if (isset($_GET['author']))
		{
			$elements[] = $this->getAuthor();
		}

		$elements[] = $this->getStatus();

		if (!empty($elements))
		{
			return " WHERE " . implode(" AND ", $elements);
		}
		else
		{
			return '';
		}		

	}

	protected function buildOrderString()
	{
		if (isset($_GET['id']))
		{
			return '';
		}
		else
		{
			return " ORDER BY sticky DESC, posted DESC";
		}
	}

	protected function buildLimitString()
	{
		if (isset($_GET['id']))
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
		return '*';
	}

	protected function getDate()
	{
		$date = $_GET['date'];
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

	protected function getCat($c=NULL)
	{
		$tempcatid = 0;
	
		$return = "(category1_id = :id1 OR category2_id = :id2 OR category3_id = :id3 OR category4_id = :id4)";

		$tempcatid = $this->reg->getCategoryId($_GET['cat']);

		if ($tempcatid > 0) // if we have found a cat id
		{
			$this->preparedStatementArray[':id1'] = $tempcatid;
			$this->preparedStatementArray[':id2'] = $tempcatid;
			$this->preparedStatementArray[':id3'] = $tempcatid;
			$this->preparedStatementArray[':id4'] = $tempcatid;

			return $return;
		}
		else // else return empty string
		{
			return '';
		}
	}

	protected function getTag()
	{
		$tagsToShow = explode('+', $_GET['tag']);

		$tagSQL = array();

		$i = 1;
		foreach ($tagsToShow as $tagToShow)
		{
			$tagToShow = entity_encode($tagToShow);
			$tagSQL[] = "tags LIKE :tag_$i";
			$this->preparedStatementArray[":tag_$i"] = '%' . $tagToShow . '%';
			$i++;
		}

		$return = '(' . implode(' OR ', $tagSQL) . ')';
		return $return;
	}

	protected function getAuthor()
	{
		$this->preparedStatementArray[':author'] = $_GET['author'];

		return "author_id = :author";
	}

	protected function getStatus() // we want only 'on air' posts not dated in the future
	{
		return "status = '3' AND posted < '" . date("Y-m-d H:i:s") . "'";	
	}			
}

?>
