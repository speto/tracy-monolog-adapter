<?php

/**
 * @license    New BSD License
 * @link       https://github.com/nextras/tracy-monolog-adapter
 */

namespace Nextras\TracyMonologAdapter\Processors;

use Throwable;
use Tracy\Helpers;


class TracyExceptionProcessor
{
	public function __invoke(array $record)
	{
		foreach (['exception', 'error'] as $key) {
			if (isset($record['context'][$key]) && $record['context'][$key] instanceof Throwable) {
				$record['message'] = $record['message'] ?: self::formatMessage($record['context'][$key]);
				break;
			}
		}
		return $record;
	}

	/**
	 * @author David Grudl
	 * @see    https://github.com/nette/tracy
	 */
	public static function formatMessage(Throwable $message): string
	{
		$tmp = [];
		while ($message) {
			$tmp[] = ($message instanceof \ErrorException
				? Helpers::errorTypeToString($message->getSeverity()) . ': ' . $message->getMessage()
				: Helpers::getClass($message) . ': ' . $message->getMessage()
			) . ' in ' . $message->getFile() . ':' . $message->getLine();
			$message = $message->getPrevious();
		}
		$message = implode($tmp, "\ncaused by ");
		return trim($message);
	}
}
