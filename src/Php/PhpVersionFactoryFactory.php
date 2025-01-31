<?php declare(strict_types = 1);

namespace PHPStan\Php;

use Nette\Utils\Json;
use PHPStan\File\FileReader;

class PhpVersionFactoryFactory
{

	private ?int $versionId;

	/** @var string[] */
	private array $composerAutoloaderProjectPaths;

	/**
	 * @param string[] $composerAutoloaderProjectPaths
	 */
	public function __construct(
		?int $versionId,
		array $composerAutoloaderProjectPaths
	)
	{
		$this->versionId = $versionId;
		$this->composerAutoloaderProjectPaths = $composerAutoloaderProjectPaths;
	}

	public function create(): PhpVersionFactory
	{
		$composerPhpVersion = null;
		if (count($this->composerAutoloaderProjectPaths) > 0) {
			$composerJsonPath = end($this->composerAutoloaderProjectPaths) . '/composer.json';
			if (is_file($composerJsonPath)) {
				try {
					$composerJsonContents = FileReader::read($composerJsonPath);
					$composer = Json::decode($composerJsonContents, Json::FORCE_ARRAY);
					$platformVersion = $composer['config']['platform']['php'] ?? null;
					if (is_string($platformVersion)) {
						$composerPhpVersion = $platformVersion;
					}
				} catch (\PHPStan\File\CouldNotReadFileException | \Nette\Utils\JsonException $e) {
					// pass
				}
			}
		}

		return new PhpVersionFactory($this->versionId, $composerPhpVersion);
	}

}
