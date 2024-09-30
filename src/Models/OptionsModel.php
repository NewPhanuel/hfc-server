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

    public static function index(): array
    {
        return R::findAll(self::TABLE_NAME);
    }

    public static function show(string $uuid): ?array
    {
        $options = R::find(self::TABLE_NAME, "question_uuid = ?", [$uuid]);

        if (!$options)
            return null;

        $exportedOptions = [];

        // Loop through each option bean and export it to an array
        foreach ($options as $option) {
            unset($option['id']);
            unset($option['question_uuid']);
            $exportedOptions[] = $option->export();
        }

        return $exportedOptions;
    }

    public static function store(array $optionsEntity): void
    {
        $optionBean = R::dispense(self::TABLE_NAME);

        foreach ($optionsEntity as $option) {
            $optionBean['question_uuid'] = $option->getQuestionUuid();
            $optionBean['option_uuid'] = $option->getOptionUuid();
            $optionBean['option_text'] = $option->getOptionText();
            $optionBean['is_correct'] = $option->getIsCorrect()->value;
            $optionBean['created_at'] = $option->getCreatedAt();
            $optionBean['updated_at'] = $option->getUpdatedAt();
            respond(200, ['option' => $optionBean->export()]);
            R::store($optionBean);
            R::close();
        }
    }
}