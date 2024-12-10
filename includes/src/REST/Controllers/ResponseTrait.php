<?php

declare(strict_types=1);

namespace JTL\REST\Controllers;

use Illuminate\Support\Collection;
use JTL\Model\DataModelInterface;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Response\TextResponse;
use League\Fractal\Manager;
use League\Fractal\Pagination\CursorInterface;
use League\Fractal\Resource\Collection as ResourceCollection;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;
use Psr\Http\Message\ResponseInterface;

/**
 * Trait ResponseTrait
 * @package JTL\REST\Controllers
 */
trait ResponseTrait
{
    /**
     * @var int
     */
    protected int $statusCode = 200;

    /**
     * @var Manager
     */
    protected Manager $fractal;

    /**
     * @param Manager $fractal
     */
    public function setFractal(Manager $fractal): void
    {
        $this->fractal = $fractal;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @param int $statusCode
     * @return ResponseTrait|AbstractController
     */
    public function setStatusCode(int $statusCode): self
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * @param int    $status
     * @param string $message
     * @return JsonResponse
     */
    public function sendCustomResponse(int $status, string $message): JsonResponse
    {
        return new JsonResponse(\compact('status', 'message'), $status);
    }

    /**
     * @return JsonResponse
     */
    public function sendEmptyResponse(): JsonResponse
    {
        return new JsonResponse(null, 204);
    }

    /**
     * Send this response when api user provide fields that doesn't exist in our application
     *
     * @param $errors
     * @return JsonResponse
     */
    public function sendUnknownFieldResponse($errors): JsonResponse
    {
        return new JsonResponse(['status' => 400, 'unknown_fields' => $errors], 400);
    }

    /**
     * Send this response when api user provide filter that doesn't exist in our application
     *
     * @param $errors
     * @return JsonResponse
     */
    public function sendInvalidFilterResponse($errors): JsonResponse
    {
        return new JsonResponse(['status' => 400, 'invalid_filters' => $errors], 400);
    }

    /**
     * Send this response when api user provide incorrect data type for the field
     *
     * @param $errors
     * @return ResponseInterface
     */
    public function sendInvalidFieldResponse($errors): ResponseInterface
    {
        return new JsonResponse(['invalid_fields' => $errors], 400);
    }

    /**
     * Send this response when an api user try access a resource that they don't belong
     *
     * @return ResponseInterface
     */
    public function sendForbiddenResponse(): ResponseInterface
    {
        return (new Response())->withStatus(403);
    }

    /**
     * Send 404 not found response
     *
     * @param string $message
     * @return ResponseInterface
     */
    public function sendNotFoundResponse(string $message = ''): ResponseInterface
    {
        if ($message === '') {
            $message = 'The requested resource was not found';
        }

        return new TextResponse($message, 404);
    }

    /**
     * Send empty data response
     *
     * @return ResponseInterface
     */
    public function sendEmptyDataResponse(): ResponseInterface
    {
        return new JsonResponse(['data' => new \stdClass()]);
    }

    /**
     * Return single item response from the application
     *
     * @param DataModelInterface  $item
     * @param TransformerAbstract $callback
     * @return JsonResponse
     */
    protected function respondWithItem(DataModelInterface $item, TransformerAbstract $callback): JsonResponse
    {
        return $this->respondWithArray($this->fractal->createData(new Item($item, $callback))->toArray());
    }

    /**
     * @param DataModelInterface $item
     * @return JsonResponse
     */
    protected function respondWithModel(DataModelInterface $item): JsonResponse
    {
        return $this->respondWithArray(['data' => $item->rawArray(true)]);
    }

    /**
     * Return a json response from the application
     *
     * @param array $array
     * @param array $headers
     * @return JsonResponse
     */
    protected function respondWithArray(array $array, array $headers = []): JsonResponse
    {
        return new JsonResponse($array, $this->statusCode, $headers);
    }

    /**
     * @param Collection           $collection
     * @param TransformerAbstract  $transformer
     * @param array                $headers
     * @param CursorInterface|null $cursor
     * @return JsonResponse
     */
    protected function respondWithCollection(
        Collection $collection,
        TransformerAbstract $transformer,
        array $headers = [],
        CursorInterface $cursor = null
    ): JsonResponse {
        $resource = new ResourceCollection($collection, $transformer);
        if ($cursor !== null) {
            $resource->setCursor($cursor);
        }

        return new JsonResponse($this->fractal->createData($resource)->toArray(), $this->statusCode, $headers);
    }
}
