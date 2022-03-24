<?php

namespace Unit\Model\Controller\Roles;

use App\Model\Controller\Roles\ListResponse;
use Hippy\Api\Repository\ListResult;
use Hippy\Model\Collection;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/** @coversDefaultClass \App\Model\Controller\Roles\ListResponse */
class ListResponseTest extends TestCase
{
    /**
     * @return void
     * @covers ::__construct
     */
    public function testThrowsIfInvalidCollection(): void
    {
        $collection = $this->createMock(Collection::class);
        $listResult = $this->createMock(ListResult::class);
        $listResult->expects($this->once())->method('getCollection')->willReturn($collection);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('invalid collection type');

        new ListResponse($listResult);
    }
}
