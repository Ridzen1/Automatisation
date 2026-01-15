<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class MyExtension extends AbstractExtension
{
	public function getName(): string
	{
		return 'my-extension';
	}

	public function getFunctions(): array
	{
		return [
			new TwigFunction('getEnvironmentVariable', [$this, 'getEnvironmentVariable']),
			// IMPORTANT : On ajoute 'is_safe' pour que le HTML des scripts ne soit pas bloqué par Twig
			new TwigFunction('getViteAssets', [$this, 'getViteAssets'], ['is_safe' => ['html']]),
		];
	}

	public function getEnvironmentVariable(string $varName): ?string
	{
		return $_ENV[$varName] ?? null;
	}

	public function manifest()
	{
		$json_file_path = __DIR__ . '/../../public/build/.vite/manifest.json';
		if (!file_exists($json_file_path)) {
			$json_file_path = __DIR__ . '/../../public/build/manifest.json';
		}

		if (file_exists($json_file_path)) {
			$json_data = file_get_contents($json_file_path);
			return json_decode($json_data, true);
		}

		return null;
	}

	public function getViteAssets(): string
	{
		$isProd = (isset($_ENV['ENV']) && $_ENV['ENV'] === 'prod');
		$manifest = $this->manifest();

		if ($isProd && $manifest) {
			$entryPoint = 'script.js';

			if (!isset($manifest[$entryPoint])) {
				return '';
			}

			$jsFile = $manifest[$entryPoint]['file'];
			$html = '<script type="module" src="/build/' . $jsFile . '"></script>';

			if (isset($manifest[$entryPoint]['css'])) {
				foreach ($manifest[$entryPoint]['css'] as $cssFile) {
					$html .= '<link rel="stylesheet" href="/build/' . $cssFile . '">';
				}
			}
			return $html;
		}

		return '
            <script type="module" src="http://localhost:3000/build/@vite/client"></script>
            <script type="module" src="http://localhost:3000/build/script.js"></script>
        ';
	}
}