<?php

namespace Unit\Affiliate\Domain;

use Affiliate\Domain\LogItem;
use PHPUnit\Framework\TestCase;

class LogItemTest extends TestCase
{
    public function test_GivenAllFields_ObjectIsCreatedSuccessfully()
    {
        $dateTime = new \DateTime('2018-04-04T08:45:14.384000Z');
        $item = new LogItem(
            clientId: 'user15',
            userAgent: 'Firefox 59',
            location: 'https://shop.com/products/?id=2',
            referer: 'https://ad.theirs1.com/?src=q1w2e3r4',
            dateTime: $dateTime,
        );

        $this->assertInstanceOf(
            LogItem::class,
            $item
        );

        $this->assertEquals(
            'user15',
            $item->getClientId()
        );

        $this->assertEquals(
            'Firefox 59',
            $item->getUserAgent()
        );

        $this->assertEquals(
            'https://shop.com/products/?id=2',
            $item->getLocation()
        );

        $this->assertEquals(
            'https://ad.theirs1.com/?src=q1w2e3r4',
            $item->getReferer()
        );

        $this->assertEquals(
            $dateTime,
            $item->getDateTime()
        );
    }
}
