<?php declare(strict_types=1);

namespace Swoft\WebSocket\Server\Annotation\Mapping;

use Doctrine\Common\Annotations\Annotation\Attribute;
use Doctrine\Common\Annotations\Annotation\Attributes;
use Doctrine\Common\Annotations\Annotation\Required;
use Doctrine\Common\Annotations\Annotation\Target;
use Swoft\WebSocket\Server\MessageParser\RawTextParser;

/**
 * Class WebSocket - mark an websocket module handler class
 *
 * @since 2.0
 *
 * @Annotation
 * @Target("CLASS")
 * @Attributes(
 *     @Attribute("name", type="string"),
 *     @Attribute("path", type="string"),
 *     @Attribute("controllers", type="array"),
 *     @Attribute("messageParser", type="string"),
 * )
 */
final class WsModule
{
    /**
     * Websocket route path.(it must unique in a application)
     *
     * @var string
     * @Required()
     */
    private $path = '/';

    /**
     * Module name.
     *
     * @var string
     */
    private $name = '';

    /**
     * Message controllers of the module
     *
     * @var string[]
     */
    private $controllers = [];

    /**
     * Message parser class for the module
     *
     * @var string
     */
    private $messageParser = RawTextParser::class;

    /**
     * Default message command. Format 'controller.action'
     *
     * @var string
     */
    private $defaultCommand = 'home.index';

    /**
     * Class constructor.
     *
     * @param array $values
     */
    public function __construct(array $values)
    {
        if (isset($values['value'])) {
            $this->path = (string)$values['value'];
        } elseif (isset($values['name'])) {
            $this->path = (string)$values['path'];
        }

        if (isset($values['name'])) {
            $this->name = (string)$values['name'];
        }

        if (isset($values['controllers'])) {
            $this->controllers = (array)$values['controllers'];
        }

        if (isset($values['messageParser'])) {
            $this->messageParser = $values['messageParser'];
        }

        if (isset($values['defaultCommand'])) {
            $this->defaultCommand = $values['defaultCommand'];
        }
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getMessageParser(): string
    {
        return $this->messageParser;
    }

    /**
     * @return string
     */
    public function getDefaultCommand(): string
    {
        return $this->defaultCommand;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string[]
     */
    public function getControllers(): array
    {
        return $this->controllers;
    }
}
