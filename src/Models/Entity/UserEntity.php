<?php
declare(strict_types=1);

namespace DevPhanuel\Models\Entity;

use DevPhanuel\Models\Enums\Gender;
use DevPhanuel\Models\Enums\Role;
use DevPhanuel\Models\Enums\Boolean;
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
    private Role $role = Role::USER;
    private Boolean $isVerified = Boolean::FALSE;
    private Boolean $isRestricted = Boolean::FALSE;
    private Boolean $canAccessQuiz = Boolean::FALSE;
    private ?int $quizAttempt = 0;
    private ?int $scores = 0;
    private ?int $verificationCode = null;
    private ?string $emailVerifiedAt = null;
    private ?int $OTP = null;
    private ?string $lastPasswordReset = null;
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

    public function getRole(): Role
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        try {
            $this->role = Role::from($role);
            return $this;
        } catch (ValueError) {
            throw new ErrorException('Invalid Role Type');
        }
    }

    public function getIsVerified(): Boolean
    {
        return $this->isVerified;
    }

    public function setIsVerified(string $isVerified): self
    {
        try {
            $this->isVerified = Boolean::from($isVerified);
            return $this;
        } catch (ValueError) {
            throw new ErrorException('Invalid Boolean Type');
        }
    }

    public function getIsRestricted(): Boolean
    {
        return $this->isRestricted;
    }

    public function setIsRestricted(string $isRestricted): self
    {
        try {
            $this->isRestricted = Boolean::from($isRestricted);
            return $this;
        } catch (ValueError) {
            throw new ErrorException('Invalid Boolean Type');
        }
    }

    /**
     * Get the value of canAccessQuiz
     *
     * @return Boolean
     */
    public function getCanAccessQuiz(): Boolean
    {
        return $this->canAccessQuiz;
    }

    /**
     * Set the value of canAccessQuiz
     *
     * @param string $canAccessQuiz
     *
     * @return self
     */
    public function setCanAccessQuiz(string $canAccessQuiz): self
    {
        try {
            $this->canAccessQuiz = Boolean::from($canAccessQuiz);
            return $this;
        } catch (ValueError) {
            throw new ErrorException('Invalid Boolean Type');
        }
    }

    public function getQuizAttempt(): ?int
    {
        return $this->quizAttempt;
    }

    public function setQuizAttempt(string|int $quizAttempt): self
    {
        $this->quizAttempt = (int) $quizAttempt;
        return $this;
    }

    public function getScores(): ?int
    {
        return $this->scores;
    }

    public function setScores(string|int $scores): self
    {
        $this->scores = (int) $scores;
        return $this;
    }

    public function getVerificationCode(): ?int
    {
        return $this->verificationCode;
    }

    public function setVerificationCode(int $verificationCode): self
    {
        $this->verificationCode = $verificationCode;
        return $this;
    }

    public function getEmailVerifiedAt(): ?string
    {
        return $this->emailVerifiedAt;
    }

    public function setEmailVerifiedAt(string $emailVerifiedAt): self
    {
        $this->emailVerifiedAt = $emailVerifiedAt;
        return $this;
    }

    public function getOTP(): ?int
    {
        return $this->OTP;
    }

    public function setOTP(int $OTP): self
    {
        $this->OTP = $OTP;
        return $this;
    }

    public function getLastPasswordReset(): ?string
    {
        return $this->lastPasswordReset;
    }

    public function setLastPasswordReset(string $lastPasswordReset): self
    {
        $this->lastPasswordReset = $lastPasswordReset;
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