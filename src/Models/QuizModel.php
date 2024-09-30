<?php
declare(strict_types=1);

namespace DevPhanuel\Models;

use DevPhanuel\Models\Entity\QuizEntity;
use Exception;
use RedBeanPHP\RedException\SQL as RedBeanSQLException;
use RedBeanPHP\R;

final class QuizModel
{
    private const TABLE_NAME = 'quizzes';
    private const DATE_TIME_FORMAT = 'Y-m-d H:i:s';

    public static function index(): array
    {
        $quizzes = R::findAll(self::TABLE_NAME);

        $exportedQuizzes = [];

        foreach ($quizzes as $quiz) {
            $exportedQuizzes[] = $quiz->export();
        }
        return $exportedQuizzes;
    }

    public static function show(string $uuid): ?array
    {
        $quizBean = R::findOne(self::TABLE_NAME, "quiz_uuid = ?", [$uuid]);

        if (!$quizBean)
            return null;

        return $quizBean->export();
    }

    public static function store(QuizEntity $quizEntity, array $questionEntity, array $optionsEntity): array|bool
    {
        try {
            // Start a transaction
            R::begin();

            // Create and store quiz bean
            $quizBean = R::dispense(self::TABLE_NAME);
            $quizBean['quiz_uuid'] = $quizEntity->getQuizUuid();
            $quizBean['title'] = $quizEntity->getTitle();
            $quizBean['description'] = $quizEntity->getDescription();
            $quizBean['created_at'] = $quizEntity->getCreatedAt();
            $quizBean['updated_at'] = $quizEntity->getUpdatedAt();
            R::store($quizBean);

            // Loop over questions and store each question and its options
            for ($i = 0; $i < count($questionEntity); $i++) {
                // Dispense a new question bean for each question
                $questionBean = R::dispense("questions");
                $questionBean['quiz_uuid'] = $questionEntity[$i]->getQuizUuid();
                $questionBean['question_uuid'] = $questionEntity[$i]->getQuestionUuid();
                $questionBean['question_text'] = $questionEntity[$i]->getQuestionText();
                $questionBean['created_at'] = $questionEntity[$i]->getCreatedAt();
                $questionBean['updated_at'] = $questionEntity[$i]->getUpdatedAt();
                R::store($questionBean);

                // Loop through each option for the current question
                foreach ($optionsEntity[$i] as $option) {
                    // Dispense a new option bean for each option
                    $optionBean = R::dispense("options");
                    $optionBean['question_uuid'] = $option->getQuestionUuid();
                    $optionBean['option_uuid'] = $option->getOptionUuid();
                    $optionBean['option_text'] = $option->getOptionText();
                    $optionBean['is_correct'] = $option->getIsCorrect()->value;
                    $optionBean['created_at'] = $option->getCreatedAt();
                    $optionBean['updated_at'] = $option->getUpdatedAt();
                    R::store($optionBean);
                }
            }

            // Commit transaction if everything is successful
            R::commit();
            R::close();

            return true;
        } catch (RedBeanSQLException $e) {
            // Handle SQL-specific exceptions
            R::rollback();
            return [
                "type" => "SQL Exception",
                "message" => $e->getMessage(),
            ];
        } catch (Exception $e) {
            // Handle any other exceptions
            R::rollback();
            return [
                "type" => "General Exception",
                "message" => $e->getMessage(),
            ];
        }
    }

}