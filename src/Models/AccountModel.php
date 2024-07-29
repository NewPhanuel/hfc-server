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

        $beanId = R::store($accountBean);
        R::close();

        if ($beanId !== null)
            return true;
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