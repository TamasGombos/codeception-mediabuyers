<?php

declare(strict_types=1);

namespace Tests\Support\Helper;

use Codeception\Event\FailEvent;

class MediaBuyersGetHelper extends \Codeception\Module
{
    /**
     * Validates all 'initials' and 'slackUserId' values in the response JSON
     *
     * @param string $response The Response JSON
     *
     * @throws FailEvent case 'initials' or 'slackUserId' is incorrect
     */
    public function validateResponseValues(string $response)
    {
        $initialsArray = $this->prepareWorkArrayOfItems($response, 'initials');
        $slackUserIdIdArray = $this->prepareWorkArrayOfItems($response, 'slackUserId');

        foreach ($initialsArray as $item) {
            if (!ctype_upper($item)) {
                $this->fail("'initials' in the response should be uppercase characters!\nGot: $item");
            }
        }

        foreach ($slackUserIdIdArray as $item) {
            if (strlen($item) < 1 || strlen($item) > 32) {
                $this->fail("'slackUserId' in the response should be between 1 and 32 characters long!\nGot: $item");
            }
        }
    }

    /**
     * Validates all 'id' and 'mbId' value uniqueness in response JSON
     *
     * @param string $response The Response JSON
     *
     * @throws FailEvent In case not all 'id' or 'mbId' is not unique
     */
    public function validateIdUniqueness(string $response)
    {
        $idArray = $this->prepareWorkArrayOfItems($response, 'id');
        $mbIdArray = $this->prepareWorkArrayOfItems($response, 'mbId');

        $countedIdArray = array_count_values($idArray);
        $countedMbIdArray = array_count_values($mbIdArray);

        foreach ($countedIdArray as $item) {
            if ($item > 1) {
                $duplicateKey = array_search($item, $countedIdArray);
                $this->fail("'id's in the response should be unique!\nGot: $item occurrences of $duplicateKey");
            }
        }

        foreach ($countedMbIdArray as $item) {
            if ($item > 1) {
                $duplicateKey = array_search($item, $countedMbIdArray);
                $this->fail("'mbId's in the response should be unique!\nGot: $item occurrences of $duplicateKey");
            }
        }
    }

    /**
     * Helper function to decode the response JSON and prepare a working array from the values
     *
     * @param string $response The Response JSON
     * @param string $item The key of the value that is needed
     *
     * @return array $workArray
     */
    private function prepareWorkArrayOfItems(string $response, string $item)
    {
        $responseJson = json_decode($response);
        $arrayLength = count($responseJson->{'data'});
        $workArray = [];

        for ($i = 0; $i < $arrayLength; $i++) {
            array_push($workArray, $responseJson->{'data'}[$i]->{$item});
        }

        return $workArray;
    }
}
