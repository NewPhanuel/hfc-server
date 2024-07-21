<?php
declare(strict_types=1);

namespace DevPhanuel\Models\Entity;

use DevPhanuel\Models\Enums\Gender;
use DevPhanuel\Models\Enums\WorkerStatus;
use ErrorException;
use ValueError;

class UserEntity
{
    private string $userUuid;
    private ?string $firstname = null;
    private ?string $lastname = null;
    private ?string $email = null;
    private ?string $phone = null;
    private ?string $password = null;
    private ?string $profilePics = null;
    private ?Gender $gender = null;
    private ?string $dob = null;
    private ?WorkerStatus $workerStatus = null;
    private ?string $department = null;
    private ?string $workersCertificate = null;
    private string $createdAt;
    private string $updatedAt;

    public function getUserUuid(): ?string
    {
        return $this->userUuid;
    }

    public function setUserUuid(string $uuid): self
    {
        $this->userUuid = $uuid;
        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;
        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = password_hash($password, PASSWORD_BCRYPT);
        return $this;
    }

    public function getProfilePics(): ?string
    {
        return $this->profilePics;
    }

    public function setProfilePics(string $profilePics): self
    {
        $this->profilePics = $profilePics;
        return $this;
    }

    public function getGender(): ?Gender
    {
        return $this->gender;
    }

    public function setGender(string $gender): self
    {
        try {
            $this->gender = Gender::from($gender);
            return $this;
        } catch (ValueError) {
            throw new ErrorException('Invalid Gender Type');
        }
    }

    public function getDob(): ?string
    {
        return $this->dob;
    }

    public function setDob(string $dob): self
    {
        $this->dob = $dob;
        return $this;
    }

    public function getWorkerStatus(): ?WorkerStatus
    {
        return $this->workerStatus;
    }

    public function setWorkerStatus(string $workerStatus): self
    {
        try {
            $this->workerStatus = WorkerStatus::from((int) $workerStatus);
            return $this;
        } catch (ValueError) {
            throw new ErrorException('Invalid worker status Type');
        }
    }

    public function getDepartment(): ?string
    {
        return $this->department;
    }

    public function setDepartment(string $department): self
    {
        $this->department = $department;
        return $this;
    }

    public function getWorkersCertificate(): ?string
    {
        return $this->workersCertificate;
    }

    public function setWorkersCertificate(string $workersCertificate): self
    {
        $this->workersCertificate = $workersCertificate;
        return $this;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function setCreatedAt(string $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(string $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
}