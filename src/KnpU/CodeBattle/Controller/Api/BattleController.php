<?php

namespace KnpU\CodeBattle\Controller\Api;


use KnpU\CodeBattle\Api\ApiProblem;
use KnpU\CodeBattle\Api\ApiProblemException;
use KnpU\CodeBattle\Controller\BaseController;
use KnpU\CodeBattle\Security\Token\ApiToken;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;

class BattleController extends BaseController
{
    protected function addRoutes(ControllerCollection $controllers){
        $controllers->post('/api/battles',array($this, 'newAction'));
        $controllers->get('/api/battles/{id}',array($this, 'showAction'))
        ->bind('api_battle_show');
    }

    public function newAction(Request $request)
    {
        $this->enforceUserSecurity();
        $data = $this->decodeRequestBodyIntoParameters($request);

        $programmerId = $data->get('programmerId');
        $projectId = $data->get('programmerId');
        $programmer = $this->getProgrammerRepository()->find($programmerId);
        $project = $this->getProjectRepository()->find($projectId);

        $errors = array();
        if(!$programmer){
            $errors['programmerId'] = 'Invalid or missing programmerId';
        }
        if(!$project){
            $errors['projectId'] = 'Invalid or missing projectId';
        }
        if($errors){
            $this->throwApiProblemValidationException($errors);
        }

        $battle = $this->getBattleManager()->battle($programmer, $project);
        $response = $this->createApiResponse($battle, 201);
        $response->headers->set('Location','Todo');
        return$response;
    }

    public function showAction($id){
        $battle = $this->getBattleRepository()->find($id);
        if(!$battle){
            $this->throw404('No battle found for id '.$id);
        }
        return $this->createApiResponse($battle);
    }



}