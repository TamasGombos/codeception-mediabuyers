<?php

declare(strict_types=1);

namespace Tests\Api;

use Tests\Support\ApiTester;
use Tests\Support\Helper\MediaBuyersGetHelper;

final class MediaBuyersGetCest
{
    public function _before(ApiTester $I): void
    {
        $I->setUpApiHeaders();
    }

    public function apiReturnsAValidJsonAgainstSchema(ApiTester $I): void
    {
        $I->sendGet('/api/mediabuyers');
        $I->validateResponseFormat();
        $I->validateResponseJsonSchema('get');
    }

    public function apiReturnsValidDataInFieldsNotSpecifiedBySchema(ApiTester $I, MediaBuyersGetHelper $M): void
    {
        $response = $I->sendGet('/api/mediabuyers');
        $M->validateResponseValues($response);
        $M->validateIdUniqueness($response);
    }
}
