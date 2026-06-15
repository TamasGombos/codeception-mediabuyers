<?php

declare(strict_types=1);

namespace Tests\Support\Helper;

use Tests\Support\Helper\MediaBuyersFixture;
use Codeception\Event\FailEvent;

class MediaBuyersPostHelper extends \Codeception\Module
{
    /**
     * Validates 'id', 'initials' and 'slackUserId' values in the response JSON
     *
     * @param string $response The Response JSON
     *
     * @throws FailEvent In case 'id', 'initials' or 'slackUserId' is incorrect
     */
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

    /**
     * Helper function to generate a mediaBuyers object
     *
     * @param string $mbId mbId
     * @param string $initials initials
     * @param string $name name
     * @param string $email email
     * @param string $slackUserId slackUserId
     * @param bool $active active
     *
     * @return string $mediaBuyer
     */
    public function generateMediaBuyersFixture(string $mbId, string $initials, string $name, string $email, string $slackUserId, bool $active)
    {
        return json_encode(new MediaBuyersFixture($mbId, $initials, $name, $email, $slackUserId, $active));
    }

    /**
     * Helper function to generate an invalid mediaBuyers object
     *
     * @param string $mbId mbId
     * @param string $initials initials
     * @param string $name name
     * @param string $email email
     * @param string $slackUserId slackUserId
     * @param string $active active (as a string, as an invalid value)
     *
     * @return string $mediaBuyer
     */
    public function generateMediaBuyersInvalidFixture(string $mbId, string $initials, string $name, string $email, string $slackUserId, string $active)
    {
        return json_encode(new MediaBuyersFixture($mbId, $initials, $name, $email, $slackUserId, $active));
    }

    /**
     * Validates all 'errors[]->details' in response JSON
     *
     * @param string $response The Response JSON
     * @param array $errorMessageArray An array of strings for the expected error messages
     *
     * @throws FailEvent In case not all errors inside $errorMessageArray were present in the response
     */
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

    /**
     * Validates all fields in response JSON against what was sent in the request
     *
     * @param string $request The Request JSON
     * @param string $response The Response JSON
     *
     * @throws FailEvent In case the returned JSON does not have the same content as the request
     */
    public function checkCreatedEntityIsTheSame(string $request, string $response)
    {
        // TODO Fix ugly solution, preferably with using Codeception assertions
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
