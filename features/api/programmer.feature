Feature: Programmer
  In order to battle projects
  As an API client
  I need to be able to create programmers and power them up

  Background:
    Given the user "weaverryan" exists

  Scenario: Create a programmer
    Given I have the payload:
      """
        {
          "nickname": "ObjectOrienter",
          "avatarNumber" : "2",
          "tagLine": "I'm from a test!"
        }
      """
    When I request "POST /api/programmers"
    Then the response status code should be 201
    And the "Location" header should be "/api/programmers/ObjectOrienter"
    And the "nickname" property should equal "ObjectOrienter"


  Scenario: Get on programmer:
    Given the following programmers exist:
      | nickname   | avatarNumber |
      | UnitTester | 3            |
    When I request "GET /api/programmers/UnitTester"
    Then the response status code should be 200
    And the following properties should exist:
      """
      nickname
      avatarNumber
      powerLevel
      tagLine
      """
    And the "nickname" property should equal "UnitTester"

  Scenario: GET a collection of programmers
    Given the following programmers exist:
      | nickname    | avatarNumber |
      | UnitTester  | 3            |
      | CowboyCoder | 5            |
    When I request "GET /api/programmers"
    Then the response status code should be 200
    And the "programmers" property should be an array
    And the "programmers" property should contain 2 items

  Scenario: PUT to edit a programmer
    Given the following programmers exist:
      | nickname    | avatarNumber | tagLine
      | CowboyCoder | 3            | foo
    And I have the payload:
      """
        {
          "nickname": "CowGirlCoder",
          "avatarNumber" : "2",
          "tagLine": "foo"
        }
      """
    When I request "PUT /api/programmers/CowboyCoder"
    And print last response
    Then the response status code should be 200
    And the "avatarNumber" property should equal "2"
    And the "nickname" property should equal "CowboyCoder"

  Scenario: Delete a programmer
    Given the following programmers exist:
      | nickname    | avatarNumber | tagLine
      | CowboyCoder | 3            | foo
    When I request "DELETE /api/programmers/CowboyCoder"
    Then the response status code should be 204