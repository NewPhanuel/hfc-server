<?php
declare(strict_types=1);

use PH7\JustHttp\StatusCode;
use PH7\PhpHttpResponseHeader\Http;

/**
 * Get the base path
 *
 * @param string $path
 * @return string
 */
function basePath(string $path): string
{
    return __DIR__ . '/' . $path;
}

/**
 * Loads a view and passes data into it
 *
 * @param string $name
 * @param array $data
 * @return void
 */
function loadView(string $name, array $data = []): void
{
    $viewPath = basePath("App/views/{$name}.view.php");

    if (file_exists($viewPath)) {
        extract($data);
        require $viewPath;
    } else {
        echo "View {$name} not found!";
    }
}

/**
 * Loads a array of partials
 *
 * @param string ...$partials
 * @return void
 */
function loadPartial(string ...$partials): void
{
    foreach ($partials as $partial) {
        $partialPath = basePath("App/views/partials/{$partial}.php");

        if (file_exists($partialPath)) {
            require $partialPath;
        } else {
            echo "Partial {$partial} not found!";
        }
    }
}

/**
 * Loads a partial with data
 *
 * @param string $partial
 * @param array $data
 * @return void
 */
function loadPartialWithData(string $partial, array $data): void
{
    $partialPath = basePath("App/views/partials/{$partial}.php");

    if (file_exists($partialPath)) {
        extract($data);
        require $partialPath;
    } else {
        echo "Partial {$partial} not found!";
    }
}

/**
 * Prints out formatted values of any variable
 *
 * @param mixed $object
 * @return void
 */
function inspect(mixed $object): void
{
    echo '<pre>';
    print_r($object);
    echo '</pre><br />';
}

/**
 * Prints out formatted values of any variable and kills the script
 *
 * @param mixed $object
 * @return void
 */
function inspectAndDie(mixed $object): void
{
    echo '<pre>';
    var_export($object);
    echo '</pre>';
    die();
}

/**
 * Formats the Salary value from the database
 *
 * @param string $salary
 * @return string
 */
function formatSalary(string $salary): string
{
    return '₦' . number_format(floatval($salary));
}

/**
 * Makes a script or any form if injection treated as a string
 *
 * @param string $dirty
 * @return string
 */
function sanitize(string $dirty): string
{
    return filter_var(strip_tags(trim($dirty)), FILTER_SANITIZE_SPECIAL_CHARS);
}

/**
 * Redirects to a given page
 *
 * @param string $url
 * @return void
 */
function redirect(string $url): void
{
    header("Location: {$url}");
    exit;
}

/**
 * Truncates content and adds elllipsis
 *
 * @param string $content
 * @param integer $maxLength
 * @return string
 */
function truncate(string $content, int $maxLength): string
{
    if (strlen($content) > $maxLength) {
        $content = substr($content, 0, $maxLength - 3) . '...';
    }
    return $content;
}

/**
 * Clears a cookie
 *
 * @param string $cookie
 * @return void
 */
function clearCookie(string $cookie): void
{
    $params = session_get_cookie_params();
    setcookie($cookie, '', time() - 86400, $params['path'], $params['domain']);
}

/**
 * Composes a success message to be sent to the client
 *
 * @param string|null $message
 * @param array $data
 * @return array
 */
function successMessage(?string $message = '', array|object $data = []): array
{
    return [
        'success' => [
            'message' => $message,
            'data' => $data,
        ],
    ];
}

/**
 * Composes an error message to be sent to the client
 *
 * @param string|null $type
 * @param string|null|array $message
 * @param mixed $code
 * @return array
 */
function errorMessage(?string $type, string|null|array $message, mixed $code = 0): array
{
    return [
        'error' => [
            'type' => $type,
            'message' => $message,
            'code' => $code,
        ],
    ];
}

/**
 * Returns a response with data and HTTP
 * header to the client
 *
 * @param integer $statusCode
 * @param array $response
 * @return void
 */
function response(int $statusCode, array|object $response = []): void
{
    Http::setHeadersByCode($statusCode);
    Http::setContentType('application/json');
    echo json_encode($response);
    exit;
}