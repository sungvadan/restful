<?php
/**
 * Created by PhpStorm.
 * User: vtphan
 * Date: 21/03/2017
 * Time: 15:01
 */

namespace KnpU\CodeBattle\Api;

use Symfony\Component\HttpFoundation\JsonResponse;

class ApiProblemResponseFactory
{
    public function createResponse(ApiProblem $apiProblem){
        $data = $apiProblem->toArray();
        if ($data['type'] != 'about:blank') {
            $data['type'] = 'http://localhost:8000/api/docs/errors#'.$data['type'];
        }

        $response = new JsonResponse(
            $data,
            $apiProblem->getStatusCode()
        );
        $response->headers->set('Content-Type', 'application/problem+json');
        return $response;
    }
}