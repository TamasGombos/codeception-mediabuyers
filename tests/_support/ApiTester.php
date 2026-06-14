<?php

declare(strict_types=1);

namespace Tests\Support;

use Codeception\Util\Shared\Asserts;

class ApiTester extends \Codeception\Actor
{
    use _generated\ApiTesterActions;
    use Asserts;

    public function setUpApiHeaders()
    {
        $this->haveHttpHeader('X-API-Key', $_ENV['API_KEY']);
        $this->haveHttpHeader('Accept', 'application/json');
        $this->haveHttpHeader('Content-Type', 'application/json');
    }

    public function validateResponseFormat()
    {
        $this->seeResponseCodeIs(200);
        $this->seeHttpHeader('Content-Type', 'application/json; charset=utf-8');
        $this->seeResponseContainsJson(array('data' => []));
    }

    public function validateErrorResponseFormat()
    {
        $this->seeResponseCodeIs(400);
        $this->seeHttpHeader('Content-Type', 'application/json; charset=utf-8');
        $this->seeResponseContainsJson(array('errors' => []));
    }

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
