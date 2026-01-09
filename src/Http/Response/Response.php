<?php

namespace Larawise\Http\Response;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Tappable;
use Srylius\Concerns\Statusable;
use Srylius\Support\Enums\Response as ResponseStatus;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

/**
 * Srylius - The ultimate symphony for technology architecture!
 *
 * @package     Larawise
 * @subpackage  Core
 * @version     v1.0.0
 * @author      Selçuk Çukur <hk@selcukcukur.com.tr>
 * @copyright   Srylius Teknoloji Limited Şirketi
 *
 * @see https://docs.larawise.com/ Larawise : Docs
 */
class Response extends SymfonyResponse implements Responsable
{
    // Traits
    use Conditionable, Tappable, Statusable;

    /**
     * The response status code.
     *
     * @var int
     */
    protected $code = 200;

    /**
     * The response message title.
     *
     * @var string|null
     */
    protected $title = null;

    /**
     * The response message text.
     *
     * @var string|null
     */
    protected $message = null;

    /**
     * The response data.
     *
     * @var mixed
     */
    protected $data = null;

    /**
     * The next URL for redirect response.
     *
     * @var string|null
     */
    protected $next = '';

    /**
     * The previous URL for redirect response.
     *
     * @var string|null
     */
    protected $previous = '';

    /**
     * The input data to share with the next request.
     *
     * @var array
     */
    protected $with = [];

    /**
     * The flag to indicate if input data should be shared.
     *
     * @var bool
     */
    protected $input = false;

    /**
     * The target for the redirect URL.
     *
     * @var string|null
     */
    protected $target = null;

    /**
     * The response required replace content status.
     *
     * @var bool
     */
    protected $replace = false;

    /**
     * The response error status.
     *
     * @var bool
     */
    protected $error = false;

    /**
     * The additional data to share with the next request.
     *
     * @var array
     */
    protected $additional = [];

    public $saveAction = 'save';

    /**
     * Resolve response instance for app container.
     *
     * @return static
     */
    public static function make()
    {
        return app(static::class);
    }

    /**
     * Sets the data to be shared with the next request.
     *
     * @param array $with
     *
     * @return $this
     */
    public function with($with)
    {
        $this->with = $with;

        return $this;
    }

    /**
     * Sets the response data.
     *
     * @param string|array|View $data The response data.
     *
     * @return $this
     */
    public function withData($data)
    {
        if ($data instanceof View) {
            $this->replace = true;
            $data = $data->render();
        }

        $this->data = $data;

        return $this;
    }

    /**
     * Sets the input status.
     *
     * @param bool $status The input status to set.
     *
     * @return $this
     */
    public function withInput($status = true)
    {
        $this->input = $status;

        return $this;
    }

    /**
     * Sets the message with optional HTML cleaning.
     *
     * @param string $message The message to set.
     * @param bool $clean Whether to clean HTML tags from the message.
     *
     * @return $this
     */
    public function withMessage($message, $clean = true)
    {
        if ($clean) {
            $message = html_entity_decode($message);
        }

        $this->message = $message;

        return $this;
    }

    /**
     * Sets the previous URL.
     *
     * @param string $url The URL to set as previous.
     *
     * @return $this
     */
    public function withPrevious($url)
    {
        $this->previous = $url;

        return $this;
    }

    /**
     * Sets the previous URL using a named route.
     *
     * @param string $name The name of the route.
     * @param array $parameters The route parameters.
     * @param bool $absolute Whether the URL should be absolute.
     *
     * @return $this
     */
    public function withPreviousRoute($name, $parameters = [], $absolute = true)
    {
        return $this->withPrevious(route($name, $parameters, $absolute));
    }

    /**
     * Sets the next URL.
     *
     * @param string $url The URL to set as next.
     *
     * @return $this
     */
    public function withNext($url)
    {
        $this->next = $url;

        return $this;
    }

    /**
     * Sets the next URL using a named route.
     *
     * @param string $name The name of the route.
     * @param array $parameters The route parameters.
     * @param bool $absolute Whether the URL should be absolute.
     *
     * @return $this
     */
    public function withNextRoute($name, $parameters = [], $absolute = true)
    {
        return $this->withNext(route($name, $parameters, $absolute));
    }

    /**
     * Sets the title with optional HTML cleaning.
     *
     * @param string $title The title to set.
     * @param bool $clean Whether to clean HTML tags from the title.
     *
     * @return $this
     */
    public function withTitle($title, $clean = true)
    {
        if ($clean) {
            $title = html_entity_decode($title);
        }

        $this->title = $title;

        return $this;
    }

    /**
     * Sets the status code.
     *
     * @param int $code The status code.
     *
     * @return $this
     */
    public function withStatus($code)
    {
        if ($code < 100 || $code >= 600) {
            return $this;
        }

        $this->code = $code;

        return $this;
    }

    /**
     * Sets the error status.
     *
     * @param bool $error The error status.
     *
     * @return $this
     */
    public function withError($error = true)
    {
        $this->error = $error;

        return $this;
    }

    /**
     * Sets the additional data to be shared with the next request.
     *
     * @param array $additional
     *
     * @return $this
     */
    public function withAdditional($additional)
    {
        $this->additional = $additional;

        return $this;
    }

    public function isSaving(): bool
    {
        return $this->getSubmitterValue() === $this->saveAction;
    }
    protected function getSubmitterValue(): string
    {
        return (string) request()->input('submitter');
    }

    public function usePreviousRouteName(): static
    {
        $this
            ->when(URL::previous(), function (self $httpReponse, string $previousUrl): void {
                $previousRouteName = optional(Route::getRoutes()->match(Request::create($previousUrl)))->getName();
                if ($previousRouteName && Str::endsWith($previousRouteName, '.edit')) {
                    $indexRouteName = Str::replaceLast('.edit', '.index', $previousRouteName);
                    if (Route::has($indexRouteName)) {
                        $httpReponse->withPreviousRoute($indexRouteName);
                    }
                }
            });

        return $this;
    }

    /**
     * Converts the object to an array.
     *
     * @return array
     */
    public function toArray()
    {
        $data = [
            'error'   => $this->error,
            'data'    => $this->data,
            'message' => $this->message,
        ];

        if (! empty($this->extra)) {
            $data = array_merge($data, ['extra' => $this->extra]);
        }

        return $data;
    }

    /**
     * Create an HTTP response that represents the object.
     *
     * @return JsonResource|JsonResponse|RedirectResponse|Response
     */
    public function toApiResponse()
    {
        if ($this->data instanceof JsonResource) {
            return $this->data->additional(array_merge([
                'error'     => $this->error,
                'message'   => $this->message,
            ], $this->additional));
        }

        return $this->toResponse(request());
    }

    /**
     * Handles redirect response with optional input data.
     *
     * @param string $url
     *
     * @return RedirectResponse
     */
    protected function toRedirectResponse($url)
    {
        $with = [
            ...$this->with,
            ...($this->error ? ['error_msg' => $this->message] : ['success_msg' => $this->message]),
        ];

        if ($this->input) {
            return redirect()
                ->to($url)
                ->with($with)
                ->withInput();
        }

        return redirect()
            ->to($url)
            ->with($with);
    }

    /**
     * Create an HTTP response that represents the object.
     *
     * @param Request $request
     *
     * @return JsonResponse|RedirectResponse
     */
    public function toResponse($request)
    {
        if ($request->expectsJson()) {
            $data = [
                'error' => $this->error,
                'data' => $this->data,
                'message' => $this->message,
            ];

            if ($this->additional) {
                $data = array_merge($data, ['additional' => $this->additional]);
            }

            return response()
                ->json($data, $this->code);
        }

        if ($this->isSaving() && ! empty($this->previous)) {
            return $this->toRedirectResponse($this->previous);
        } elseif (! empty($this->next)) {
            return $this->toRedirectResponse($this->next);
        }

        return $this->toRedirectResponse(URL::previous());
    }
}
