<?php

namespace Tests\Unit\GlobalId;

use Nuwave\Lighthouse\GlobalId\GlobalId;
use Tests\TestCase;

class GlobalIdTest extends TestCase
{
    /**
     * @var \Nuwave\Lighthouse\GlobalId\GlobalId
     */
    protected $globalIdResolver;

    public function setUp(): void
    {
        parent::setUp();

        $this->globalIdResolver = new GlobalId;
    }

    public function testHandleGlobalIds(): void
    {
        $globalId = $this->globalIdResolver->encode('User', 'asdf');
        $idParts = $this->globalIdResolver->decode($globalId);

        $this->assertSame(['User', 'asdf'], $idParts);
    }

    public function testDecodeJustTheId(): void
    {
        $globalId = $this->globalIdResolver->encode('User', 123);

        $this->assertSame('123', $this->globalIdResolver->decodeID($globalId));
    }

    public function testDecodeJustTheType(): void
    {
        $globalId = $this->globalIdResolver->encode('User', 123);

        $this->assertSame('User', $this->globalIdResolver->decodeType($globalId));
    }
}
