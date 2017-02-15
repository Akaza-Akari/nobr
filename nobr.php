<?php
 
// Take credit for your work.
$wgExtensionCredits['parserhook'][] = array(
	'path' => __FILE__,
	'name' => 'nobr',
	'description' => "br to [Enter].",
	'version' => '1.0',
	'author' => 'koreapyj, 김동동, 코코아',
	'url' => 'https://github.com/Hoto-Cocoa/nobr',
   
	'license-name' => "AGPL-3.0",
  
);

if ( !defined( 'MEDIAWIKI' ) ) {
	die;
}

$wgHooks['ParserBeforeTidy']['nobrparser'] = 'nobr';

function nobr( &$parser, &$text ) {
	$title = $parser->getTitle();
if(!preg_match('/^특수:/', $title)) {
		$wEngine = new nobrclass($text);
		$text =  $wEngine->toHtml();
		$br_regex = '@^(.*?)(?<!<br/>|<br>|<br />)\n(?!<p>|<h|</p|<e|<u|<l|편집한 내용은 아직|이것을 입력하지|<a onclick|<br|</ol|</li|<if|<div|</div|<dl|<dd|</u|<m|</m|<t|</t|<o|</o|<blockquote)([^\n])@m';
		do {
		$text = preg_replace($br_regex, '$1<br>$2', $text);
		} while(preg_match($br_regex, $text));
	}
}

class nobrclass extends nobr {

	function __construct($wtext) {

		$this->WikiPage = $wtext;

		$this->toc = array();
		$this->fn = array();
		$this->fn_cnt = 0;
		$this->prefix = '';
	}

	public function toHtml() {
		$this->whtml = $this->WikiPage;
		$this->whtml = $this->htmlScan($this->whtml);
		return $this->whtml;
	}

	private function htmlScan($text) {
		$result = '';
		$len = strlen($text);
		$now = '';
		$line = '';


		for($i=0;$i<$len;$this->nextChar($text,$i)) {
			$now = $this->getChar($text,$i);

			if($now == "\n") {
				$result .= $this->lineParser($line, '');
				$line = '';
			}
			else
				$line.=$now;
		}
		if($line != '')
			$result .= $this->lineParser($line, 'notn');
		return $result;
	}

    protected function blockParser($block) {
        $result = '';
        $block_len = strlen($block);

        $result .= $this->formatParser($block);
        return $result;
    }

}

class nobr {

	protected function lineParser($line, $type) {

		$line = $this->blockParser($line);

		if($type == 'notn')
			return $line;
		else
            return $line."\n";
	}

	protected function formatParser($line) {
		$line_len = strlen($line);
		for($j=0;$j<$line_len;self::nextChar($line,$j))
		return $line;
	}
	
	protected static function getChar($string, $pointer){
		if(!isset($string[$pointer])) return false;
		$char = ord($string[$pointer]);
		if($char < 128){
			return $string[$pointer];
		}else{
			if($char < 224){
				$bytes = 2;
			}elseif($char < 240){
				$bytes = 3;
			}elseif($char < 248){
				$bytes = 4;
			}elseif($char == 252){
				$bytes = 5;
			}else{
				$bytes = 6;
			}
			$str = substr($string, $pointer, $bytes);
			return $str;
		}
	}

	protected static function nextChar($string, &$pointer){
		if(!isset($string[$pointer])) return false;
		$char = ord($string[$pointer]);
		if($char < 128){
			return $string[$pointer++];
		}else{
			if($char < 224){
				$bytes = 2;
			}elseif($char < 240){
				$bytes = 3;
			}elseif($char < 248){
				$bytes = 4;
			}elseif($char == 252){
				$bytes = 5;
			}else{
				$bytes = 6;
			}
			$str = substr($string, $pointer, $bytes);
			$pointer += $bytes;
			return $str;
		}
	}
}

?>