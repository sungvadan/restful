<?php

namespace KnpU\CodeBattle\Api;

use Symfony\Component\HttpKernel\Exception\HttpException;

class ApiProblemException extends HttpException
{
    private $apiProblem;

    public function __construct(ApiProblem $apiProblem, \Exception $previous = null, array $headers=array(), $code=0)
    {
        $statusCode = $apiProblem->getStatusCode();
        $message = $apiProblem->getTitle();
        $this->apiProblem = $apiProblem;
        parent::__construct($statusCode, $message, $previous, $headers, $code);
    }

    /**
     * @return mixed
     */
    public function getApiProblem()
    {
        return $this->apiProblem;
    }

}