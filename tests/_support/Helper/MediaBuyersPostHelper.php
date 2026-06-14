<?php

declare(strict_types=1);

namespace Tests\Support\Helper;

use Tests\Support\Helper\MediaBuyersFixture;

class MediaBuyersPostHelper extends \Codeception\Module
{
    public function validateResponseValues(string $response)
    {
        $responseJson = json_decode($response);
        $id = $responseJson->{'data'}->{'id'};
        $initials = $responseJson->{'data'}->{'initials'};
        $slackUserIdId = $responseJson->{'data'}->{'slackUserId'};

        if (!is_integer($id) || intval($id) < 1) {
            $this->fail("'id' in the response should be a positive integer!\nGot: $id");
        }

        if (!ctype_upper($initials)) {
            $this->fail("'initials' in the response should be uppercase characters!\nGot: $initials");
        }

        if (strlen($slackUserIdId) < 1 || strlen($slackUserIdId) > 32) {
            $this->fail("'slackUserId' in the response should be between 1 and 32 characters long!\nGot: $slackUserIdId");
        }
    }

    public function generateMediaBuyersFixture(string $mbId, string $initials, string $name, string $email, string $slackUserId, bool $active)
    {
        return json_encode(new MediaBuyersFixture($mbId, $initials, $name, $email, $slackUserId, $active));
    }

    public function checkErrorMessages(string $response, array $errorMessageArray)
    {
        $responseJson = json_decode($response);

        for ($i = 0; $i < count($responseJson->{'errors'}); $i++) {
            for ($j = 0; $j < count($errorMessageArray); $j++) {
                if ($responseJson->{'errors'}[$i]->{'detail'} == $errorMessageArray[$j]) {
                    unset($errorMessageArray[$j]);
                }
            }
        }

        if (count($errorMessageArray) > 0) {
            $remainingErrors = implode(", ", $errorMessageArray);
            $this->fail("Not all error messages were found in the response!\nRemained: $remainingErrors");
        }
    }

    public function checkCreatedEntityIsTheSame(string $request, string $response)
    {
        // TODO Fix ugly solution
        $responseJson = json_decode($response);
        $mbId = $responseJson->{'data'}->{'mbId'};
        $initials = $responseJson->{'data'}->{'initials'};
        $name = $responseJson->{'data'}->{'name'};
        $email = $responseJson->{'data'}->{'email'};
        $slackUserId = $responseJson->{'data'}->{'slackUserId'};
        $active = $responseJson->{'data'}->{'active'};
        $active = $active ? 'true' : 'false';

        $comparisonResponse = "{\"mbId\":\"$mbId\",\"initials\":\"$initials\",\"name\":\"$name\",\"email\":\"$email\",\"slackUserId\":\"$slackUserId\",\"active\":$active}";

        codecept_debug($request);
        codecept_debug($comparisonResponse);
        if (!strcmp($request, $comparisonResponse) == 0) {
            $this->fail("The entity in the response is not the same as what was sent!");
        }
    }
}
