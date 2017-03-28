<?php

namespace KnpU\CodeBattle\Controller\Api;

use Hateoas\Representation\CollectionRepresentation;
use KnpU\CodeBattle\Api\ApiProblemException;
use KnpU\CodeBattle\Controller\BaseController;
use KnpU\CodeBattle\Model\Hompage;
use Silex\Application;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use KnpU\CodeBattle\Model\Programmer;
use KnpU\CodeBattle\Api\ApiProblem;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ProgrammerController extends BaseController
{
    protected function addRoutes(ControllerCollection $controllers)
    {
        $controllers->get('/api', array($this, 'homepageAction'))
            ->bind('api_homepage');
        $controllers->post('/api/programmers', array($this, 'newAction'));
        $controllers->get('/api/programmers', array($this, 'listAction'))
            ->bind('api_programmers_list');
        $controllers->get('/api/programmers/{nickname}', array($this, 'showAction'))
            ->bind('api_programmers_show');
        $controllers->put('/api/programmers/{nickname}', array($this, 'updateAction'));
        $controllers->match('/api/programmers/{nickname}', array($this, 'updateAction'))
            ->method('PATCH');
        $controllers->delete('/api/programmers/{nickname}', array($this, 'deleteAction'));
        $controllers->get('/api/programmers/{nickname}/battles', array($this, 'listBattlesAction'))
            ->bind('api_programmers_battles_list');
    }

    public function homepageAction(){
        $homepage = new Hompage();
        return $this->createApiResponse($homepage);
    }

    public function listBattlesAction($nickname)
    {
        $programmer = $this->getProgrammerRepository()->findOneByNickname($nickname);
        if (!$programmer) {
            $this->throw404('Oh no! This programmer has deserted! We\'ll send a search party!');
        }

        $battles = $this->getBattleRepository()
            ->findAllBy(array('programmerId' => $programmer->id));
        $collection = new CollectionRepresentation(
            $battles,
            'battles'
        );
        $response = $this->createApiResponse($collection,200);
        return $response;

    }

    public function newAction(Request $request)
    {
        $this->enforceUserSecurity();
        $programmer = new Programmer();

        $this->handleRequest($request, $programmer);
        $errors = $this->validate($programmer);
        if(!empty($errors)){
            $this->throwApiProblemValidationException($errors);
        }
        $this->save($programmer);

        $response = $this->createApiResponse($programmer,201);

        $url = $this->generateUrl('api_programmers_show',array(
            'nickname' => $programmer->nickname
        ));
        $response->headers->set('Location', $url);

        return $response;
    }

    public function updateAction(Request $request, $nickname)
    {

        $programmer = $this->getProgrammerRepository()->findOneByNickname($nickname);
        $this->enforceProgrammerOwnershipSecurity($programmer);
        if(!$programmer){
            $this->throw404('Crap! This programmer has deserted! We\'ll send a search party');
        }

        $this->handleRequest($request, $programmer);
        $errors = $this->validate($programmer);
        if(!empty($errors)){
            $this->throwApiProblemValidationException($errors);
        }
        $this->save($programmer);

        $response = $this->createApiResponse($programmer,200);


        return $response;
    }

    public function showAction($nickname)
    {
        $programmer = $this->getProgrammerRepository()->findOneByNickname($nickname);
        if(!$programmer){
            $this->throw404('Crap! This programmer has deserted! We\'ll send a search party');
        }

        $response = $this->createApiResponse($programmer,200);

        return $response;

    }

    public function deleteAction($nickname)
    {
        $programmer = $this->getProgrammerRepository()->findOneByNickname($nickname);
        $this->enforceProgrammerOwnershipSecurity($programmer);
        if ($programmer) {
            $this->delete($programmer);
        }

        return new Response(null, 204);


    }

    public function listAction()
    {
        $programmers = $this->getProgrammerRepository()->findAll();
        $collection = new CollectionRepresentation(
            $programmers,
            'programmers'
        );
        $response = $this->createApiResponse($collection,200);
        return $response;

    }


    private function handleRequest(Request $request, Programmer $programmer)
    {
        $data = $this->decodeRequestBodyIntoParameters($request);
        $isNew = !$programmer->id;

        $apiProperties = array('avatarNumber', 'tagLine');
        if($isNew){
            $apiProperties[] = 'nickname';
        }
        foreach ($apiProperties as $property){
            // if a property is missing on PATCH, that's ok - just skip it
            if(!$data->has($property) && $request->isMethod('PATCH')){
                continue;
            }
            $programmer->$property = $data->get($property);
        }
        $programmer->userId = $this->getLoggedInUser()->id;


    }



}
