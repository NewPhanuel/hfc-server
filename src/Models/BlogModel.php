<?php
declare(strict_types=1);

namespace DevPhanuel\Models;

use DevPhanuel\Models\Entity\BlogEntity;
use PH7\JustHttp\StatusCode;
use RedBeanPHP\R;

final class BlogModel
{
    private const TABLE_NAME = 'blogs';

    public static function index(): array
    {
        return R::findAll(self::TABLE_NAME);
    }

    public static function store(BlogEntity $blogEntity): int|string
    {
        $blogBean = R::dispense(self::TABLE_NAME);
        $blogBean['blog_uuid'] = $blogEntity->getBlogUuid();
        $blogBean['blog_name'] = $blogEntity->getName();
        $blogBean['body'] = $blogEntity->getBody();
        $blogBean['blog_image'] = $blogEntity->getImage();
        $blogBean['created_by'] = $blogEntity->getCreatedBy();
        $blogBean['created_at'] = $blogEntity->getCreatedAt();
        $blogBean['updated_at'] = $blogEntity->getUpdatedAt();
        $beanId = R::store($blogBean);
        R::close();
        return $beanId;
    }

    public static function update(string $uuid, BlogEntity $blogEntity): mixed
    {
        $blogBean = R::findOne(self::TABLE_NAME, 'blog_uuid = ?', [$uuid]);

        if (!$blogBean) {
            response(StatusCode::UNPROCESSABLE_ENTITY, errorMessage("Invalid Blog UUID", "Blog was not found on the server", StatusCode::UNPROCESSABLE_ENTITY));
        }

        if ($blogEntity->getName())
            $blogBean['blog_name'] = $blogEntity->getName();
        if ($blogEntity->getBody())
            $blogBean['body'] = $blogEntity->getBody();
        if ($blogEntity->getImage())
            $blogBean['blog_image'] = $blogEntity->getImage();
        $blogBean['updated_at'] = $blogEntity->getUpdatedAt();
        R::store($blogBean);
        R::close();
        return $blogBean;
    }

    public static function show(string $uuid): array
    {
        $blogBean = R::findOne(self::TABLE_NAME, 'blog_uuid = ?', [$uuid]);
        if (!$blogBean) {
            response(StatusCode::UNPROCESSABLE_ENTITY, errorMessage("Invalid Blog UUID", "Blog was not found on the server", StatusCode::UNPROCESSABLE_ENTITY));
        }
        return $blogBean->export();
    }

    public static function destroy(string $uuid): bool
    {
        $blogBean = R::findOne(self::TABLE_NAME, 'blog_uuid = ?', [$uuid]);
        if (!$blogBean) {
            response(StatusCode::UNPROCESSABLE_ENTITY, errorMessage("Invalid Blog UUID", "Blog was not found on the server", StatusCode::UNPROCESSABLE_ENTITY));
        }
        return (bool) R::trash($blogBean);
    }
}