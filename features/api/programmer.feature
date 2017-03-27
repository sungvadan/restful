Feature: Programmer
  In order to battle projects
  As an API client
  I need to be able to create programmers and power them up

  Background:
    Given the user "weaverryan" exists
    And "weaverryan" has an authentication token "ABCD123"
    And I set the "Authorization" header to be "token ABCD123"

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
    And the "userId" property should not exist
    And the "_links.self.href" property should equal "/api/programmers/UnitTester"
    #And print last response

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

  Scenario: PATCH to edit a programmer
    Given the following programmers exist:
      | nickname    | avatarNumber | tagLine
      | CowboyCoder | 5            | foo
    And I have the payload:
      """
        {
          "tagLine": "giddyup"
        }
      """
    When I request "PATCH /api/programmers/CowboyCoder"
    Then the response status code should be 200
    And the "avatarNumber" property should equal "5"
    And the "nickname" property should equal "CowboyCoder"
    And the "tagLine" property should equal "giddyup"

  Scenario: Validation Errors
    Given I have the payload:
      """
        {
          "avatarNumber" : "2",
          "tagLine": "I'm from a test!"
        }
      """
    When I request "POST /api/programmers"
    Then the response status code should be 400
    And the following properties should exist:
      """
      type
      title
      errors
      """
    And the "errors.nickname" property should exist
    And the "errors.avatarNumber" property should not exist
    And the "Content-type" header should be "application/problem+json"

  Scenario: Error response on invalid JSON
    Given I have the payload:
      """
        {
          "avatarNumber" : "2
          "tagLine": "I'm from a test!"
        }
      """
    When I request "POST /api/programmers"
    Then the response status code should be 400
    And the "Content-type" header should be "application/problem+json"
    And the "type" property should contain "invalid_body_format"

  Scenario: Proper 404 exception on no programmer
    When I request "GET /api/programmers/fake"
    Then the response status code should be 404
    And the "Content-type" header should be "application/problem+json"
    And the "type" property should equal "about:blank"
    And the "title" property should equal "Not Found"
    And the "detail" property should contain "This programmer has deserted!"