<?php
declare(strict_types=1);

namespace DevPhanuel\Models\Entity;

use DevPhanuel\Models\Enums\Boolean;
use ErrorException;
use Ramsey\Uuid\Nonstandard\Uuid;
use ValueError;

class OptionsEntity
{
    private const DATE_TIME_FORMAT = 'Y-m-d H:i:s';
    private string $optionUuid;
    private string $questionUuid;
    private ?string $optionText;
    private Boolean $isCorrect = Boolean::FALSE;
    private string $createdAt;
    private string $updatedAt;

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
    public function setOptionUuid(): self
    {
        $this->optionUuid = (string) Uuid::uuid4();
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
    public function setCreatedAt(): self
    {
        $this->createdAt = date(self::DATE_TIME_FORMAT);
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
    public function setUpdatedAt(): self
    {
        $this->updatedAt = date(self::DATE_TIME_FORMAT);
        return $this;
    }
}