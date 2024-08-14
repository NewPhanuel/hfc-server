<?php
declare(strict_types=1);

namespace DevPhanuel\Models\Entity;

class QuestionEntity
{
    private string $questionUuid;
    private string $quizUuid;
    private ?string $questionText;
    private string $createdAt;
    private string $updatedAt;


    /**
     * Get the value of questionUuid
     *
     * @return string
     */
    public function getQuestionUuid(): string
    {
        return $this->questionUuid;
    }

    /**
     * Set the value of questionUuid
     *
     * @param string $questionUuid
     *
     * @return self
     */
    public function setQuestionUuid(string $questionUuid): self
    {
        $this->questionUuid = $questionUuid;
        return $this;
    }

    /**
     * Get the value of quizUuid
     *
     * @return string
     */
    public function getQuizUuid(): string
    {
        return $this->quizUuid;
    }

    /**
     * Set the value of quizUuid
     *
     * @param string $quizUuid
     *
     * @return self
     */
    public function setQuizUuid(string $quizUuid): self
    {
        $this->quizUuid = $quizUuid;
        return $this;
    }

    /**
     * Get the value of questionText
     *
     * @return ?string
     */
    public function getQuestionText(): ?string
    {
        return $this->questionText;
    }

    /**
     * Set the value of questionText
     *
     * @param ?string $questionText
     *
     * @return self
     */
    public function setQuestionText(?string $questionText): self
    {
        $this->questionText = $questionText;
        return $this;
    }

    /**
     * Get the value of createdAt
     *
     * @return string
     */
    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    /**
     * Set the value of createdAt
     *
     * @param string $createdAt
     *
     * @return self
     */
    public function setCreatedAt(string $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }


    /**
     * Get the value of updatedAt
     *
     * @return string
     */
    public function getUpdatedAt(): string
    {
        return $this->updatedAt;
    }

    /**
     * Set the value of updatedAt
     *
     * @param string $updatedAt
     *
     * @return self
     */
    public function setUpdatedAt(string $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
}