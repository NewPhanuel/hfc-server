<?php
declare(strict_types=1);

namespace DevPhanuel\Models\Entity;

class UserQuizAttemptsEntity
{
    private string $uuid;
    private string $attemptUuid;
    private string $questionUuid;
    private string $optionUuid;

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
     * Get the value of attemptUuid
     *
     * @return string
     */
    public function getAttemptUuid(): string
    {
        return $this->attemptUuid;
    }

    /**
     * Set the value of attemptUuid
     *
     * @param string $attemptUuid
     *
     * @return self
     */
    public function setAttemptUuid(string $attemptUuid): self
    {
        $this->attemptUuid = $attemptUuid;
        return $this;
    }

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
     * Get the value of optionUuid
     *
     * @return string
     */
    public function getOptionUuid(): string
    {
        return $this->optionUuid;
    }

    /**
     * Set the value of optionUuid
     *
     * @param string $optionUuid
     *
     * @return self
     */
    public function setOptionUuid(string $optionUuid): self
    {
        $this->optionUuid = $optionUuid;
        return $this;
    }
}