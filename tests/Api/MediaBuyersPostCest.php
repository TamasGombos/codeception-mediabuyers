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
        // Validates HTTP status code, response structure, schema and entity values in case of Happy Path
        $request = $M->generateMediaBuyersFixture('9001', 'TM', 'Test Media Buyer', 'test.media.buyer@example.com', 'U05AZ3DQBBKK', true);
        $response = $I->sendPost('/api/mediabuyers', $request);
        $I->validateResponseFormat();
        $I->validateResponseJsonSchema('post');
        $M->checkCreatedEntityIsTheSame($request, $response);
        $M->validateResponseValues($response);
    }

    public function validateMediaBuyersMissingNameFieldError(ApiTester $I, MediaBuyersPostHelper $M)
    {
        // Validates missing name scenario
        $request = $M->generateMediaBuyersFixture('9001', 'TM', '', 'test.media.buyer@example.com', 'U05AZ3DQBBKK', true);
        $response = $I->sendPost('/api/mediabuyers', $request);
        $I->validateErrorResponseFormat(400);
        $M->checkErrorMessages($response, ['This field is missing: [name]']);
    }

    public function validateMediaBuyersInactive(ApiTester $I, MediaBuyersPostHelper $M)
    {
        // Validates setting a mediaBuyer to inactive
        $request = $M->generateMediaBuyersFixture('9001', 'TM', 'Test Media Buyer', 'test.media.buyer@example.com', 'U05AZ3DQBBKK', false);
        $response = $I->sendPost('/api/mediabuyers', $request);
        $I->validateResponseFormat();
        $I->validateResponseJsonSchema('post');
        $M->checkCreatedEntityIsTheSame($request, $response);
        $M->validateResponseValues($response);
    }

    public function validateInvalidEmailAddress(ApiTester $I, MediaBuyersPostHelper $M)
    {
        // Validates invalid email scenario
        $request = $M->generateMediaBuyersFixture('9001', 'TM', 'Test Media Buyer', 'not-an-email', 'U05AZ3DQBBKK', true);
        $response = $I->sendPost('/api/mediabuyers', $request);
        $I->validateErrorResponseFormat(400);
        $M->checkErrorMessages($response, ['The email not-an-email is not a valid email.']);
    }

    public function validateInitialsTooLong(ApiTester $I, MediaBuyersPostHelper $M)
    {
        // Validates initials being too long scenario
        $request = $M->generateMediaBuyersFixture('9001', 'TOO LONG', 'Test Media Buyer', 'test.media.buyer@example.com', 'U05AZ3DQBBKK', true);
        $response = $I->sendPost('/api/mediabuyers', $request);
        $I->validateErrorResponseFormat(400);
        $M->checkErrorMessages($response, ['The initials must be exactly 2 characters long.']);
    }

    public function validateNameTooShort(ApiTester $I, MediaBuyersPostHelper $M)
    {
        // Validates name being too long scenario
        $request = $M->generateMediaBuyersFixture('9001', 'TM', 'A', 'test.media.buyer@example.com', 'U05AZ3DQBBKK', true);
        $response = $I->sendPost('/api/mediabuyers', $request);
        $I->validateErrorResponseFormat(400);
        $M->checkErrorMessages($response, ['The name must be at least 2 characters long.']);
    }

    public function validateInvalidMbId(ApiTester $I, MediaBuyersPostHelper $M)
    {
        // Validates invalid mbId scenario
        $request = $M->generateMediaBuyersFixture('abc', 'TM', 'Test Media Buyer', 'test.media.buyer@example.com', 'U05AZ3DQBBKK', true);
        $response = $I->sendPost('/api/mediabuyers', $request);
        $I->validateErrorResponseFormat(400);
        $M->checkErrorMessages($response, ['The mbId must be a positive integer value.']);
    }

    public function validateInvalidActiveStatus(ApiTester $I, MediaBuyersPostHelper $M)
    {
        // Validates active status being sent as a string instead of a bool
        $request = $M->generateMediaBuyersInvalidFixture('9001', 'TM', 'Test Media Buyer', 'test.media.buyer@example.com', 'U05AZ3DQBBKK', 'yes');
        $response = $I->sendPost('/api/mediabuyers', $request);
        $I->validateErrorResponseFormat(400);
        $M->checkErrorMessages($response, ['The email not-an-email is not a valid email.']);
    }

    public function validateDuplicateMediaBuyers(ApiTester $I, MediaBuyersPostHelper $M)
    {
        // Validates duplicate entities being created
        // Creating the first mediaBuyer
        $request = $M->generateMediaBuyersFixture('9001', 'TM', 'Test Media Buyer', 'test.media.buyer@example.com', 'U05AZ3DQBBKK', true);
        $response = $I->sendPost('/api/mediabuyers', $request);
        $I->validateResponseFormat();
        $I->validateResponseJsonSchema('post');
        $M->checkCreatedEntityIsTheSame($request, $response);
        $M->validateResponseValues($response);

        // Creating the same mediaBuyer once again, and expecting an error
        $requestDuplicate = $M->generateMediaBuyersFixture('9001', 'TM', 'Test Media Buyer', 'test.media.buyer@example.com', 'U05AZ3DQBBKK', true);
        $responseDuplicate = $I->sendPost('/api/mediabuyers', $requestDuplicate);
        $I->validateErrorResponseFormat(409);
        $M->checkErrorMessages($responseDuplicate, ['mediaBuyer with id 9001 already exists!']);
    }
}
