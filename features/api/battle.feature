Feature:
  In order to prove my programmers' worth against projects
  As an API client
  I need to be able to create and view battles

  Scenario: Creating a new battle
    Given there is a project called "my_project"
    And there is a user "weaverryan" with password "test"
    And there is a programmer called "Fred"
    And I have the payload:
     """
     {
     "programmerId": "%programmers.Fred.id%",
     "projectId": "%projects.my_project.id%"
     }
     """
    When I request "POST /api/battles"
    Then the response status code should be 201
    And the "Location" header should exist
    And the "didProgrammerWin" property should exist


