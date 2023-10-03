Feature: Retrieve dashboard

  @createSchema
  Scenario: Retrieve platform dashboard json
    Given I add Authorization header
     When I add "Accept" header equal to "application/json"
      And I send a "GET" request to "my/dashboard"
     Then the response status code should be 200
      And the response should be in JSON
      And the header "Content-Type" should be equal to "application/json; charset=utf-8"
      And the JSON should be like:
      """
      {
          "admin": {
              "username": "admin",
              "name": "admin",
              "lastname": "match:type('some lastname')",
              "email": "match:regexp(/\\w+@\\w+\\.\\w+/)"
          },
          "recentActivity": [
              {
                  "id": "match:type(0)",
                  "name": "Irontec_e2e",
                  "nif": "",
                  "sipDomain": "sip.irontec.com",
                  "maxCalls": 0
              },
              {
                  "id": "match:type(1)",
                  "name": "DemoBrand",
                  "nif": "",
                  "sipDomain": "",
                  "maxCalls": 0
              }
          ],
          "brandNumber": "match:type(2)",
          "clientNumber": "match:type(5)",
          "userNumber": "match:type(1)"
      }
      """
