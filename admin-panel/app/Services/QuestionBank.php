<?php

namespace App\Services;

use App\Models\Question;

class QuestionBank
{
    /**
     * Fetch questions based on job type and experience level.
     *
     * @param string $jobType
     * @param string $experienceLevel
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function fetchQuestions(string $job_id, string $experienceLevel)
    {
        return Question::where('jobType_Id', $job_id)
            ->get();
    }

    /**
     * Add a new question.
     *
     * @param array $data
     * @return \App\Models\Question
     */
    public function addQuestion(array $data)
    {
        return Question::create($data);
    }

    /**
     * Remove a question by ID.
     *
     * @param int $id
     * @return bool|null
     */
    public function removeQuestion(int $id)
    {
        $question = Question::find($id);

        if ($question) {
            return $question->delete();
        }

        return false;
    }
    
}