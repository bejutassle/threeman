<?php
/**
 * ThreeMan Web Application
 * @author Emre Emir <emre@emreemir.com>
 * @package BBCode Helper
 * @copyright  It is protected by the GNU General Public License! All Rights Reserved.
 */

namespace Helper;

use Decoda\Decoda as Decoda;
use Decoda\Filters\DefaultFilter as DefaultFilter;
use Decoda\Filters\TextFilter as TextFilter;
use Decoda\Filters\TableFilter as TableFilter;
use Decoda\Filter\EmailFilter as EmailFilter;
use Decoda\Filter\UrlFilter as UrlFilter;
use Decoda\Filter\ImageFilter as ImageFilter;
use Decoda\Filter\BlockFilter as BlockFilter;
use Decoda\Filter\ListFilter as ListFilter;
use Decoda\Filter\VideoFilter as VideoFilter;
use Decoda\Filter\CodeFilter as CodeFilter;
use Decoda\Filter\QuoteFilter as QuoteFilter;
use Decoda\Hook\CensorHook as CensorHook;
use Decoda\Hook\ClickableHook as ClickableHook;

class BBCode {

	/**
	 *
	 * This function parses BBcode tag to HTML code (XHTML transitional 1.0)
	 *
	 * It parses (only if it is in valid format e.g. an email must to be
	 * as example@example.ext or similar) the text with BBcode and
	 * translates in the relative html code.
	 *
	 * @param string $text
	 * @param boolean $advanced his var describes if the parser run in advanced mode (only *simple* bbcode is parsed).
	 * @return string
	 */
	public static function parse($text, $advanced = true, $charset = 'UTF-8'){
		$config = config('bbcode');
		$code = new Decoda($text, $config);

		$code->addFilter(new DefaultFilter());
		$code->addFilter(new EmailFilter());
		$code->addFilter(new ImageFilter());
		$code->addFilter(new UrlFilter());
		$code->addFilter(new BlockFilter());
		$code->addFilter(new VideoFilter());
		$code->addFilter(new CodeFilter());
		$code->addFilter(new ListFilter());
		$code->addFilter(new TableFilter());
		$code->addFilter(new QuoteFilter());
		$code->addFilter(new TextFilter());

		//$code->addHook(new CensorHook());
		$code->addHook(new ClickableHook());

		return $code->parse();
	}
}