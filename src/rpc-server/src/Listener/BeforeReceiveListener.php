<?php declare(strict_types=1);


namespace Swoft\Rpc\Server\Listener;


use Swoft\Context\Context;
use Swoft\Event\Annotation\Mapping\Listener;
use Swoft\Event\EventHandlerInterface;
use Swoft\Event\EventInterface;
use Swoft\Log\Helper\Log;
use Swoft\Rpc\Server\Request;
use Swoft\Rpc\Server\Response;
use Swoft\Rpc\Server\ServiceContext;
use Swoft\Rpc\Server\ServiceServerEvent;

/**
 * Class BeforeReceiveListener
 *
 * @since 2.0
 *
 * @Listener(event=ServiceServerEvent::BEFORE_RECEIVE)
 */
class BeforeReceiveListener implements EventHandlerInterface
{
    /**
     * @param EventInterface $event
     *
     * @throws \ReflectionException
     * @throws \Swoft\Bean\Exception\ContainerException
     */
    public function handle(EventInterface $event): void
    {
        /**
         * @var Request  $request
         * @var Response $response
         */
        [$request, $response] = $event->getParams();
        $serviceContext = ServiceContext::new($request, $response);

        if (Log::getLogger()->isEnable()) {
            $data = [
                'traceid'     => $request->getExtKey('traceid', ''),
                'spanid'      => $request->getExtKey('spanid', ''),
                'parentid'    => $request->getExtKey('parentid', ''),
                'uri'         => sprintf('%s::%s', $request->getInterface(), $request->getMethod()),
                'requestTime' => $request->getRequestTime(),
            ];

            $serviceContext->setMulti($data);
        }

        Context::set($serviceContext);
    }
}