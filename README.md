# codeception-mediabuyers

Assumptions made:
- Content-Type header charset was not defined, assuming it is charset=utf-8
- Authorization happens with an API key, setting in in .env
- Schemas are auto-generated, they might get overridden, so schema issues should be handled in helpers
- initials are required in mediaBuyers POST according to schema, but not according to field validation rules (assuming validation rules are correct)
- mediaBuyers POST length-related error is not defined, assuming one based on other errors
- mediaBuyers POST mbId-related error is not defined, assuming one based on other errors
- mediaBuyers POST duplicate entity error is not defined, assuming one based on other errors
- Duplicate mediaBuyer entities return HTTP 409 instead of HTTP 400

Overview of the solution:
- Codeception setup with REST, PhpBrowser and Asserts modules + PhpDotenv for environment setup
- tests/_data/schemas for storing latest JSON schemas
- tests/_support/Helper for fixtures, API call and test case helpers, split between GET and POST
- tests/_support/ApiTester for common test functions and API setup
- tests/Api folder for tests split into two files (GET, POST)
- tests/.env.example an example environment file for the variables needed

Notes on test scenarios:
- Happy Path scenarios were always done as defaults
- Every error case scenario is put in a different test so the results are more segregated after the run
- All acceptance criteria is covered

Abstractions introduced:
- Setup of mediaBuyers API fixture, helps with possible changes to the structure in the future
- API response and HTTP header validations, they make tests more readable, and the frequently repeat
- Looping through response array, it's a common usage scenario, will possibly happen in the future
- Lopping through all error codes in a response, and checking them against a list of expected ones
- Checking responses against the latest schemas, it can change so they should not be hard coded in tests

Future improvements:
- Test data setup, so no need to do it in test case level
- Looping through a list of error-causing inputs and their expected error codes array and doing a data driven test
- Teardown script to cleanup database, delete entities created during test
- Parse test output result files and generate a single XML report that can be used in Allure for example
- Move failure screenshots and reports to timestamped archive folder for the run for historical auditability
- Have multiple actors running the tests in parallel
- CI integration script to run the tests on every commit (on-demand) and before every merge commit (required)
- Use actual Codeception assertions where possible, instead of low-level PHP ones
- Split Helpers and Data/Fixtures better, currently the separation is pretty basic
