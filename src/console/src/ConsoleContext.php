<?php declare(strict_types=1);

namespace Swoft\Console;

use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Concern\PrototypeTrait;
use Swoft\Console\Input\Input;
use Swoft\Console\Output\Output;
use Swoft\Context\AbstractContext;

/**
 * Class ConsoleContext
 * @since 2.0
 * @Bean(scope=Bean::PROTOTYPE)
 */
class ConsoleContext extends AbstractContext
{
    use PrototypeTrait;
    /**
     * @return ConsoleContext
     * @throws \Throwable
     */
    public static function new(): self
    {
        /** @var self $ctx */
        $ctx = self::__instance();

        $ctx->setMulti([
            'parentid' => '',
            'spanid'   => \uniqid('', false),
            'traceid'  => \uniqid('', false),
        ]);

        return $ctx;
    }

    /**
     * @return Input
     * @throws \Throwable
     */
    public function getInput(): Input
    {
        return \Swoft::getSingleton('input');
    }

    /**
     * @return Output
     * @throws \Throwable
     */
    public function getOutput(): Output
    {
        return \Swoft::getSingleton('output');
    }
}
