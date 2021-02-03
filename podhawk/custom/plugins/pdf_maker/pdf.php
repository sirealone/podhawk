<?php

class PDF extends tFPDF  {

var $title;
var $text;
var $date;
var $author;
var $image;
var $strapline;
var $endText;
var $headerTextColour;
var $headerLeftMargin;
var $startPostingText;
var $postFont;
var $authDate;
var $B;
var $I;
var $U;
var $HREF;
var $fontList;
var $issetfont;
var $issetcolor;
var $PRE;
var $bi = true;


function PDF($orientation='P', $unit='mm', $format='A4')
{
    	//Call parent constructor
    	$this->tFPDF($orientation,$unit,$format);
	
    	//Initialization
    	$this->B=0;
    	$this->I=0;
    	$this->U=0;
   	$this->HREF='';
    	$this->fontlist=array('arial', 'times', 'courier', 'helvetica', 'symbol');
    	$this->issetfont=false;
    	$this->issetcolor=false;
	$this->PRE = false;
	$this->debug=false;
	$this->AliasNbPages();
}


function getData ($data) {

	foreach ($data as $name => $value) {
	
		$this->$name = $value;

		}
	}


function Header() {

	$m = $this->headerLeftMargin;
	if (!isset($this->headerTextColour)) $this->HeaderTextColour = "#7F7F7F";
	$c = $this->hex2dec($this->headerTextColour);
	 
	// header image
	if (!empty($this->image)) {
    		$this->Image($this->image,10,8,0,20);
		}

	// strapline
	$this->SetXY($m, 8);
	$this->SetFont('Arial','I',12);    	
    	$this->SetTextColor($c['R'], $c['G'], $c['B']);	
	$this->MultiCell(0, 5, $this->strapline);

   	// title
    	$this->SetFont('Arial','B',15);   
    	$this->SetXY($m, 19);   	 
    	$this->MultiCell(0,9,$this->title,0,1);
	
	// author and date
	$this->SetX($m);
	$this->SetFont('Arial','I',12);  
	$this->Cell(0, 5, $this->authDate, 0, 1);
	$this->ln(2);
	    

	}

function Footer()  {

	
    // page number at bottom of page
    $this->SetY(-15);    
    $this->SetFont('Arial','I',8);    
    $this->SetTextColor(128);   
    $this->Cell(0,10,'Page '.$this->PageNo(),0,0,'C');
}


function ChapterBody ()  {

	// set the starting position and the font
	$this->SetY($this->startPostingText);
	$this->SetFont($this->postFont,'',12);

    	// parse html
    	$this->WriteHTML($this->text);    	
    	$this->Ln(10);

    	// tailpiece below text
    	$this->SetFont($this->postFont,'', '12');
	$this->SetTextColor(0, 0, 0);
    	$this->WriteHTML($this->endText);

	}

function setBI($bi)
{
	$this->bi = $bi;
}

function WriteHTML($html,$bi=true)
    {
        //remove all unsupported tags
        $bi = $this->bi;
        if ($bi)
            $html=strip_tags($html,"<a><img><p><br><font><tr><blockquote><h1><h2><h3><h4><pre><red><blue><ul><li><hr><b><i><u><strong><em>"); 
        else
            $html=strip_tags($html,"<a><img><p><br><font><tr><blockquote><h1><h2><h3><h4><pre><red><blue><ul><li><hr>"); 
        $html=str_replace("\n",'',$html); //replace carriage returns with spaces
	$html = str_replace("\r",'',$html);
        // debug
        if ($this->debug) { echo $html; exit; }

        $html = str_replace('&trade;','™',$html);
        $html = str_replace('&copy;','©',$html);
        $html = str_replace('&euro;','€',$html);

        $a=preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
        $skip=false;
        foreach($a as $i=>$e)
        { 
            if (!$skip) {
                if($this->HREF)
                    $e=str_replace("\n","",str_replace("\r","",$e));
                if($i%2==0)
                {
                    // new line
                    if($this->PRE)
                        $e=str_replace("\r","\n",$e);
                    else
                        $e=str_replace("\r","",$e);
                    //Text
                    if($this->HREF) {
                        $this->PutLink($this->HREF,$e);
                        $skip=true;
                    } else 
                        $this->Write(5,stripslashes(my_html_entity_decode($e)));
                } else {
                    //Tag
			$f= trim($e);
                    if (substr($f, 0, 1) == "/") {
                        $this->CloseTag(strtoupper(substr($f,strpos($f,'/')+1)));
			 }
                    else {
                        //Extract attributes
                        $a2=explode(' ',$e);
                        $tag=strtoupper(array_shift($a2));
                        $attr=array();
                        foreach($a2 as $v) {
                            if(preg_match('/([^=]*)=["\']?([^"\']*)/',$v,$a3))
                                $attr[strtoupper($a3[1])]=$a3[2];
                        }
                        $this->OpenTag($tag,$attr);
                    }
                }
            } else {
                $this->HREF='';
                $skip=false;
            }
        }
    }

    function OpenTag($tag,$attr)
    {
        //Opening tag
        switch($tag){
            case 'STRONG':
            case 'B':
                if ($this->bi)
                    $this->SetStyle('B',true);
                else
                    $this->SetStyle('U',true);
                break;
            case 'H1':
                $this->Ln(10);
                $this->SetTextColor(150,0,0);
                $this->SetFontSize(22);
                break;
            case 'H2':
                $this->Ln(10);
                $this->SetFontSize(18);
                $this->SetStyle('U',true);
                break;
            case 'H3':
                $this->Ln(10);
                $this->SetFontSize(16);
                $this->SetStyle('U',true);
                break;
            case 'H4':
                $this->Ln(10);
                $this->SetTextColor(102,0,0);
                $this->SetFontSize(14);
                if ($this->bi)
                    $this->SetStyle('B',true);
                break;
            case 'PRE':
                $this->SetFont('Courier','',11);
                $this->SetFontSize(11);
                $this->SetStyle('B',false);
                $this->SetStyle('I',false);
                $this->PRE=true;
                break;
            case 'RED':
                $this->SetTextColor(255,0,0);
                break;
            case 'BLOCKQUOTE':
                $this->mySetTextColor(100,0,45);
                $this->Ln(3);
                break;
            case 'BLUE':
                $this->SetTextColor(0,0,255);
                break;
            case 'I':
            case 'EM':
                if ($this->bi)
                    $this->SetStyle('I',true);
                break;
            case 'U':
                $this->SetStyle('U',true);
                break;
            case 'A':
                $this->HREF=$attr['HREF'];
                break;
            case 'IMG':
                if(isset($attr['SRC']) && (isset($attr['WIDTH']) || isset($attr['HEIGHT']))) {
                    if(!isset($attr['WIDTH']))
                        $attr['WIDTH'] = 0;
                    if(!isset($attr['HEIGHT']))
                        $attr['HEIGHT'] = 0;
                    $this->Image($attr['SRC'], $this->GetX(), $this->GetY(), $this->px2mm($attr['WIDTH']), $this->px2mm($attr['HEIGHT']));
                    
			$this->Ln($this->px2mm($attr['HEIGHT']));
                }
		
                break;
            case 'LI':
                $this->Ln(5);
                $this->SetTextColor(128);
                $this->Write(5,'>> ');
                $this->mySetTextColor(-1);
                break;
            case 'TR':
                $this->Ln(7);
                $this->PutLine();
                break;
            case 'BR':
                $this->Ln(5);
                break;
            case 'P':
		$this->Ln(5);
		break;
	    case 'UL';
                $this->Ln(5);
                break;
            case 'HR':
                $this->PutLine();
                break;
            case 'FONT':
                if (isset($attr['COLOR']) && $attr['COLOR']!='') {
                    $coul=$this->hex2dec($attr['COLOR']);
                    $this->mySetTextColor($coul['R'],$coul['G'],$coul['B']);
                    $this->issetcolor=true;
                }
                if (isset($attr['FACE']) && in_array(strtolower($attr['FACE']), $this->fontlist)) {
                    $this->SetFont(strtolower($attr['FACE']));
                    $this->issetfont=true;
                }
                break;
        }
    }

    function CloseTag($tag)
    {
        //Closing tag
        if ($tag=='H1' || $tag=='H2' || $tag=='H3' || $tag=='H4') {
            $this->Ln(6);
            $this->SetFont($this->postFont,'',12);
            $this->SetFontSize(12);
            $this->SetStyle('U',false);
            $this->SetStyle('B',false);
            $this->SetTextColor(0, 0, 0);
        }
	if ($tag == 'UL')  {
		$this->Ln(5);
		}
        if ($tag=='PRE'){
            $this->SetFont($this->postFont,'',12);
            $this->SetFontSize(12);
            $this->PRE=false;
        }
        if ($tag=='RED' || $tag=='BLUE')
            $this->mySetTextColor(-1);
        if ($tag=='BLOCKQUOTE'){
            $this->mySetTextColor(0,0,0);
            $this->Ln(3);
        }
        if($tag=='STRONG')
            $tag='B';
        if($tag=='EM')
            $tag='I';
        if((!$this->bi) && $tag=='B')
            $tag='U';
        if($tag=='B' || $tag=='I' || $tag=='U')
            $this->SetStyle($tag,false);
        if($tag=='A')
            $this->HREF='';
        if($tag=='FONT'){
            if ($this->issetcolor==true) {
                $this->SetTextColor(0,0,0);
            }
            if ($this->issetfont) {
                $this->SetFont($this->postFont,'',12);
                $this->issetfont=false;
            }
        }
    }


function SetStyle($tag, $enable)
{
    //Modify style and select corresponding font
    $this->$tag+=($enable ? 1 : -1);
    $style='';
    foreach(array('B','I','U') as $s)
    {
        if($this->$s>0)
            $style.=$s;
    }
    $this->SetFont('',$style);
}

function PutLink($URL, $txt)  {

    	//Put a hyperlink
    	$this->SetTextColor(0,0,255);
    	$this->SetStyle('U',true);
    	$this->Write(5,$txt,$URL);
   	$this->SetStyle('U',false);
   	$this->SetTextColor(0);
}

function PutLine() {

        $this->Ln(2);
        $this->Line($this->GetX(),$this->GetY(),$this->GetX()+187,$this->GetY());
        $this->Ln(3);

    }

function mySetTextColor($r,$g=0,$b=0){
        static $_r=0;
	static $_g=0;
	static $_b=0;

        if ($r == -1) 
            $this->SetTextColor($_r,$_g,$_b);
        else {
            $this->SetTextColor($r,$g,$b);
            	$_r=$r;
           	$_g=$g;
          	$_b=$b;
        }
    }

function px2mm($px) {
    return $px*25.4/72;
	}

function hex2dec($color = "#000000") {
    $tbl_color = array();
    $tbl_color['R']=hexdec(substr($color, 0, 2));
    $tbl_color['G']=hexdec(substr($color, 2, 2));
    $tbl_color['B']=hexdec(substr($color, 4, 2));
    return $tbl_color;
	}


}

?>
