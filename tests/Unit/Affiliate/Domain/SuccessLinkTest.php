<?php

namespace Unit\Affiliate\Domain;

use Affiliate\Domain\SuccessLink;
use PHPUnit\Framework\TestCase;

class SuccessLinkTest extends TestCase
{
    public function test_GivenAllFields_ObjectIsCreatedSuccessfully()
    {
        $item = new SuccessLink(
            clientId: 'user15',
            partnerLink: 'https://ad.theirs1.com/?src=q1w2e3r4',
        );

        $this->assertInstanceOf(
            SuccessLink::class,
            $item
        );

        $this->assertEquals(
            'user15',
            $item->getClientId()
        );

        $this->assertEquals(
            'https://ad.theirs1.com/?src=q1w2e3r4',
            $item->getPartnerLink()
        );
    }
}
