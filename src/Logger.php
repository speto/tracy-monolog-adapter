<?php

/**
 * @license    New BSD License
 * @link       https://github.com/nextras/tracy-monolog-adapter
 */

namespace Nextras\TracyMonologAdapter;

use Monolog;
use Nextras\TracyMonologAdapter\Processors\TracyExceptionProcessor;
use Throwable;
use Tracy\Helpers;
use Tracy\ILogger;


class Logger implements ILogger
{
	/** @const Tracy priority to Monolog priority mapping */
	const PRIORITY_MAP = [
		self::DEBUG => Monolog\Logger::DEBUG,
		self::INFO => Monolog\Logger::INFO,
		self::WARNING => Monolog\Logger::WARNING,
		self::ERROR => Monolog\Logger::ERROR,
		self::EXCEPTION => Monolog\Logger::CRITICAL,
		self::CRITICAL => Monolog\Logger::CRITICAL,
	];

	/** @var Monolog\Logger */
	protected $monolog;


	public function __construct(Monolog\Logger $monolog)
	{
		$this->monolog = $monolog;
	}


	public function log($message, $priority = self::INFO)
	{
		$context = [
			'at' => Helpers::getSource(),
		];

		if ($message instanceof Throwable) {
			$context['exception'] = $message;
			$message = TracyExceptionProcessor::formatMessage($message);
		}

		$this->monolog->addRecord(
			self::PRIORITY_MAP[$priority] ?? Monolog\Logger::ERROR,
			$message,
			$context
		);
	}
}
