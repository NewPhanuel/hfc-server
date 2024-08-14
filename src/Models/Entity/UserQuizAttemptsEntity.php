<?php
declare(strict_types=1);

namespace DevPhanuel\Models\Entity;

class UserQuizAttemptsEntity
{
    private string $uuid;
    private string $userUuid;
    private string $quizUuid;
    private ?string $score = null;
    private string $startedAt;
    private ?string $submittedAt = null;

    /**
     * Get the value of uuid
     *
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * Set the value of uuid
     *
     * @param string $uuid
     *
     * @return self
     */
    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;
        return $this;
    }

    /**
     * Get the value of userUuid
     *
     * @return string
     */
    public function getUserUuid(): string
    {
        return $this->userUuid;
    }

    /**
     * Set the value of userUuid
     *
     * @param string $userUuid
     *
     * @return self
     */
    public function setUserUuid(string $userUuid): self
    {
        $this->userUuid = $userUuid;
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
     * Get the value of score
     *
     * @return ?string
     */
    public function getScore(): ?string
    {
        return $this->score;
    }

    /**
     * Set the value of score
     *
     * @param ?string $score
     *
     * @return self
     */
    public function setScore(?string $score): self
    {
        $this->score = $score;
        return $this;
    }

    /**
     * Get the value of startedAt
     *
     * @return string
     */
    public function getStartedAt(): string
    {
        return $this->startedAt;
    }

    /**
     * Set the value of startedAt
     *
     * @param string $startedAt
     *
     * @return self
     */
    public function setStartedAt(string $startedAt): self
    {
        $this->startedAt = $startedAt;
        return $this;
    }

    /**
     * Get the value of submittedAt
     *
     * @return ?string
     */
    public function getSubmittedAt(): ?string
    {
        return $this->submittedAt;
    }

    /**
     * Set the value of submittedAt
     *
     * @param ?string $submittedAt
     *
     * @return self
     */
    public function setSubmittedAt(?string $submittedAt): self
    {
        $this->submittedAt = $submittedAt;
        return $this;
    }
}