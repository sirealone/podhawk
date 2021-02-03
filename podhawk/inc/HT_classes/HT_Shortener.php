<?php

class HT_Shortener
// shortens a string containing html markup, closing any unclosed html tags
{

	private $inputText='';
	private $outputText='';
	private $addString='';
	private $length;
	private $addStringPosition = 'in';
	private $lastSpaceCut = true;

	public function __construct($string, $length)
	{
		$this->inputText = $string;

		$this->length = $length;
	}

	public function getShortText()
	{
		$this->shorten();

		return $this->outputText;
	}

	public function setAddString($string, $position)
	{
		$this->addString = $string;

		$this->addStringPosition = $position; //'in' = inside any unclosed p or li tag before it is closed; 'out' = after closure of all unclosed tags

	}

	public function setLastSpaceCut($bool)
	{
		$this->lastSpaceCut = $bool;
	}

	public function shorten()
	{

		if (strlen($this->inputText) > $this->length)
		{
			if(!empty($this->inputText) && $this->length > 0)
			{
				$isText = true;
				$ret = "";
				$i = 0; // counter for the number of text characters we have parsed

				$currentChar = "";
				$lastSpacePosition = -1;
				$lastChar = "";

				$tagsArray = array();
				$currentTag = "";
				$tagLevel = 0;

				$addstringAdded = false; // flag to prevent us from adding the 'addstring' more than once

				$noTagLength = strlen(strip_tags($this->inputText)); // ie the text length of the input string

				// Parser loop
				for( $j=0; $j<strlen( $this->inputText ); $j++ )
				{
					$currentChar = substr( $this->inputText, $j, 1 );
					$ret .= $currentChar; // add the current char to the string to be returned

					// Lesser than event
					if( $currentChar == "<") $isText = false;

					// if we are dealing with text
					if( $isText )
					{
						// Memorize last space position
						if( $currentChar == " " ) $lastSpacePosition = $j;
						//else { $lastChar = $currentChar; }

						$i++; // increment the count of text characters
					}
					else // if we are dealing with an html tag
					{
						$currentTag .= $currentChar;
					}

					// Greater than event
					if( $currentChar == ">" )
					{
						$isText = true;

						// establish whether the tag is an opening tag
						if( ( strpos( $currentTag, "<" ) !== FALSE ) && 	// the tag has a < at the beginning..
							( strpos( $currentTag, "/>" ) === FALSE ) && 	//.. but it it not a closing tag
							( strpos( $currentTag, "</") === FALSE ) ) 		//..or a self-closing tag
						{

							// if it is an opening tag, find the tag name ..
							// .. if the tag has attribute(s)
							if( strpos( $currentTag, " " ) !== FALSE )
							{
								$currentTag = substr( $currentTag, 1, strpos( $currentTag, " " ) - 1 );
							}
							else
							{
								// .. or if the tag doesn't have attribute(s)
								$currentTag = substr( $currentTag, 1, -1 );
							}

							// add the tag name to stack of tags
							array_push( $tagsArray, $currentTag );

						}
						 // if the tag is a closing tag
						else if( strpos( $currentTag, "</" ) !== FALSE )
						{
							array_pop( $tagsArray ); // remove the most recent element in the array of tags
						}

						$currentTag = ""; // start again with a clean sheet
					}

					if( $i >= $this->length) // break if we have parsed 'length' characters
					{
						break;
					}
				}

				// Cut HTML string at last space position
				if( $this->length < $noTagLength )
				{
					if( $lastSpacePosition != -1 && $this->lastSpaceCut == true) // have we found a space, and do we want to cut there?
					{
						$ret = substr( $this->inputText, 0, $lastSpacePosition );
					}
					else // we have not found a space, or we don't want to move the pointer back to the last space
					{
						$ret = substr( $this->inputText, 0, $j );
					}
				}
				else // if we haven't cut the string
				{
					$addstringAdded = true; // set flag to prevent the "add string" being appended to the end.
				}

				// Close broken XHTML elements
				while( sizeof( $tagsArray ) != 0 )
				{
					$aTag = array_pop( $tagsArray );
					// if a <p> or <li> tag needs to be closed, put the add-string in first
					if ($this->addStringPosition == 'in' && ($aTag == "p" || $aTag == "li")  && !$addstringAdded)
					{
						$ret .= $this->addString;
						$addstringAdded = true;
					}
					$ret .= "</" . $aTag . ">\n";
				}

			}
			else // if we have an empty input string or a zero required length
			{
				$ret = "";
			}

			// if we have not added the add-string already, add it now 
			if ( $addstringAdded == false)
			{
				$this->outputText = $ret . $this->addString;
			}
			else
			{
				$this->outputText = $ret;
			}
		}
		else
		{
			$this->outputText = $this->inputText;
		}
	}	
}
?>
