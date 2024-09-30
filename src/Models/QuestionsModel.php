<?php
declare(strict_types=1);

namespace DevPhanuel\Models;

use DevPhanuel\Models\Entity\OptionsEntity;
use DevPhanuel\Models\Entity\QuestionEntity;
use DevPhanuel\Models\Entity\QuizEntity;
use RedBeanPHP\RedException\SQL as RedBeanSQLException;
use RedBeanPHP\R;

final class QuestionsModel
{

    private const TABLE_NAME = 'questions';
    private const DATE_TIME_FORMAT = 'Y-m-d H:i:s';

    public static function index(): array
    {
        return R::findAll(self::TABLE_NAME);
    }

    public static function show(string $uuid): ?array
    {
        $questions = R::find(self::TABLE_NAME, "quiz_uuid = ?", [$uuid]);

        if (!$questions)
            return null;

        $exportedQuestions = [];

        foreach ($questions as $question) {
            unset($question['id']);
            unset($question['quiz_uuid']);
            $exportedQuestions[] = $question->export();
        }

        return $exportedQuestions;
    }

    public static function store(array $questionEntity, array $optionsEntity): void
    {
        $question = R::dispense(self::TABLE_NAME);
        for ($i = 0; $i < count($questionEntity); $i++) {
            $question['quiz_uuid'] = $questionEntity[$i]->getQuizUuid();
            $question['question_uuid'] = $questionEntity[$i]->getQuestionUuid();
            $question['question_text'] = $questionEntity[$i]->getQuestionText();
            $question['created_at'] = $questionEntity[$i]->getCreatedAt();
            $question['updated_at'] = $questionEntity[$i]->getUpdatedAt();
            respond(200, ['question' => $question->export(), 'i' => $i]);
            R::store($question);
            R::close();
            OptionsModel::store($optionsEntity[$i]);
        }
    }
}