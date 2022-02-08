<?php

namespace ShowHeroes\Passport\Tests\Fixtures\Apis;

use Illuminate\Validation\Validator;

/**
 * Class ResponseFormatAssertions
 * @package ShowHeroes\AdHero\Fixtures
 * @mixin \Illuminate\Foundation\Testing\TestCase
 */
trait ResponseFormatAssertions
{

    /** @var array|bool Decoded JSON last response. */
    protected $responseData;
    /** @var integer */
    protected $responseCode;


    /**
     * Asserts that API response is OK (status code 200).
     */
    protected function assertResponseOk()
    {
        $this->assertEquals(200, $this->responseCode, 'Response status is not OK.');
    }

    protected function assertApiResourceDeleted(): void
    {
        $this->assertApiResponse(200, 'Resource deleted.');
    }

    protected function assertApiResourceUpdated(): void
    {
        $this->assertApiResponse(200, 'Resource updated.');
    }

    protected function assertApiResourceCreated(): void
    {
        $this->assertApiResponse(201, 'Resource created.');
    }

    /**
     * Asserts that API response has required status code.
     *
     * @param integer $code
     */
    protected function assertResponseStatus($code)
    {
        $this->assertEquals($code, $this->responseCode, 'Response status is not matching.');
    }

    /**
     * Asserts that API response require authentication.
     * @param string $message
     */
    protected function assertApiUnauthenticatedError($message = 'Unauthenticated.')
    {
        $this->assertApiResponse(401, $message);
    }

    /**
     * Asserts that API response require authorisation.
     *
     * @param string $message
     */
    protected function assertApiForbiddenError($message = 'This action is unauthorized.')
    {
        $this->assertApiResponse(403, $message);
    }

    /**
     * Asserta that response 400 Bad Request.
     * @param string $message
     */
    protected function assertApiBadRequest($message = '')
    {
        $this->assertApiResponse(400, $message);
    }

    /**
     * Asserts that API response entity not found.
     * @param string $message
     */
    protected function assertApiNotFoundError($message = 'Not found.')
    {
        $this->assertApiResponse(404, $message);
    }

    /**
     * Asserts that API response contains validation errors.
     *
     * @param array $errors
     */
    protected function assertApiValidationErrors(array $errors)
    {
        $this->assertValidationErrors($errors);
    }

    /**
     * Asserts that API response correctly formatted and contains
     * specified code and message.
     *
     * @param int $code
     * @param string $message
     */
    protected function assertApiResponse($code = 200, $message = '')
    {
        $this->assertResponseStatus($code);
        $this->assertResponseFormatted();
        $this->assertResponseMetaStatusCode($code);
        $this->assertResponseMetaMessage($message);
    }

    protected function assertResponseFormatted()
    {
        $this->assertArrayHasKey('data', $this->responseData);
        $this->assertArrayHasKey('meta', $this->responseData);
        $this->assertArrayHasKey('status_code', $this->responseData['meta']);
        $this->assertArrayHasKey('message', $this->responseData['meta']);
    }

    /**
     * Asserts that Response data corresponds to specified data and validation rules.
     *
     * @param array $exactData Array of data for equals assertion.
     * @param array $existsDataValidation Array of validation rules for Laravel Validator class.
     */
    protected function assertResponseData(array $exactData = [], array $existsDataValidation = [])
    {
        $dataToCheckEquals = $this->responseData['data'];
        $dataToCheckValidation = [];
        foreach ($existsDataValidation as $fieldName => $rule) {
            $validators = explode('|', $rule);

            // Do not validate missing attributes, they marked as 'sometimes'.
            if (in_array('sometimes', $validators) && !array_key_exists($fieldName, $this->responseData['data'])) {
                continue;
            }

            // Validate everything else.
            $this->assertArrayHasKey($fieldName, $this->responseData['data']);
            $dataToCheckValidation[$fieldName] = $this->responseData['data'][$fieldName];
            if (!array_key_exists($fieldName, $exactData)) {
                unset($dataToCheckEquals[$fieldName]);
            }
        }
        $this->assertEquals($exactData, $dataToCheckEquals);

        if (!empty($dataToCheckValidation)) {
            $validator = Validator::make($dataToCheckValidation, $existsDataValidation);
            if ($validator->fails()) {
                $this->fail('Data Validator error: '.$validator->errors()->first());
            }
        }
    }
    protected function assertResponseMetaStatusCode($code)
    {
        $this->assertEquals($code, $this->responseData['meta']['status_code']);
    }

    protected function assertResponseMetaMessage($message)
    {
        $this->assertEquals($message, $this->responseData['meta']['message'], 'Meta messages does not match.');
    }

    protected function assertValidationErrors($errors)
    {
        $this->assertResponseFormatted();
        $this->assertResponseMetaStatusCode(422);
        $this->assertResponseStatus(422);
        foreach ($errors as $fieldName => $message) {
            $this->assertArrayHasKey($fieldName, $this->responseData['data'], "Failing to found validation errors for a field '$fieldName'.");
            $this->assertEquals($message, $this->responseData['data'][$fieldName], "Failing to match validation errors for a field '$fieldName'.");
        }
    }

    protected function assertNoValidationErrorsForField($fieldName)
    {
        $this->assertResponseFormatted();
        if (!empty($this->responseData['data'])) {
            $this->assertArrayNotHasKey($fieldName, $this->responseData['data']);
        }
    }

    /**
     * Checks that response collection contains required IDs.
     *
     * @param array $IDs
     * @param string $attributeName
     */
    protected function assertResponseContainsIDs(array $IDs, $attributeName = 'id')
    {
        $data = $this->responseData['data'];
        if (!is_array($data) || empty($data)) {
            $this->fail('Failed to locate IDs in response data. Data is empty or invalid.');
        }
        $unLocatedIDs = array_flip($IDs);
        foreach ($data as $item) {
            if (isset($item[$attributeName])) {
                unset($unLocatedIDs[$item[$attributeName]]);
            }
        }
        if (!empty($unLocatedIDs)) {
            $this->fail('Failed to found IDs inside response data. Missing IDs: '.implode(',', array_keys($unLocatedIDs)).'.');
        }
    }

}
