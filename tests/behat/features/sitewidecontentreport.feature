Feature: Site wide content report
  As a website user
  I want to use the site wide content report

  Background:

    Given the "group" "EDITOR group" has permissions "CMS_ACCESS_LeftAndMain"
    And a "page" "My page" has the "Content" "<p>My content</p>"
    And a "file" "test1.pdf"

    Given I am logged in with "ADMIN" permissions

    # Create a subsite
    And I go to "/admin/subsites"
    And I press the "Add Subsite" button
    And I fill in "Subsite Name" with "My subsite"
    And I press the "Create" button

    # Add a page to the subsite
    Given a "page" "The subsite page" with "SubsiteID"="1" and "Content"="<p>My subsite content</p>"

  Scenario: Operate site wide content report

    When I go to "/admin/reports"
    And I follow "Site-wide content report"
    
    # Show all Pages
    Then I should see "My page"
    And I should see "my-page"
    And I should see "Main site"
    And I should see "The subsite page"
    And I should see "the-subsite-page"
    And I should see "My subsite"

    # Show all files
    And I should see "test1.pdf"
    And I should see "Adobe Acrobat PDF file"

    # Click on a page to open it
    When I go to "/admin/reports"
    And I follow "Site-wide content report"
    When I follow "My page"
    Then I should see a ".mce-tinymce" element

    # Click on a file to open it
    When I go to "/admin/reports"
    And I follow "Site-wide content report"
    When I follow "test1" with javascript
    Then I should see a "#Form_fileEditForm" element

    # Filter by subsite
    When I go to "/admin/reports"
    And I follow "Site-wide content report"
    And I select "My subsite" from "Filter by"
    And I press the "Filter" button
    Then I should not see "My page"
