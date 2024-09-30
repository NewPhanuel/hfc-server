<?php
declare(strict_types=1);

namespace DevPhanuel\Services;

use DevPhanuel\Models\OptionsModel;
use DevPhanuel\Models\QuestionsModel;
use DevPhanuel\Models\QuizModel;

class QuizService
{
    public static function index(): ?array
    {
        $quizzes = QuizModel::index();

        if (count($quizzes) < 1) {
            return null;
        }

        $quizzesWithQuestions = [];

        foreach ($quizzes as &$quiz) {
            unset($quiz['id']);

            $questions = QuestionsModel::show($quiz['quiz_uuid']);

            foreach ($questions as &$question) {
                $question['options'] = OptionsModel::show($question['question_uuid']);
            }

            $quiz['questions'] = $questions;
            $quizzesWithQuestions[] = $quiz;
        }

        return $quizzesWithQuestions;
    }

    public static function show(string $uuid): ?array
    {
        $quiz = QuizModel::show($uuid);

        if (!$quiz) {
            return null;
        }

        unset($quiz['id']);

        $questions = QuestionsModel::show($quiz['quiz_uuid']);

        foreach ($questions as &$question) {
            $question['options'] = OptionsModel::show($question['question_uuid']);
        }

        $quiz['questions'] = $questions;
        $quizzesWithQuestions[] = $quiz;

        return $quizzesWithQuestions;
    }
}