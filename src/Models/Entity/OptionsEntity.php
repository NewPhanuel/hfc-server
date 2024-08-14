<?php
declare(strict_types=1);

namespace DevPhanuel\Models\Entity;

use DevPhanuel\Models\Enums\Boolean;
use ErrorException;
use ValueError;

class OptionsEntity
{
    private string $optionsUuid;
    private string $questionUuid;
    private ?string $optionText;
    private Boolean $isCorrect = Boolean::FALSE;
    private string $createdAt;
    private string $updatedAt;

    /**
     * Get the value of optionsUuid
     *
     * @return string
     */
    public function getOptionsUuid(): string
    {
        return $this->optionsUuid;
    }

    /**
     * Set the value of optionsUuid
     *
     * @param string $optionsUuid
     *
     * @return self
     */
    public function setOptionsUuid(string $optionsUuid): self
    {
        $this->optionsUuid = $optionsUuid;
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
     * Get the value of optionText
     *
     * @return ?string
     */
    public function getOptionText(): ?string
    {
        return $this->optionText;
    }

    /**
     * Set the value of optionText
     *
     * @param ?string $optionText
     *
     * @return self
     */
    public function setOptionText(?string $optionText): self
    {
        $this->optionText = $optionText;
        return $this;
    }

    /**
     * Get the value of isCorrect
     *
     * @return Boolean
     */
    public function getIsCorrect(): Boolean
    {
        return $this->isCorrect;
    }

    /**
     * Set the value of isCorrect
     *
     * @param string $isCorrect
     *
     * @return self
     */
    public function setIsCorrect(string $isCorrect): self
    {
        try {
            $this->isCorrect = Boolean::from($isCorrect);
            return $this;
        } catch (ValueError) {
            throw new ErrorException('Invalid Boolean Type');
        }
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