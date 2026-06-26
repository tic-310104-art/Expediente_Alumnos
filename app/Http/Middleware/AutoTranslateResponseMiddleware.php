<?php

namespace App\Http\Middleware;

use Closure;
use DOMDocument;
use DOMXPath;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AutoTranslateResponseMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (app()->getLocale() !== 'en') {
            return $response;
        }

        $contentType = (string) $response->headers->get('Content-Type', '');
        if (!str_contains($contentType, 'text/html')) {
            return $response;
        }

        $html = $response->getContent();
        if (!is_string($html) || $html === '') {
            return $response;
        }

        static $keys = null;
        static $vals = null;
        if ($keys === null || $vals === null) {
            $path = base_path('lang/en.json');
            $json = is_file($path) ? file_get_contents($path) : false;
            $decoded = is_string($json) ? json_decode($json, true) : null;
            $map = is_array($decoded) ? $decoded : [];
            $keys = array_keys($map);
            usort($keys, fn ($a, $b) => strlen((string) $b) <=> strlen((string) $a));
            $vals = array_map(fn ($k) => $map[$k], $keys);
        }

        if (!$keys || !$vals) {
            return $response;
        }

        $translated = $this->translateHtml($html, $keys, $vals);
        if (is_string($translated) && $translated !== '') {
            $response->setContent($translated);
        }

        return $response;
    }

    private function translateHtml(string $html, array $keys, array $vals): string
    {
        $dom = new DOMDocument();
        $prev = libxml_use_internal_errors(true);
        $dom->loadHTML($html, LIBXML_NOERROR | LIBXML_NOWARNING | LIBXML_NONET);
        libxml_clear_errors();
        libxml_use_internal_errors($prev);

        $xpath = new DOMXPath($dom);

        foreach ($xpath->query('//text()[normalize-space()]') as $textNode) {
            $parent = $textNode->parentNode ? strtolower((string) $textNode->parentNode->nodeName) : '';
            if (in_array($parent, ['script', 'style', 'noscript'], true)) {
                continue;
            }
            $textNode->nodeValue = str_replace($keys, $vals, (string) $textNode->nodeValue);
        }

        $attrQuery = '//@placeholder | //@title | //@aria-label | //@alt';
        foreach ($xpath->query($attrQuery) as $attrNode) {
            $owner = $attrNode->ownerElement ? strtolower((string) $attrNode->ownerElement->nodeName) : '';
            if (in_array($owner, ['script', 'style', 'noscript'], true)) {
                continue;
            }
            $attrNode->value = str_replace($keys, $vals, (string) $attrNode->value);
        }

        $out = $dom->saveHTML();
        return is_string($out) ? $out : $html;
    }
}

