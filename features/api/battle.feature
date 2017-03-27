Feature:
  In order to prove my programmers' worth against projects
  As an API client
  I need to be able to create and view battles

  Background:
    Given the user "weaverryan" exists
    And "weaverryan" has an authentication token "ABCD123"
    And I set the "Authorization" header to be "token ABCD123"

  Scenario: Creating a new battle
    Given there is a project called "my_project"
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
    
  Scenario: Getting a single battle
    Given there is a programmer called "Fred"
    And there is a project called "project_facebook"
    And there has been a battle between "Fred" and "project_facebook"
    When I request "GET /api/battles/%battles.last.id%"
    Then the response status code should be 200
    And the following properties should exist:
      """
      didProgrammerWin
      notes
      """
    # And the "_links.programmer.href" property should equal "/api/programmers/Fred"
    And the link "programmer" should exist and its value should be "/api/programmers/Fred"
    And the "Content-Type" header should be "application/hal+json"
    And the "_embedded.programmer.nickname" property should equal "Fred"
    And the embedded "programmer" should have a "nickname" property equal to "Fred"
    #And print last response


