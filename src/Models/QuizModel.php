<?php
declare(strict_types=1);

namespace DevPhanuel\Models;

use DevPhanuel\Models\Entity\QuizEntity;
use RedBeanPHP\RedException\SQL as RedBeanSQLException;
use RedBeanPHP\R;

final class QuizModel
{
    private const TABLE_NAME = 'quizzes';
    private const DATE_TIME_FORMAT = 'Y-m-d H:i:s';

    public static function index(): array
    {
        return R::findAll(self::TABLE_NAME);
    }

    public static function store(QuizEntity $quizEntity, array $questionEntity, array $optionsEntity): string|bool
    {
        $quizBean = R::dispense(self::TABLE_NAME);
        $quizBean['quiz_uuid'] = $quizEntity->getQuizUuid();
        $quizBean['title'] = $quizEntity->getTitle();
        $quizBean['description'] = $quizEntity->getDescription();
        $quizBean['created_at'] = $quizEntity->getCreatedAt();
        $quizBean['updated_at'] = $quizEntity->getUpdatedAt();

        try {
            R::begin();
            R::store($quizBean);
            QuestionsModel::store($questionEntity, $optionsEntity);
            return true;
        } catch (RedBeanSQLException $e) {
            return $e->getMessage();
        }
    }
}