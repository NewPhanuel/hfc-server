<?php
declare(strict_types=1);

namespace DevPhanuel\Controllers;

use DevPhanuel\Exception\InvalidValidationException;
use DevPhanuel\Models\BlogModel;
use DevPhanuel\Validation\SchemaValidation;
use DevPhanuel\Models\Entity\BlogEntity;
use PH7\JustHttp\StatusCode;
use Ramsey\Uuid\Nonstandard\Uuid;

class BlogsController
{
    private const DATE_TIME_FORMAT = 'Y-m-d H:i:s';
    private SchemaValidation $SchemaValidation;

    public function __construct()
    {
        $this->SchemaValidation = new SchemaValidation();
    }

    public function index(): void
    {
        $blogs = BlogModel::index();
        if (!$blogs) {
            response(StatusCode::NO_CONTENT, errorMessage('No Content', 'No content found in the server', StatusCode::NO_CONTENT));
            return;
        }
        foreach ($blogs as $blog) {
            unset($blog['id']);
        }
        response(StatusCode::OK, successMessage('All Blogs on the server', $blogs));
        return;
    }

    public function store(array $params): void
    {
        $data = $params['data'];
        $user = $params['user'];

        if (!$this->SchemaValidation->validateBlogschema($data)) {
            throw new InvalidValidationException('Schema does not follow validation rules');
        }

        $data->blogUuid = (string) Uuid::uuid4();
        $data->createdAt = date(self::DATE_TIME_FORMAT);
        $data->updatedAt = date(self::DATE_TIME_FORMAT);

        $blogEntity = new BlogEntity();
        $blogEntity->setBlogUuid($data->blogUuid)->setName($data->blog_name)->setBody($data->body)
            ->setImage($data->blog_image)->setCreatedBy($user->data->uuid)->setCreatedAt($data->createdAt)
            ->setUpdatedAt($data->updatedAt);

        BlogModel::store($blogEntity);
        response(StatusCode::CREATED, successMessage('Blog successfully created', $data));
    }

    public function show(array $params): void
    {
        $uuid = $params['uuid'];

        if (!$this->SchemaValidation->validateUuid($uuid)) {
            throw new InvalidValidationException('Invalid Blog UUID');
        }

        $blog = BlogModel::show($uuid);
        unset($blog['id']);
        response(StatusCode::OK, successMessage('Blog successfully retrieved from the server', $blog));
        return;
    }

    public function update(array $params): void
    {
        $data = $params['data'];
        $uuid = $params['uuid'];

        if (!$this->SchemaValidation->validateUuid($uuid))
            throw new InvalidValidationException('Invalid Blog UUID');

        if (!$this->SchemaValidation->validateBlogschemaForUpdate($data))
            throw new InvalidValidationException('Schema does not follow validation rules');

        $data->updatedAt = date(self::DATE_TIME_FORMAT);

        $blogEntity = new BlogEntity();

        if (isset($data->blog_name))
            $blogEntity->setName($data->blog_name);
        if (isset($data->body))
            $blogEntity->setBody($data->body);
        if (isset($data->blog_image))
            $blogEntity->setImage($data->blog_image);
        $blogEntity->setUpdatedAt($data->updatedAt);

        $blog = BlogModel::update($uuid, $blogEntity);
        unset($blog['id']);
        response(StatusCode::OK, successMessage('Blog successfully updated', $blog));
    }

    public function destroy(array $params): void
    {
        $uuid = $params['uuid'];

        if (!$this->SchemaValidation->validateUuid($uuid)) {
            throw new InvalidValidationException('Invalid Blog UUID');
        }

        if (BlogModel::destroy($uuid)) {
            response(StatusCode::OK, successMessage('Blog deleted successfully'));
            return;
        }

        response(StatusCode::INTERNAL_SERVER_ERROR, errorMessage('SQLError', 'For some reason, the blog could not be deleted', StatusCode::INTERNAL_SERVER_ERROR));
        return;
    }
}