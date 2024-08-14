<?php
declare(strict_types=1);

namespace DevPhanuel\Models;

use DevPhanuel\Models\Entity\OptionsEntity;
use DevPhanuel\Models\Entity\QuizEntity;
use RedBeanPHP\RedException\SQL as RedBeanSQLException;
use RedBeanPHP\R;

final class OptionsModel
{

    private const TABLE_NAME = 'options';
    private const DATE_TIME_FORMAT = 'Y-m-d H:i:s';

    public static function store(OptionsEntity $optionsEntity): int|string
    {
        $option = R::dispense(self::TABLE_NAME);
        $option['question_uuid'] = $optionsEntity->getQuestionUuid();
        $option['option_uuid'] = $optionsEntity->getOptionsUuid();
        $option['option_text'] = $optionsEntity->getOptionText();
        $option['is_correct'] = $optionsEntity->getIsCorrect()->value;
        $option['created_at'] = $optionsEntity->getCreatedAt();
        $option['updated_at'] = $optionsEntity->getUpdatedAt();
        return R::store($option);
    }
}