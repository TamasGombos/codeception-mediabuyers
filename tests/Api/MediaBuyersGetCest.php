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
        // Validates HTTP headers, status code, data structure, and schema of the response
        $I->sendGet('/api/mediabuyers');
        $I->validateResponseFormat();
        $I->validateResponseJsonSchema('get');
    }

    public function apiReturnsValidDataInFieldsNotSpecifiedBySchema(ApiTester $I, MediaBuyersGetHelper $M): void
    {
        // Validates values types and uniqueness of id, mbId
        $response = $I->sendGet('/api/mediabuyers');
        $M->validateResponseValues($response);
        $M->validateIdUniqueness($response);
    }

    public function validateNonExistentApiEndpoint(ApiTester $I): void
    {
        // Validates HTTP 404 in case of non-existent endpoint
        $I->sendGet('/api/nonexistent');
        $I->validateErrorResponseFormat(404);
    }
}
