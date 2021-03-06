<?php

namespace AppBundle\Service\JsonApi\Response;


use AppBundle\Service\JsonApi\Serializer\JsonApiSerializer;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class JsonApiResponseBuilder
 * @package AppBundle\Service\JsonApi\Response
 */
class JsonApiResponseBuilder
{
    /** @var JsonApiSerializer $jsonApiSserializer */
    private $jsonApiSserializer;

    /**
     * JsonApiResponseBuilder constructor.
     * @param JsonApiSerializer $serializer
     */
    public function __construct(JsonApiSerializer $serializer)
    {
        $this->jsonApiSserializer = $serializer;
    }

    /**
     * @param mixed $payLoad
     * @param $statusCode
     *
     * @return Response
     */
    public function make($payLoad, $type, $statusCode)
    {
        $serializedPayload = $this->jsonApiSserializer->serialize($payLoad, $type);

        $response = new Response();
        $response->setContent($serializedPayload);
        $response->setStatusCode($statusCode);
        $response->headers->set('Content-Type', 'application/vnd.api+json');

        return $response;
    }
}
