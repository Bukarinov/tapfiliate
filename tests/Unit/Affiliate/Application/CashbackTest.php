<?php

namespace Unit\Affiliate\Application;

use Affiliate\Application\Cashback;
use Affiliate\Domain\LogItem;
use Affiliate\Domain\SuccessLink;
use PHPUnit\Framework\TestCase;

class CashbackTest extends TestCase
{
    public function test_GivenOrganicTransition_ZeroSuccessLinksAreFound()
    {
        $cashback = new Cashback([
            new LogItem(
                clientId: 'user15',
                userAgent: 'Firefox 59',
                location: 'https://shop.com/products/?id=2',
                referer: 'https://yandex.ru/search/?q=купить+котика',
                dateTime: new \DateTime('2018-04-03T07:59:13.286000Z'),
            ),
        ]);

        $links = $cashback->getSuccessLinks(
            checkoutLink: 'https://shop.com/checkout',
            partnersHosts: [
                'ad.theirs1.com',
                'ad.theirs2.com',
                'referal.ours.com',
            ],
        );

        $this->assertEquals(
            [],
            $links
        );
    }

    public function test_GivenCheckoutWithoutReferralLink_ZeroSuccessLinksAreFound()
    {
        $cashback = new Cashback([
            // Checkout without a referral link
            new LogItem(
                clientId: 'user42',
                userAgent: 'Firefox 59',
                location: 'https://shop.com/checkout',
                referer: 'https://shop.com/products/?id=2',
                dateTime: new \DateTime('2018-04-04T08:59:16.222000Z'),
            ),
        ]);

        $links = $cashback->getSuccessLinks(
            checkoutLink: 'https://shop.com/checkout',
            partnersHosts: [
                'ad.theirs1.com',
                'ad.theirs2.com',
                'referal.ours.com',
            ],
        );

        $this->assertEquals(
            [],
            $links
        );
    }

    public function test_GivenContinuousUserPath_OneLastSuccessLinkIsFound()
    {
        $cashback = new Cashback([
            // The first referral link
            new LogItem(
                clientId: 'user15',
                userAgent: 'Firefox 59',
                location: 'https://shop.com/products/?id=2',
                referer: 'https://referal.ours.com/?ref=123hexcode',
                dateTime: new \DateTime('2018-04-04T08:30:14.104000Z'),
            ),
            // The last referral link
            new LogItem(
                clientId: 'user15',
                userAgent: 'Firefox 59',
                location: 'https://shop.com/products/?id=2',
                referer: 'https://ad.theirs1.com/?src=q1w2e3r4',
                dateTime: new \DateTime('2018-04-04T08:45:14.384000Z'),
            ),
            // Checkout with a referral link
            new LogItem(
                clientId: 'user15',
                userAgent: 'Firefox 59',
                location: 'https://shop.com/checkout',
                referer: 'https://shop.com/products/?id=2',
                dateTime: new \DateTime('2018-04-04T08:59:16.222000Z'),
            ),
        ]);

        $links = $cashback->getSuccessLinks(
            checkoutLink: 'https://shop.com/checkout',
            partnersHosts: [
                'ad.theirs1.com',
                'ad.theirs2.com',
                'referal.ours.com',
            ],
        );

        $expected = [
            new SuccessLink(
                clientId: 'user15',
                partnerLink: 'https://ad.theirs1.com/?src=q1w2e3r4',
            ),
        ];

        $this->assertEquals(
            $expected,
            $links
        );
    }

    public function test_GivenWayMoreContinuousUserPath_OneLastSuccessLinkIsFound()
    {
        $cashback = new Cashback([
            // The last referral link
            new LogItem(
                clientId: 'user15',
                userAgent: 'Firefox 59',
                location: 'https://shop.com/products/?id=2',
                referer: 'https://ad.theirs1.com/?src=q1w2e3r4',
                dateTime: new \DateTime('2018-04-04T08:45:14.384000Z'),
            ),
            // User path
            new LogItem(
                clientId: 'user15',
                userAgent: 'Firefox 59',
                location: 'https://shop.com/products',
                referer: 'https://shop.com/products/?id=2',
                dateTime: new \DateTime('2018-04-04T08:59:01.222000Z'),
            ),
            // User path
            new LogItem(
                clientId: 'user15',
                userAgent: 'Firefox 59',
                location: 'https://shop.com/',
                referer: 'https://shop.com/products',
                dateTime: new \DateTime('2018-04-04T08:59:10.222000Z'),
            ),
            // Checkout with a referral link
            new LogItem(
                clientId: 'user15',
                userAgent: 'Firefox 59',
                location: 'https://shop.com/checkout',
                referer: 'https://shop.com/',
                dateTime: new \DateTime('2018-04-04T08:59:16.222000Z'),
            ),
        ]);

        $links = $cashback->getSuccessLinks(
            checkoutLink: 'https://shop.com/checkout',
            partnersHosts: [
                'ad.theirs1.com',
                'ad.theirs2.com',
                'referal.ours.com',
            ],
        );

        $expected = [
            new SuccessLink(
                clientId: 'user15',
                partnerLink: 'https://ad.theirs1.com/?src=q1w2e3r4',
            ),
        ];

        $this->assertEquals(
            $expected,
            $links
        );
    }

    public function test_GivenReferralAndThenOrganicLinks_OneLastSuccessLinkIsFound()
    {
        $cashback = new Cashback([
            // The referral link
            new LogItem(
                clientId: 'user15',
                userAgent: 'Firefox 59',
                location: 'https://shop.com/products/?id=2',
                referer: 'https://ad.theirs1.com/?src=q1w2e3r4',
                dateTime: new \DateTime('2018-04-04T06:45:14.384000Z'),
            ),
            // An organic transition
            new LogItem(
                clientId: 'user15',
                userAgent: 'Firefox 59',
                location: 'https://shop.com/products/?id=2',
                referer: 'https://yandex.ru/search/?q=купить+котика',
                dateTime: new \DateTime('2018-04-03T07:59:13.286000Z'),
            ),
            // Checkout with a referral link
            new LogItem(
                clientId: 'user15',
                userAgent: 'Firefox 59',
                location: 'https://shop.com/checkout',
                referer: 'https://shop.com/products/?id=2',
                dateTime: new \DateTime('2018-04-04T08:59:16.222000Z'),
            ),
        ]);

        $links = $cashback->getSuccessLinks(
            checkoutLink: 'https://shop.com/checkout',
            partnersHosts: [
                'ad.theirs1.com',
                'ad.theirs2.com',
                'referal.ours.com',
            ],
        );

        $expected = [
            new SuccessLink(
                clientId: 'user15',
                partnerLink: 'https://ad.theirs1.com/?src=q1w2e3r4',
            ),
        ];

        $this->assertEquals(
            $expected,
            $links
        );
    }

    public function test_GivenDiscontinuousReferralLink_OneLastSuccessLinkIsFound()
    {
        $cashback = new Cashback([
            // The referral link
            new LogItem(
                clientId: 'user15',
                userAgent: 'Firefox 59',
                location: 'https://shop.com/products/?id=2',
                referer: 'https://ad.theirs1.com/?src=q1w2e3r4',
                dateTime: new \DateTime('2018-04-04T06:45:14.384000Z'),
            ),
            // An organic transition
            new LogItem(
                clientId: 'user15',
                userAgent: 'Firefox 59',
                location: 'https://shop.com/products',
                referer: 'https://yandex.ru/search/?q=купить+котика',
                dateTime: new \DateTime('2018-04-03T07:59:13.286000Z'),
            ),
            // Checkout without a referral link
            new LogItem(
                clientId: 'user15',
                userAgent: 'Firefox 59',
                location: 'https://shop.com/checkout',
                referer: 'https://shop.com/products',
                dateTime: new \DateTime('2018-04-04T08:59:16.222000Z'),
            ),
        ]);

        $links = $cashback->getSuccessLinks(
            checkoutLink: 'https://shop.com/checkout',
            partnersHosts: [
                'ad.theirs1.com',
                'ad.theirs2.com',
                'referal.ours.com',
            ],
        );

        $expected = [
            new SuccessLink(
                clientId: 'user15',
                partnerLink: 'https://ad.theirs1.com/?src=q1w2e3r4',
            ),
        ];

        $this->assertEquals(
            $expected,
            $links
        );
    }

    public function test_GivenSeveralUsersPath_SuccessLinkForEachUserAreFound()
    {
        $cashback = new Cashback([
            // The first referral link for User 15
            new LogItem(
                clientId: 'user15',
                userAgent: 'Firefox 59',
                location: 'https://shop.com/products/?id=2',
                referer: 'https://referal.ours.com/?ref=123hexcode',
                dateTime: new \DateTime('2018-04-04T08:30:14.104000Z'),
            ),
            // The first referral link for User 16
            new LogItem(
                clientId: 'user16',
                userAgent: 'Firefox 59',
                location: 'https://shop.com/products/?id=2',
                referer: 'https://referal.ours.com/?ref=123hexcode',
                dateTime: new \DateTime('2018-04-04T08:31:14.104000Z'),
            ),
            // The last referral link for User 15
            new LogItem(
                clientId: 'user15',
                userAgent: 'Firefox 59',
                location: 'https://shop.com/products/?id=2',
                referer: 'https://ad.theirs1.com/?src=q1w2e3r4',
                dateTime: new \DateTime('2018-04-04T08:45:14.384000Z'),
            ),
            // The last referral link for User 16
            new LogItem(
                clientId: 'user16',
                userAgent: 'Firefox 59',
                location: 'https://shop.com/products/?id=2',
                referer: 'https://ad.theirs1.com/?src=q1w2e3r4',
                dateTime: new \DateTime('2018-04-04T08:46:14.384000Z'),
            ),
            // Checkout with a referral link for User 15
            new LogItem(
                clientId: 'user15',
                userAgent: 'Firefox 59',
                location: 'https://shop.com/checkout',
                referer: 'https://shop.com/products/?id=2',
                dateTime: new \DateTime('2018-04-04T08:59:16.222000Z'),
            ),
            // Checkout with a referral link for User 16
            new LogItem(
                clientId: 'user16',
                userAgent: 'Firefox 59',
                location: 'https://shop.com/checkout',
                referer: 'https://shop.com/products/?id=2',
                dateTime: new \DateTime('2018-04-04T08:59:22.222000Z'),
            ),
        ]);

        $links = $cashback->getSuccessLinks(
            checkoutLink: 'https://shop.com/checkout',
            partnersHosts: [
                'ad.theirs1.com',
                'ad.theirs2.com',
                'referal.ours.com',
            ],
        );

        $expected = [
            new SuccessLink(
                clientId: 'user16',
                partnerLink: 'https://ad.theirs1.com/?src=q1w2e3r4',
            ),
            new SuccessLink(
                clientId: 'user15',
                partnerLink: 'https://ad.theirs1.com/?src=q1w2e3r4',
            ),
        ];

        $this->assertEquals(
            $expected,
            $links
        );
    }
}
