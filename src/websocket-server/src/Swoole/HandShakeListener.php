<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2019-02-04
 * Time: 13:01
 */

namespace Swoft\WebSocket\Server\Swoole;


use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Co;
use Swoft\Connection\Connections;
use Swoft\Http\Message\Request as Psr7Request;
use Swoft\Server\Swoole\HandShakeInterface;
use Swoft\WebSocket\Server\Connection;
use Swoft\WebSocket\Server\Contract\WsModuleInterface;
use Swoft\WebSocket\Server\Dispatcher;
use Swoft\WebSocket\Server\Helper\WsHelper;
use Swoft\WebSocket\Server\WsEvent;
use Swoole\Http\Request;
use Swoole\Http\Response;

/**
 * Class HandShakeListener
 * @since 2.0
 * @package Swoft\WebSocket\Server\Swoole
 *
 * @Bean("handShakeListener")
 */
class HandShakeListener implements HandShakeInterface
{
    /**
     * HandShake event
     *
     * @param Request  $request
     * @param Response $response
     * @return bool
     * @throws \ReflectionException
     * @throws \Swoft\Bean\Exception\ContainerException
     */
    public function onHandShake(Request $request, Response $response): bool
    {
        $fd       = $request->fd;
        $secWSKey = $request->header['sec-websocket-key'];

        // sec-websocket-key 错误
        if (WsHelper::isInvalidSecWSKey($secWSKey)) {
            \server()->log("Handshake: shake hands failed with the #$fd. 'sec-websocket-key' is error!");
            return false;
        }

        // bind fd
        Connections::bindFd($fd);

        // Initialize psr7 Request and Response and metadata
        $cid = Co::tid();

        // get a clean connection instance.
        /** @var Connection $conn */
        $conn = \bean(Connection::class);
        // initialize connection
        $conn->initialize($fd, $request);

        $psr7Req = Psr7Request::loadFromSwooleRequest($request);
        $psr7Res = new \Swoft\Http\Message\Response($response);

        // bind connection
        Connections::set($fd, $conn);

        \Swoft::trigger(WsEvent::ON_HANDSHAKE, $fd, $request, $response);

        /** @var Dispatcher $dispatcher */
        $dispatcher = \bean('wsDispatcher');

        /** @var \Swoft\Http\Message\Response $psr7Res */
        [$status, $psr7Res] = $dispatcher->handshake($psr7Req, $psr7Res);

        $meta = $conn->getMetadata();

        // handshake check is failed -- 拒绝连接，比如需要认证，限定路由，限定ip，限定domain等
        if (WsModuleInterface::ACCEPT !== $status) {
            \server()->log("Client #$fd handshake check failed, request path {$meta['path']}");
            $psr7Res->send();

            Connections::unbindFd();
            // NOTICE: Rejecting a handshake still triggers a close event.
            return false;
        }

        // setting response
        $psr7Res = $psr7Res->withStatus(101)->withHeaders(WsHelper::handshakeHeaders($secWSKey));

        if (isset($request->header['sec-websocket-protocol'])) {
            $psr7Res = $psr7Res->withHeader('Sec-WebSocket-Protocol', $request->header['sec-websocket-protocol']);
        }

        // $this->log("Handshake: response headers:\n", $psr7Res->getHeaders());

        // Response handshake successfully
        $psr7Res->send();

        // mark handshake is ok
        $conn->setHandshake(true);

        \server()->log(
            "Handshake: Client #{$fd} handshake successful! path {$meta['path']}, co Id #$cid, Meta:",
            $meta,
            'debug'
        );

        // Handshaking successful, Manually triggering the open event
        \server()->getSwooleServer()->defer(function () use ($psr7Req, $fd) {
            $this->onOpen($psr7Req, $fd);
        });

        // unbind fd
        Connections::unbindFd();
        return true;
    }

    /**
     * @param Psr7Request $request
     * @param int         $fd
     * @throws \ReflectionException
     * @throws \Swoft\Bean\Exception\ContainerException
     */
    public function onOpen(Psr7Request $request, int $fd): void
    {
        $server = \server()->getSwooleServer();

        \Swoft::trigger(WsEvent::ON_OPEN, null, $server, $request, $fd);

        \server()->log("connection #$fd has been opened, co ID #" . Co::tid(), [], 'debug');

        /** @see Dispatcher::open() */
        \bean('wsDispatcher')->open($server, $request, $fd);
    }
}