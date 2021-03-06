<?php declare(strict_types=1);


namespace Swoft\Test\Concern;

use PHPUnit\Framework\Assert;
use Swoft\Stdlib\Helper\JsonHelper;

/**
 * Class HttpResponseAssertTrait
 *
 * @since 2.0
 */
trait HttpResponseAssertTrait
{
    /**
     * @return HttpResponseAssertTrait
     */
    public function assertSuccess(): self
    {
        Assert::assertTrue($this->status == self::STATUS_SUCCESS);

        return $this;
    }

    /**
     * @return HttpResponseAssertTrait
     */
    public function assertFail(): self
    {
        Assert::assertTrue($this->status != self::STATUS_SUCCESS);

        return $this;
    }

    /**
     * @param int $status
     *
     * @return HttpResponseAssertTrait
     */
    public function assertEqualStatus(int $status): self
    {
        Assert::assertTrue($status == $this->status);

        return $this;
    }

    /**
     * @param string $key
     * @param string $values
     *
     * @return HttpResponseAssertTrait
     */
    public function assertEqualHeader(string $key, string $values): self
    {
        Assert::assertTrue(isset($this->header[$key]));

        $headerLine = $this->header[$key] ?? '';
        Assert::assertEquals($values, $headerLine);

        return $this;
    }

    /**
     * @param array $headers
     *
     * @return HttpResponseAssertTrait
     */
    public function assertEqualHeaders(array $headers): self
    {
        Assert::assertEquals($this->getHeader(), $headers);

        return $this;
    }

    /**
     * @param string $key
     * @param string $value
     *
     * @return HttpResponseAssertTrait
     */
    public function assertContainHeader(string $key, string $value): self
    {
        Assert::assertTrue(isset($this->header[$key]));

        $headerLine = $this->header[$key] ?? '';
        $hasContain = \strpos($headerLine, $value) !== false;

        Assert::assertTrue($hasContain);
        return $this;
    }

    /**
     * @param string $content
     *
     * @return HttpResponseAssertTrait
     */
    public function assertEqualContent(string $content): self
    {
        Assert::assertEquals($content, $this->content);
        return $this;
    }

    /**
     * @param string $content
     *
     * @return HttpResponseAssertTrait
     */
    public function assertContainContent(string $content): self
    {
        Assert::assertTrue(\strpos($this->content, $content) !== false);
        return $this;
    }

    /**
     * @param string $content
     *
     * @return HttpResponseAssertTrait
     */
    public function assertNotEqualContent(string $content): self
    {
        Assert::assertNotEquals($content, $this->content);
        return $this;
    }

    /**
     * @param array $data
     *
     * @return HttpResponseAssertTrait
     */
    public function assertEqualJson(array $data): self
    {
        $result = JsonHelper::decode($this->content, true);
        Assert::assertEquals($result, $data);
        return $this;
    }
}