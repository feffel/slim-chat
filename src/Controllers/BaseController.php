<?php
declare(strict_types=1);

namespace Chat\Controllers;

use Chat\Validation\Validator;
use Illuminate\Database\Capsule\Manager;
use League\Fractal\Manager as Fractal;
use League\Fractal\Resource\ResourceInterface;
use League\Fractal\Serializer\ArraySerializer;
use Psr\Container\ContainerInterface;
use Slim\Http\Response;

class BaseController
{
    protected Validator      $validator;
    protected Manager        $db;
    protected Fractal        $fractal;

    public function __construct(ContainerInterface $container)
    {
        $this->db        = $container->get('db');
        $this->validator = $container->get('validator');
        $this->fractal   = (new Fractal())->setSerializer(new ArraySerializer())->parseIncludes($_GET['include'] ?? '');
    }

    protected function serialize(ResourceInterface $resource): array
    {
        return $this->fractal->createData($resource)->toArray();
    }

    protected function notFound(Response $response, string $format = 'application/json'): Response
    {
        return $response->withStatus(404)->withHeader('Content-Type', $format);
    }
}
