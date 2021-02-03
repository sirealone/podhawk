<?php

class pdf_maker extends PluginPattern
{

	private $short_text_divider = "";
	private $pdfDir;
	private $fontsArray = array();
	private $fontCharSet = 'windows-1252';
	private $pdf; // instance of PDF

	function __construct($data=NULL)
	{

		$this->myName = "pdf_maker"; // PodHawk will know the plugin by this name
		$this->myFullName = "PDF Maker"; // a human readable name for the plugin
		$this->version = "2.0";

		// description of what the plugin does - displays on the plugin's backend page.
		$this->description = "This plugin creates a pdf (portable document format) document from the text of a posting, and inserts a link to the pdf file below each posting. It uses the <a href=\"http://www.fpdf.org/\">FPDF Library</a>. If you create a new directory \"pdf\" in the root of your PodHawk installation the plugin will cache the pdf files there. Otherwise, it will generate pdf files whenever they are requested (ie on the fly.)"; 
		$this->author = "Peter Carter";
		$this->contact = "cpetercarter@googlemail.com";

		// default parameters, eg for when the plugin is first activated
		$this->initialParams = array("strapline"		=> "",
									 "endText" 			=> "",
									 "image" 			=> "rssimage.jpg",
									 "headerLeftMargin" => "55",
									 "startPostingText" => "35",
									 "headerTextColour" => "7F7F7F",
									 "postFont" 		=> "arial",
									 "unifont" 			=> '',
									 "fileName" 		=> "My_Podcasts",
									 "pdfLink" 			=> "below_post",
									 "pdfLinkImage" 	=> 1);

		$this->params = (!empty($data['params'])) ? $data['params'] : $this->initialParams;

		$this->enabled = $data['enabled'];

		// the plugin needs to know when the onInitialise, onPostingDataReady, addHeadScript and onSavePosting events are triggered
		$this->listeners = array('onInitialise', 'onPostingDataReady', 'addHeadScript', 'onSavePosting');

		// and it needs to know if the "short text" plugin is enabled and, if it is, what value is set for the "divider" parameter
		$this->dataNeeded = array(array("other_plugin"=>"short_text", "param"=>"divider"));

		$this->pdfDir = PATH_TO_ROOT . "/pdf";

		$this->reg = Registry::instance();

		$this->fontsArray = array('arial' 		=> array('display' => 'Arial/Helvetica',
													'charset' => 'windows-1252',
													'fontFile' => 'core'),
								'times' 		=> array('display' => 'Times',
													'charset' => 'windows-1252',
													'fontFile' => 'core'),
								'courier' 		=> array('display' => 'Courier',
													'charset' => 'windows-1252',
													'fontFile' => 'core'),
								'dejavu'		=> array('display' => 'DejaVu',
													'charset' => 'UTF-8',
													'fontFile' => 'DejaVuSansCondensed.ttf'),
								'dejavu_serif'	=> array('display' => 'DejaVu Serif',
													'charset' => 'UTF-8',
													'fontFile' => 'DejaVuSerifCondensed.ttf'),
								'dejavu_mono'	=> array('display' => 'DejaVu Mono',
													'charset' => 'UTF-8',
													'fontFile' => 'DejaVuSansMono.ttf'),
								'liberation'	=> array ('display' => 'Liberation',
													'charset' => 'UTF-8',
													'fontFile' => 'LiberationSans-Regular.ttf')
							);
	
	}


	// this method is required to implement PluginPattern::backendPluginsPage(). It creates a set of html table rows which
	// Podhawk will insert into a table
	protected function backendPluginsPage()
	{
	$link = '{$posting.pdfLink}';
	$html = <<<EOF
	<tr>
		<td class="left">Strapline :</td>
		<td class="center"><textarea name="strapline" rows="4">{$this->params['strapline']}</textarea>
		<td class="right">The text at the top of your PDF page eg "The latest from Mike's podcasts"</td>
	</tr>
	<tr>
		<td class="left">Header image :</td>
		<td class="center">
		<script>
		function selectItem(li) {
		}

		function formatItem(row) {
		return row;
		}

		$(document).ready(function() {
		$("#suggest").autocomplete('index.php?page=autocomplete&type=images', { minChars:1, matchSubset:1, matchContains:1, cacheLength:10, onItemSelect:selectItem, formatItem:formatItem, selectOnly:1, mode:"multiple", multipleSeparator:" " });
	});
		</script>	
	<input type="text" id="suggest" name="image" value="{$this->params['image']}" /></td>
		<td class="right">Choose an image from your images folder to appear at the top left of your PDF, or leave blank for no image</br>
		<a href="../images/{$this->params['image']}"><img class="rssimage" src="../images/{$this->params['image']}" width="100" /></a></td>
	</tr>
	<tr>
		<td class="left">Margin to left of header text :</td>
		<td class="center"><input type="text" name="headerLeftMargin" value="{$this->params['headerLeftMargin']}" /></td>
		<td class="right">Margin at left of header text (in millimeters, not pixels).</td>
	</tr>
	<tr>
		<td class="left">Text colour for header :</td>
		<td class="center"><input class="color {pickerPosition:'right']}" type="text" name="headerTextColour" value="{$this->params['headerTextColour']}" /></td>
		</td>
		<td class="right">Click in the text box to show the colour picker</td>
	</tr>
	<tr>
		<td class="left">Vertical margin above text of posting :</td>
		<td class="center"><input type="text" name="startPostingText" value="{$this->params['startPostingText']}" /></td>
		<td class="right">Margin above the text of your posting (millimeters)</td>
	</tr>


	<tr>
		<td class="left">Font for posting text :</td>
		<td class="center">
EOF;

	foreach ($this->fontsArray as $name => $value)
	{
		$fonts[$name] = $value['display'];
	}

	$html .= $this->makeOptions($fonts, "postFont");

	$html .= <<<EOF
		</td>
		<td class="right">Choose the font you want for the text of your posting.</td>
	</tr>

	<tr>
		<td class="left">Or you can use a UTF-8 font (Unifont) in your unifont directory.</td>
		<td class="center"><input type="text" name="unifont" value="{$this->params['unifont']}" /></td>
		<td class="right">Upload a .ttf (TrueType font) file for the font you wish to use to your podhawk/custom/plugins/pdf_maker/fonts/unifont directory. Then enter the name of the font file (eg "Phetsarath_OT.ttf") here. The file name is cAseSensiTivE.</td>
	</tr>
	
	<tr>
		<td class="left">End text :</td>
		<td class="center"><textarea name="endText" rows="4">{$this->params['endText']}</textarea></td>
		<td class="right">Text to appear at the bottom of your PDF eg a copyright notice. Use ||URL|| to include the URL of the posting eg "You can find this podcast at ||URL||."</td>
	</tr>
	<tr>
		<td class="left">Name for your PDF file:</td>
		<td class="center"><input type="text" name="fileName" value="{$this->params['fileName']}" /></td>
		<td class="right">eg "Mikes_Podcasts". The plugin will then add the title of your post, like this "Mikes_Podcasts_Cool_Jazz.pdf".</td>
	</tr>
	<tr>
		<td class="left">Where do you want the link to your PDF to appear?</td>
		<td class="center">
EOF;
	$link_position_options = array("below_post" => "Below the posting text", "free" => "Somewhere else within the postings loop");

	$html .= $this->makeOptions($link_position_options, "pdfLink"); 
	
	$html .= <<<EOF
		</td>
		<td class="right">If you choose "Below the Posting Text", the plugin will automatically place a pdf link below each posting. If you want to display the link somewhere else inside the posting loop (eg immediately after the title), select "Somewhere else within the postings loop" and add the Smarty tag <code>$link</code> in the index.tpl file for your theme, in the place where you want the link to appear.</td>
	</tr>
	<tr>
		<td class="left">Image or text for the download link?</td>
		<td class="center">
EOF;
	$image = array("Text", "Image");

	$html .= $this->makeOptions($image, "pdfLinkImage");
				
	$html .=<<<EOF
		</td>
		<td class="right">The download link can be this image <img src="custom/plugins/pdf_maker/pdf.gif" alt="PDF"> or the words "Download PDF"</td>
	</tr>	
EOF;

	return $html;

	}

	// this method implements PluginsPattern::getParamsFromPosts()
	// It turns $_POST values from the backend form into an array of parameters which can be inserted into the database
	protected function getParamsFromPosts()
	{

		$params['strapline'] = entity_encode($_POST['strapline']);
		$params['endText'] = entity_encode($_POST['endText']);
		$params['image'] = trim($_POST['image']);
		$params['headerLeftMargin'] = (ctype_digit($_POST['headerLeftMargin'])) ? $_POST['headerLeftMargin'] : "55";
		$params['startPostingText'] = (ctype_digit($_POST['startPostingText'])) ? $_POST['startPostingText'] : "35";
		$params['headerTextColour'] = $_POST['headerTextColour'];
		$params['postFont'] = $_POST['postFont'];
		$params['unifont'] = $_POST['unifont'];
		$params['fileName'] = $_POST['fileName'];
		$params['pdfLink'] = $_POST['pdfLink'];
		$params['pdfLinkImage'] = $_POST['pdfLinkImage'];
	
		return $params;
	
	}
	
	// when the onInitialise event is triggered, the plugin checks to see whether a PDF is requested
	// and, if it is, the plugin creates the pdf on the fly from the text of the posting
	public function onInitialise()
	{

		if (ACTION == "webpage" && isset($_GET['action']) && $_GET['action'] == "pdf" && isset($_GET['id']))
		{
			cleanmygets();

			require('tfpdf.php');
			require('pdf.php');

			$this->pdf = new PDF();

			$unifontDir = 'podhawk/custom/plugins/pdf_maker/font/unifont'; // relative to PodHawk root

			$permissions = new Permissions(array($unifontDir));

			// open the unifont directory for caching font metrics
			$permissions->make_writable($unifontDir);

			// find the font to use, add it if necessary to the PDF object, set it as the font for the Chapter section, and find the charset
			$this->getFontToUse();

			// get the posting data and manipulate it as necessary
			$data = $this->getPDFData();

			// what our PDF file will be called
			$fileName = $this->makeFileName($_GET['id']);		
	
			// send posting data to the PDF object
			$this->pdf->getData($data);

			// create a PDF page		
			$this->pdf->AddPage();		

			// write the PDF page
			$this->pdf->ChapterBody();			

			// output the PDF to the browser
			$this->pdf->Output(urlencode($fileName), 'I');

			// close the unifont directory
			$permissions->make_not_writable($unifontDir);

			// save a copy of the PDF in the pdf directory if it exists
			$this->savePDF($fileName);
		
			// NB we need to exit the programme after the PDF has been sent.
			exit;
		}

	}
	
	// when the onPostingDataReady event is triggered, the plugin will EITHER create a link (to the PDF file) and insert it below the posting
	// OR it will request that a new offset 'pdfLink' is added to each element in the postings array

	public function onPostingDataReady($postings)
	{
		if (ACTION == "webpage")
		{
			$changed = array();	

			foreach ($postings as $key => $posting)
			{
				$title = my_html_entity_decode($posting['title']);

				$filePath = $this->params['fileName'] . "_" . str_replace (" ", "_", $title) . ".pdf";
				$encodedFilePath = "/pdf/" . urlencode($filePath);

				// a direct link to the pdf if it already exists in the pdf directory, otherwise a link to create the pdf
				$href = (is_readable($this->pdfDir . "/" . $filePath)) ? THIS_URL . $encodedFilePath : THIS_URL ."/index.php?action=pdf&amp;id=" . $key;
			
				// the "pdfLinkImage" parameter controls whether the link has a pdf icon or the words "Download PDF"
				$the_link = ($this->params['pdfLinkImage']) ? "<a href=\"" . $href . "\"><img src=\"podhawk/custom/plugins/pdf_maker/pdf.gif\" alt=\"pdf\" title=\"Download PDF\" /></a>" : "<a href=\"" . $href . "\">Download PDF</a>";

				if ($this->params['pdfLink'] == "below_post")
				{
					$this->postingFooter[$key] = $the_link;
				}
				else
				{	
					$changed[] = array("plugin"=>$this->myName, "variable" => "postings", "offset"=>array($key, "pdfLink"), "value"=>$the_link);
				}
			}

		return $changed;
		}
	}

	// when the addHeadScript event is triggered, the plugin checks what page is requested, and adds the javascript
	// for the colour-picker to the "head" section of the plugin's backend page

	public function addHeadScript()
	{
		$return = array();

		if (ACTION == "backend" && isset($_GET['page']) && isset($_GET['edit']) && $_GET['page'] == "plugins" && $_GET['edit'] == $this->myName)
		{

			$return[] = '<script src="backend/jscolor/jscolor.js" type="text/javascript"></script>';

		}

	return $return;
	}

	// when a posting is saved, we delete the existing cached pdf (if it exists) in case the text of the posting has changed
	public function onSavePosting ($id)
	{
		$title = $this->getPostingTitle($id);
		
		$fileName = $this->makeFileName($id);

		$path = $this->pdfDir . '/' . $fileName . ".pdf";

		if (file_exists($path))
		{
			$permissions = new Permissions (array('pdf'));
			$permissions->make_writable('pdf');
		 	unlink ($path);
			$permissions->make_not_writable('pdf');
		}

		return array();  

	}

	// calculates the data which the FPFD programme needs
	private function getPDFData()
	{
		$result = $this->getPostingData();

		$title = $this->getText($result['title'], false);
		
		$date_format = $this->convertDateFormat($this->reg->findSetting('preferred_date_format'));

		$text = $this->getText($result['message_html'], true);	
	
		$date = date($date_format, strtotime($result['posted']));
	
		$author = $this->reg->getNickname($result['author_id']);

		$image = (!empty($this->params['image'])) ? PATH_TO_ROOT . "/images/" . $this->params['image'] : "";

		$strapline = my_html_entity_decode($this->params['strapline']);

		$thisPostURL = PO_Posting_Extended::getPermalink($_GET['id']);

		$URLLink = "<a href=\"$thisPostURL\">$thisPostURL</a>";

		$endText = str_replace("||URL||", $URLLink, my_html_entity_decode($this->params['endText']));

		$headerTextColour = $this->params['headerTextColour'];

		$headerLeftMargin = $this->params['headerLeftMargin'];

		$startPostingText = $this->params['startPostingText'];
	
		$authDate = "Posted by " . $author . " on " . $date;

/*$fp = fopen(LOG_DIR . 'special.log', 'wb');
fwrite ($fp, $text);
fclose ($fp);
echo "Written to log dir";
exit;*/


		$data = array(	'title' 			=> $title,
						'text' 				=> $text,
						'date' 				=> $date,
						'author' 			=> $author,
						"image" 			=> $image,
						"strapline" 		=> $strapline,
						'endText' 			=> $endText,
						'headerTextColour' 	=> $headerTextColour,
						'headerLeftMargin' 	=> $headerLeftMargin,
						'startPostingText' 	=> $startPostingText,
						'authDate' 			=> $authDate
						);
		return $data;
	}
	
	private function getPostingData()
	{
		$dosql = "SELECT title, posted, author_id, message_html from " . DB_PREFIX . "lb_postings WHERE id = :id";
		
		$GLOBALS['lbdata']->prepareStatement($dosql);

		$result = $GLOBALS['lbdata']->executePreparedStatement(array(':id' => $_GET['id']));

		if (empty($result[0])) die('I cannot find the requested posting');

		else return $result[0];

	}

	private function getPostingTitle($id)
	{
		$dosql = "SELECT title FROM " . DB_PREFIX . "lb_postings WHERE id = :id";
		$GLOBALS['lbdata']->prepareStatement ($dosql);
		$result = $GLOBALS['lbdata']->executePreparedStatement(array(':id' => $id));
		$title = $result[0]['title'];
		$title = my_html_entity_decode($title);

		return $title;
	}

	private function getText($rawText, $message=false)
	{
		$text = my_html_entity_decode($rawText);

		$text = preg_replace("/\t/", '', $text); // suppress tabs which might otherwise generate unwanted characers in the PDF

		if ($message)
		{
			$text = $this->removeShortTextDivider($text);
			$text = $this->removeATags($text);
		}

		if ($this->fontCharSet != 'UTF-8') // convert text to a charset appropriate to the font we want to use
		{
			if (function_exists('iconv'))
			{
				$text = iconv('UTF-8', $this->fontCharSet.'//TRANSLIT', $text);
			}
			else
			{
				$text = utf8_decode($text); // decodes to ISO-8859-1
			}
		}

		return $text;		
	}
	
	private function getFontToUse()
	{
		$unifontDir = PLUGINS_DIR . $this->myName . '/font/unifont/';

		if (!empty($this->params['unifont']) && file_exists($unifontDir . $this->params['unifont']))
		{
			$fontName = substr(strtolower($this->params['unifont']), 0, 6);
			$this->fontCharSet = 'UTF-8';
			$fontFile = $this->params['unifont'];
		}
		else
		{
			$fontName = $this->params['postFont'];
			$this->fontCharSet = $this->fontsArray[$fontName]['charset'];
			$fontFile = $this->fontsArray[$fontName]['fontFile'];
		}

		if ($fontFile != 'core')
		{
			$this->pdf->AddFont($fontName,'', $fontFile, true);
			$bold = substr($fontFile, 0, -4) . '-Bold.ttf';
			$italics = substr($fontFile, 0, -4) . '-Oblique.ttf';
			$boldItalics = substr($fontFile, 0, -4) . '-BoldOblique.ttf';

			// are there bold/italic/bold-italic versions of the font? If so, register them....
			if (file_exists($unifontDir . $bold) && file_exists($unifontDir . $italics) && file_exists($unifontDir . $boldItalics))
			{
				$this->pdf->AddFont($fontName,'B', $bold, true);
				$this->pdf->AddFont($fontName, 'I', $italics, true);
				$this->pdf->AddFont($fontName, 'BI', $boldItalics, true);
			}
			else // ....else set PDF object to ignore italic or bold tags.
			{
				$this->pdf->setBI(false); 
			}
		}

		$this->pdf->SetFont($fontName);
	}

	private function makeFileName($id)
	{
		$title = $this->getPostingTitle($id);
		$fileName = $this->params['fileName'] . "_" . str_replace(" ", "_", $title);
		return $fileName;
	}

	// the preferred date format in "settings" is formatted for Smarty. This method converts it to a format
	// useable by the php date() function
	private function convertDateFormat($string)
	{
		$search = array("%", "a", "A", "b", "B", "e");
		$replace = array("", "D", "l", "M", "F", "j");
		return str_replace($search, $replace, $string);
	}

	private function savePDF($fileName)
	{
		if (file_exists($this->pdfDir))
		{
			// create a permissions object to handle writing to the pdf directory
			$permissions = new Permissions (array('pdf'));

			$permissions->make_writable('pdf');

			if (is_writable($this->pdfDir))
			{
				$this->pdf->Output($this->pdfDir . "/" . $fileName . '.pdf', 'F');		
			}

			$permissions->make_not_writable('pdf');
		}
	}

	// we need to remove the "divider" used by the Short Text plugin from the posting text before creating the PDF
	private function removeShortTextDivider ($string)
	{
		return str_replace($this->short_text_divider, "", $string);
	}

	// registers the "divider" used by the Short Text plugin
	public function receiveData($data)
	{
		if ($data[0] == 'short_text' && $data[1] == 'divider')
		{
			$this->short_text_divider = $data[2];

		}
	}

	private function removeATags ($text)
	{
		//the pdf programme will not print images if the img tag is surrounded by <a...></a> - so we remove the <a> tags
		$search = "/(<a[^>]+>)(<img[^>]+>)(<\/a>)/i";

		$replace = '$2';

		$text = preg_replace($search, $replace, $text);
	
		return $text;

	}	
}
?>
