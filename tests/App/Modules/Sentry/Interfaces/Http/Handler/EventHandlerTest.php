<?php

declare(strict_types=1);

namespace Tests\App\Modules\Sentry\Interfaces\Http\Handler;

use App\Application\HTTP\StreamFactory;
use Modules\Sentry\Application\EventHandlerInterface;
use Modules\Sentry\Interfaces\Http\Handler\EventHandler;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Spiral\Cqrs\CommandBusInterface;
use Spiral\Files\FilesInterface;
use Spiral\Http\ResponseWrapper;

class EventHandlerTest extends TestCase
{
    public function testHandleGzippedEventData()
    {
        $factory = new StreamFactory;
        $eventHandler = $this->createMock(EventHandlerInterface::class);
        $commandBus = $this->createMock(CommandBusInterface::class);

        $handler = new EventHandler(
            $factory,
            new ResponseWrapper(
                $this->createMock(ResponseFactoryInterface::class),
                $this->createMock(StreamFactoryInterface::class),
                $this->createMock(FilesInterface::class),
            ),
            $eventHandler,
            $commandBus,
        );
        $body = '{"event_id": "81d68250-ebb4-461a-935f-1d9df8309f7f", "level": "Error", "platform": "other", "environment": "None"}';
        $body = gzencode($body);
        $serverRequest = new ServerRequest('POST', 'http://sentry@127.0.0.1:3001/1/store', [
            'Content-Encoding' => 'gzip'
        ], $body);


        $commandBus->expects($this->once())->method('dispatch');
        $handler->handle($serverRequest, fn(ServerRequestInterface $request) => new Response());
    }

    public function testHandlePlaintextEventData()
    {
        $factory = new StreamFactory;
        $eventHandler = $this->createMock(EventHandlerInterface::class);
        $commandBus = $this->createMock(CommandBusInterface::class);

        $handler = new EventHandler(
            $factory,
            new ResponseWrapper(
                $this->createMock(ResponseFactoryInterface::class),
                $this->createMock(StreamFactoryInterface::class),
                $this->createMock(FilesInterface::class),
            ),
            $eventHandler,
            $commandBus,
        );
        $body = '{"event_id": "81d68250-ebb4-461a-935f-1d9df8309f7f", "level": "Error", "platform": "other", "environment": "None"}';
        $serverRequest = new ServerRequest('POST', 'http://sentry@127.0.0.1:3001/1/store', [], $body);


        $commandBus->expects($this->once())->method('dispatch');
        $handler->handle($serverRequest, fn(ServerRequestInterface $request) => new Response());
    }
}
