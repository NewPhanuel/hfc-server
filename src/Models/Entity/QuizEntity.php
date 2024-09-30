<?php
declare(strict_types=1);

namespace DevPhanuel\Models\Entity;

use Ramsey\Uuid\Nonstandard\Uuid;

class QuizEntity
{
    private const DATE_TIME_FORMAT = 'Y-m-d H:i:s';
    private string $quizUuid;
    private ?string $title = null;
    private ?string $description = null;
    private string $createdAt;
    private string $updatedAt;

    public function getQuizUuid(): string
    {
        return $this->quizUuid;
    }

    public function setQuizUuid(): self
    {
        $this->quizUuid = (string) Uuid::uuid4();
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function setCreatedAt(): self
    {
        $this->createdAt = date(self::DATE_TIME_FORMAT);
        return $this;
    }

    public function getUpdatedAt(): string
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(): self
    {
        $this->updatedAt = date(self::DATE_TIME_FORMAT);
        return $this;
    }
}