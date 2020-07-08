<?php
namespace Tatocaster\Monolog\Formatter;

use Monolog\Formatter\JsonFormatter;

/**
 * extension of JsonFormatter of Monolog, which pretty prints json and supports unicode. Does not escape slashes.
 */
class JsonPrettyUnicodePrintFormatter extends JsonFormatter
{
    protected $isJson;

	public function __construct($batchMode = 1, $appendNewline = true){
		parent::__construct($batchMode, $appendNewline);
	}

    public function format(array $record)
    {
        return json_encode($record, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . ($this->appendNewline ? ",\n" : '');
    }
}
