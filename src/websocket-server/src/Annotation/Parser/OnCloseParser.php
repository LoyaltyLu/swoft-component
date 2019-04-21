<?php declare(strict_types=1);

namespace Swoft\WebSocket\Server\Annotation\Parser;

use Swoft\Annotation\Annotation\Mapping\AnnotationParser;
use Swoft\Annotation\Annotation\Parser\Parser;
use Swoft\Annotation\Exception\AnnotationException;
use Swoft\Server\Swoole\SwooleEvent;
use Swoft\WebSocket\Server\Annotation\Mapping\OnClose;
use Swoft\WebSocket\Server\Router\RouteRegister;

/**
 * Class OnCloseParser
 * @since 2.0
 * @AnnotationParser(OnClose::class)
 */
class OnCloseParser extends Parser
{
    /**
     * Parse object
     *
     * @param int     $type       Class or Method or Property
     * @param OnClose $annotation Annotation object
     *
     * @return array
     * Return empty array is nothing to do!
     * When class type return [$beanName, $className, $scope, $alias, $size] is to inject bean
     * When property type return [$propertyValue, $isRef] is to reference value
     */
    public function parse(int $type, $annotation): array
    {
        if ($type !== self::TYPE_METHOD) {
            throw new AnnotationException('`@OnClose` must be defined on class method!');
        }

        RouteRegister::bindEvent($this->className, $this->methodName, SwooleEvent::CLOSE);

        return [];
    }
}
