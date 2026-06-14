<?php

declare(strict_types=1);

namespace Tests\Support\Helper;

class MediaBuyersGetHelper extends \Codeception\Module
{
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

    public function validateIdUniqueness(string $response)
    {
        $idArray = $this->prepareWorkArrayOfItems($response, 'id');
        $mbIdArray = $this->prepareWorkArrayOfItems($response, 'mbId');

        $countedIdArray = array_count_values($idArray);
        $countedMbIdArray = array_count_values($mbIdArray);

        foreach ($countedIdArray as $item) {
            if ($item > 1) {
                $duplicateKey = array_search($item, $countedIdArray);
                $this->fail("'id's in the response should be unique!\nGot: $item occurances of $duplicateKey");
            }
        }

        foreach ($countedMbIdArray as $item) {
            if ($item > 1) {
                $duplicateKey = array_search($item, $countedMbIdArray);
                $this->fail("'mbId's in the response should be unique!\nGot: $item occurances of $duplicateKey");
            }
        }
    }

    private function prepareWorkArrayOfItems(string $response, string $item)
    {
        $responseJson = json_decode($response);
        $arrayLenght = count($responseJson->{'data'});
        $workArray = [];

        for ($i = 0; $i < $arrayLenght; $i++) {
            array_push($workArray, $responseJson->{'data'}[$i]->{$item});
        }

        return $workArray;
    }
}
