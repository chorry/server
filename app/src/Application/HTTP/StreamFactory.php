<?php

declare(strict_types=1);

namespace App\Application\HTTP;

use GuzzleHttp\Psr7\Stream as PsrStream;
use Http\Message\Encoding\GzipDecodeStream;
use Psr\Http\Message\ServerRequestInterface;

final class StreamFactory
{
    public function createFromRequest(ServerRequestInterface $request): Stream
    {
        $content = (string)$request->getBody();

        $resource = fopen('php://temp', 'r+');
        fwrite($resource, $content);
        rewind($resource);

        $contentEncoding = $request->getHeaderLine('Content-Encoding');

        return match ($contentEncoding) {
            'gzip' => new Stream(
                new GzipDecodeStream(new PsrStream($resource))
            ),
            default => new Stream(new PsrStream($resource)),
        };
    }
}
