<?php declare(strict_types=1);


namespace Swoft\Test\Concern;


use PHPUnit\Framework\Assert;

trait RpcResponseAssertTrait
{
    /**
     * @var \Swoft\Rpc\Response
     */
    protected $returnResponse;

    /**
     * Assert
     */
    public function assertSuccess(): void
    {
        Assert::assertTrue($this->returnResponse->getError() == null);
    }

    /**
     * Assert
     */
    public function assertFail(): void
    {
        Assert::assertTrue($this->returnResponse->getError() != null);
    }

    /**
     * @param int $code
     */
    public function assertErrorCode(int $code): void
    {
        $error = $this->returnResponse->getError();
        if ($error === null) {
            Assert::assertTrue(false);
            return;
        }

        $errorCode = $error->getCode();
        Assert::assertEquals($code, $errorCode);
    }

    /**
     * @param string $message
     */
    public function assertErrorMessage(string $message): void
    {
        $error = $this->returnResponse->getError();
        if ($error === null) {
            Assert::assertTrue(false);
            return;
        }

        $errorMessage = $error->getMessage();
        Assert::assertEquals($message, $errorMessage);
    }

    /**
     * @param string $message
     */
    public function assertContainErrorMessage(string $message): void
    {
        $error = $this->returnResponse->getError();
        if ($error === null) {
            Assert::assertTrue(false);
            return;
        }

        $errorMessage = $error->getMessage();
        Assert::assertTrue(\strpos($errorMessage, $message) !== false);
    }

    /**
     * @param array $data
     */
    public function assertEqualJsonResult(array $data): void
    {
        Assert::assertEquals($data, $this->returnResponse->getResult());
    }

    /**
     * @param mixed $result
     */
    public function assertEqualResult($result): void
    {
        Assert::assertEquals($result, $this->returnResponse->getResult());
    }
}