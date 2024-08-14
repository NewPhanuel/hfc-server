<?php
declare(strict_types=1);

namespace DevPhanuel\Models\Entity;

class QuizEntity
{
    private string $quizUuid;
    private ?string $title = null;
    private ?string $description = null;
    private string $createdAt;
    private string $updatedAt;

    public function getQuizUuid(): string
    {
        return $this->quizUuid;
    }

    public function setQuizUuid(string $quizUuid): self
    {
        $this->quizUuid = $quizUuid;
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

    public function setCreatedAt(string $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): string
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(string $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
}