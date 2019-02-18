<?php declare(strict_types=1);


namespace Swoft\Db\Query;

use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Concern\Prototype;
use Swoft\Bean\PrototypeInterface;

/**
 * Class Expression
 *
 * @Bean(scope=Bean::PROTOTYPE)
 *
 * @since 2.0
 */
class Expression implements PrototypeInterface
{
    use Prototype;

    /**
     * The value of the expression.
     *
     * @var mixed
     */
    protected $value;

    /**
     * Create new expression
     *
     * @param mixed ...$params
     *
     * @return Expression
     * @throws \Swoft\Bean\Exception\PrototypeException
     */
    public static function new(...$params): self
    {
        list($value) = $params;
        $self = self::__instance();

        $self->value = $value;
        return $self;
    }

    /**
     * Get the value of the expression.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Get the value of the expression.
     *
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getValue();
    }
}