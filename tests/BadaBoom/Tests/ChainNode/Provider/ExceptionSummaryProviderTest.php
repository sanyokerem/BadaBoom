<?php

namespace BadaBoom\Tests\ChainNode\Provider;

use BadaBoom\DataHolder\DataHolder;
use BadaBoom\ChainNode\Provider\ExceptionSummaryProvider;

class ExceptionSummaryProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     * @test
     */
    public function shouldBeSubclassOfAbstractProvider()
    {
        $rc = new \ReflectionClass('BadaBoom\ChainNode\Provider\ExceptionSummaryProvider');
        $this->assertTrue($rc->isSubclassOf('BadaBoom\ChainNode\Provider\AbstractProvider'));
    }

    /**
     *
     * @test
     */
    public function shouldSetDefaultSectionNameIfNotProvided()
    {
        $e = new \Exception('foo');
        $data = new DataHolder;

        $provider = new ExceptionSummaryProvider();
        $provider->handle($e, $data);

        $this->assertTrue($data->has('summary'));
    }

    /**
     *
     * @test
     */
    public function shouldUseCustomSectionNameIfProvided()
    {
        $e = new \Exception('foo');
        $data = new DataHolder();

        $provider = new ExceptionSummaryProvider('barSection');
        $provider->handle($e, $data);

        $this->assertTrue($data->has('barSection'));
        $this->assertFalse($data->has('summary'));
    }

    /**
     *
     * @test
     */
    public function shouldFillDataHolderWithExceptionInfo()
    {
        $e = new \Exception('foo', 123);
        $data = new DataHolder();

        $provider = new ExceptionSummaryProvider();
        $provider->handle($e, $data);

        $summary = $data->get('summary');
        $this->assertInternalType('array', $summary);

        $this->assertArrayHasKey('class', $summary);
        $this->assertArrayHasKey('code', $summary);
        $this->assertArrayHasKey('message', $summary);
        $this->assertArrayHasKey('file', $summary);

        $this->assertEquals('Exception', $summary['class']);
        $this->assertEquals(123, $summary['code']);
        $this->assertEquals('foo', $summary['message']);
        $this->assertContains('BadaBoom/Tests/ChainNode/Provider/ExceptionSummaryProviderTest.php', $summary['file']);
    }

    /**
     *
     * @test
     */
    public function shouldDelegateHandlingToNextChainNode()
    {
        $e = new \Exception;
        $data = new DataHolder;

        $nextChainNode = $this->createMockChainNode();
        $nextChainNode->expects($this->once())->method('handle')->with($this->equalTo($e), $this->equalTo($data));

        $provider = new ExceptionSummaryProvider();
        $provider->nextNode($nextChainNode);

        $provider->handle($e, $data);
    }

    protected function createMockChainNode()
    {
        return $this->getMockForAbstractClass('BadaBoom\ChainNode\AbstractChainNode');
    }
}