<?php
declare(strict_types=1);

namespace DevPhanuel\Controllers;

use DevPhanuel\Models\Entity\OptionsEntity;
use DevPhanuel\Models\Entity\QuestionEntity;
use DevPhanuel\Models\Entity\QuizEntity;
use DevPhanuel\Models\QuizModel;
use DevPhanuel\Services\QuizService;
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

    public function index(): void
    {
        $quizzes = QuizService::index();

        if (!$quizzes) {
            response(StatusCode::NO_CONTENT, errorMessage('No Content', 'No content found in the server', StatusCode::NO_CONTENT));
            return;
        }

        response(StatusCode::OK, successMessage('All Quizzes on the server', ['quizzes' => $quizzes]));
        return;
    }

    public function show(array $params): void
    {
        $uuid = $params['uuid'];

        $quiz = QuizService::show($uuid);
        if (!$quiz) {
            response(StatusCode::NO_CONTENT, errorMessage('No Content', 'No content found in the server', StatusCode::NO_CONTENT));
            return;
        }

        response(StatusCode::OK, successMessage('Quiz successfully retrieved', $quiz));
        return;
    }

    public function store(array $params): void
    {
        $data = $params['data'];

        $validation = $this->SchemaValidation->validateQuiz($data);

        if (!is_bool($validation)) {
            response(StatusCode::FORBIDDEN, errorMessage('Validation Error', $validation, StatusCode::FORBIDDEN));
        }

        $questions = [];
        $options = [];

        $quizEntity = new QuizEntity();
        $quizEntity->setQuizUuid()->setTitle($data->title)->setDescription($data->description)->setCreatedAt()->setUpdatedAt();

        for ($i = 0; $i < count($data->questions); $i++) {
            $questionEntity = new QuestionEntity();
            $questionEntity->setQuestionUuid()->setQuizUuid($quizEntity->getQuizUuid())->setQuestionText($data->questions[$i]->question_text)
                ->setCreatedAt()->setUpdatedAt();

            $optionsArray = [];
            for ($j = 0; $j < count($data->questions[$i]->options); $j++) {
                $optionEntity = new OptionsEntity();
                $optionEntity->setOptionUuid()->setQuestionUuid($questionEntity->getQuestionUuid())->setOptionText($data->questions[$i]->options[$j]->option_text)
                    ->setIsCorrect($data->questions[$i]->options[$j]->is_correct)->setCreatedAt()->setUpdatedAt();
                $optionsArray[] = $optionEntity;
            }
            $questions[] = $questionEntity;
            $options[] = $optionsArray;
        }

        $quizIsStored = QuizModel::store($quizEntity, $questions, $options);

        if (!is_bool($quizIsStored))
            response(StatusCode::INTERNAL_SERVER_ERROR, errorMessage($quizIsStored['type'], $quizIsStored['message'], StatusCode::INTERNAL_SERVER_ERROR));
        response(StatusCode::CREATED, successMessage("Quiz created Successfully"));
    }
}