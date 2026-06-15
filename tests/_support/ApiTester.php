<?php

declare(strict_types=1);

namespace Tests\Support;

use Codeception\Util\Shared\Asserts;

class ApiTester extends \Codeception\Actor
{
    use _generated\ApiTesterActions;
    use Asserts;

    /**
     * Sets up 'X-API-Key', 'Accept' and 'Content-Type' headers for all API calls
     */
    public function setUpApiHeaders()
    {
        $this->haveHttpHeader('X-API-Key', $_ENV['API_KEY']);
        $this->haveHttpHeader('Accept', 'application/json');
        $this->haveHttpHeader('Content-Type', 'application/json');
    }

    /**
     * Checks for HTTP 200 status code, 'Content-Type' header, and the existence of 'data' object
     */
    public function validateResponseFormat()
    {
        $this->seeResponseCodeIs(200);
        $this->seeHttpHeader('Content-Type', 'application/json; charset=utf-8');
        $this->seeResponseContainsJson(array('data' => []));
    }

    /**
     * Checks for HTTP $statusCode status code, 'Content-Type' header, and the existence of 'errors' object
     *
     * @param int $statusCode The expected status code
     */
    public function validateErrorResponseFormat(int $statusCode)
    {
        $this->seeResponseCodeIs($statusCode);
        $this->seeHttpHeader('Content-Type', 'application/json; charset=utf-8');
        $this->seeResponseContainsJson(array('errors' => []));
    }

    /**
     * Validates the JSON schema of the 'GET' or 'POST' $method, based on 'SCHEMA_VERSION' environment variable
     *
     * @param string $method The method used for the API call
     */
    public function validateResponseJsonSchema(string $method)
    {
        if ($method != "get" && $method != "post") {
            $this->fail("Only 'get' and 'post' methods are allowed in validateResponseJsonSchema()");
        }
        $schemaVersion = $_ENV['SCHEMA_VERSION'];

        $this->seeResponseIsJson();
        $this->seeResponseIsValidOnJsonSchema("tests/_data/schemas/$schemaVersion/$method-media-buyers-schema.json");
    }
}
