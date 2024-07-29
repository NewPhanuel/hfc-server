<?php
declare(strict_types=1);

namespace DevPhanuel\Models;

use DevPhanuel\Exception\InvalidValidationException;
use DevPhanuel\Models\Entity\AccountEntity;
use RedBeanPHP\R;
use RedbeanPHP\RedException\SQL as RedBeanSQLException;

final class AccountModel
{
    private const TABLE_NAME = 'accounts';

    public static function index(): array
    {
        return R::findAll(self::TABLE_NAME);
    }

    public static function store(AccountEntity $accountEntity): bool
    {
        $accountBean = R::dispense(self::TABLE_NAME);
        $accountBean['user_uuid'] = $accountEntity->getUserUuid();
        $accountBean['is_funded'] = $accountEntity->getIsFunded()->value;
        $accountBean['is_deactivated'] = $accountEntity->getIsDeactivated()->value;
        $accountBean['total_funding'] = $accountEntity->getTotalFunding();
        $accountBean['total_earning'] = $accountEntity->getTotalEarning();
        $accountBean['earning_balance'] = $accountEntity->getEarningBalance();
        $accountBean['remitted_payment'] = $accountEntity->getRemittedPayment();
        $accountBean['created_at'] = $accountEntity->getCreatedAt();
        $accountBean['updated_at'] = $accountEntity->getUpdatedAt();

        $beanId = R::store($accountBean);
        R::close();

        if ($beanId !== null)
            return true;
        return false;
    }

    public static function update(string $uuid, AccountEntity $accountEntity): array|false
    {
        $accountBean = R::findOne(self::TABLE_NAME, 'user_uuid = ?', [$uuid]);
        if (!$accountBean)
            return false;

        if ($accountEntity->getEmail())
            $accountBean['email'] = $accountEntity->getEmail();
        if ($accountEntity->getPassword())
            $accountBean['password'] = $accountEntity->getPassword();
        if ($accountEntity->getIsFunded())
            $accountBean['is_funded'] = $accountEntity->getIsFunded()->value;
        if ($accountEntity->getTotalEarning())
            $accountBean['total_earning'] = $accountEntity->getTotalEarning();
        if ($accountEntity->getTotalFunding())
            $accountBean['total_funding'] = $accountEntity->getTotalFunding();
        if ($accountEntity->getEarningBalance())
            $accountBean['earning_balance'] = $accountEntity->getEarningBalance();
        if ($accountEntity->getRemittedPayment())
            $accountBean['remitted_payment'] = $accountEntity->getRemittedPayment();
        if ($accountEntity->getGuarantorName())
            $accountBean['guarantor_name'] = $accountEntity->getGuarantorName();
        if ($accountEntity->getGuarantorPhone())
            $accountBean['guarantor_phone'] = $accountEntity->getGuarantorPhone();
        if ($accountEntity->getBankName())
            $accountBean['bank_name'] = $accountEntity->getBankName();
        if ($accountEntity->getAcctName())
            $accountBean['acct_name'] = $accountEntity->getAcctName();
        if ($accountEntity->getAcctNumber())
            $accountBean['acct_number'] = $accountEntity->getAcctNumber();
        if ($accountEntity->getIsDeactivated())
            $accountBean['is_deactivated'] = $accountEntity->getIsDeactivated()->value;
        if ($accountEntity->getUpdatedAt())
            $accountBean['updated_at'] = $accountEntity->getUpdatedAt();

        $beanId = R::store($accountBean);
        R::close();

        if ($beanId !== null)
            return $accountBean->export();
        return false;
    }

    public static function show(string $uuid): array
    {
        $userBean = R::findOne(self::TABLE_NAME, 'user_uuid = ?', [$uuid]);
        if (!$userBean) {
            throw new InvalidValidationException('Invalid User UUID');
        }
        return $userBean->export();
    }

    public static function get(string $uuid): array
    {
        $userBean = R::findOne(self::TABLE_NAME, 'user_uuid = ?', [$uuid]);
        if (!$userBean) {
            throw new InvalidValidationException('Invalid User UUID');
        }
        return $userBean->export();
    }
}