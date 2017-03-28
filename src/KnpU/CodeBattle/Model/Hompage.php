<?php
namespace KnpU\CodeBattle\Model;

use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @Serializer\ExclusionPolicy("all")
 *
 * @Hateoas\Relation("self",
 *      href = @Hateoas\Route(
 *          "api_homepage",
 *     ),
 *     attributes = {"title": "Your API starting point" }
 * )
 * @Hateoas\Relation("programmers",
 *      href = @Hateoas\Route(
 *          "api_programmers_list",
 *     ),
 *     attributes = {"title": "All of the programmers" }
 * )
 */
class Hompage
{
    private $message = "Welcome to the CodeBattles API! Look around at the _links to browse the API. And hav";
}