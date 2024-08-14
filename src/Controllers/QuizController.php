<?php
declare(strict_types=1);

namespace DevPhanuel\Controllers;

use DevPhanuel\Validation\SchemaValidation;
use PH7\JustHttp\StatusCode;

class QuizController
{

    private const DATE_TIME_FORMAT = 'Y-m-d H:i:s';
    private SchemaValidation $SchemaValidation;

    public function __construct()
    {
        $this->SchemaValidation = new SchemaValidation();
    }

    public function store(array $params): void
    {
        $data = $params['data'];

        $validation = $this->SchemaValidation->validateQuiz($data);

        if (!is_bool($validation)) {
            response(StatusCode::FORBIDDEN, errorMessage('Validation Error', $validation, StatusCode::FORBIDDEN));
        }

        response(StatusCode::OK, successMessage('Quiz passed the validator', ['data' => $data]));
    }
}