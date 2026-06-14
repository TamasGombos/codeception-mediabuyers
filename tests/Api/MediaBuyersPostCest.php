<?php

declare(strict_types=1);

namespace Tests\Api;

use Tests\Support\ApiTester;
use Tests\Support\Helper\MediaBuyersPostHelper;

final class MediaBuyersPostCest
{
    public function _before(ApiTester $I): void
    {
        $I->setUpApiHeaders();
    }

    public function validateMediaBuyersHappyPath(ApiTester $I, MediaBuyersPostHelper $M)
    {
        $request = $M->generateMediaBuyersFixture('9001', 'TM', 'Test Media Buyer', 'test.media.buyer@example.com', 'U05AZ3DQBBKK', true);
        $response = $I->sendPost('/api/mediabuyers', $request);
        $I->validateResponseFormat();
        $I->validateResponseJsonSchema('post');
        $M->checkCreatedEntityIsTheSame($request, $response);
        $M->validateResponseValues($response);
    }

    public function validateMediaBuyersMissingNameFieldError(ApiTester $I, MediaBuyersPostHelper $M)
    {
        $request = $M->generateMediaBuyersFixture('9001', 'TM', '', 'test.media.buyer@example.com', 'U05AZ3DQBBKK', true);
        $response = $I->sendPost('/api/mediabuyerserror', $request);
        $I->validateErrorResponseFormat();
        $M->checkErrorMessages($response, ['This field is missing: [name]']);
    }

    public function validateMediaBuyersInactive(ApiTester $I, MediaBuyersPostHelper $M)
    {
        $request = $M->generateMediaBuyersFixture('9001', 'TM', 'Test Media Buyer', 'test.media.buyer@example.com', 'U05AZ3DQBBKK', false);
        $response = $I->sendPost('/api/mediabuyers', $request);
        $I->validateResponseFormat();
        $I->validateResponseJsonSchema('post');
        $M->checkCreatedEntityIsTheSame($request, $response);
        $M->validateResponseValues($response);
    }
}
