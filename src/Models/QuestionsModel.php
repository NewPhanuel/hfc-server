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

    public static function store(array $questionEntity, array $optionsEntity): void
    {
        $question = R::dispense(self::TABLE_NAME);
        for ($i = 0; $i < count($questionEntity); $i++) {
            $question['quiz_uuid'] = $questionEntity[$i]->getQuizUuid();
            $question['question_uuid'] = $questionEntity[$i]->getQuestionUuid();
            $question['question_text'] = $questionEntity[$i]->getQuestionText();
            $question['created_at'] = $questionEntity[$i]->getCreatedAt();
            $question['updated_at'] = $questionEntity[$i]->getUpdatedAt();
            R::store($question);
            OptionsModel::store($optionsEntity[$i]);
        }
    }
}