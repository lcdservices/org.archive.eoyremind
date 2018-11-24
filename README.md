# org.archive.eoyremind

This extension supports the Internet Archive's end of year campaign reminder workflow.

If a person requests to be reminded later to donate to the campaign, an activity is created in their record with a Completed status. If they later choose to opt out of the reminder, that status is changed to Cancelled. This is handled through external scripts.

If the person has asked to be reminded and later donates, this extension searches for a Remind Me Later activity within the configured date range and changes the status to Not Required. By changing the status, we ensure the user does not receive scheduled reminders which condition on the Completed status.

To use this extension, first configure the date range. In CiviCRM v5.8.0+ this can be done by visiting the 'EOY Remind Workflow Settings' page under the Administer menu item.

In prior versions this can be done by visiting the API explorer.

* go to Support > Developer > API Explorer
* select Entity = 'Setting' and Action = 'create'
* in the parameter dropdown, search for and select 'EOY Remind Start Date' and 'EOY Remind End Date'. set the values using 'Y-m-d' format.

When the extension reviews newly created contributions and cycles through Remind Me Later activities, it will limit the analysis to those falling within this date range. This allows the organization to reuse the extension and workflow from year to year for both the EOY campaign and potentially other outreach efforts.

Additional notes:

* the extension only acts on Contributions with a financial type of 'Donation' (id = 1) and status = Completed
* the extension only acts during the initial contribution creation. updates to existing contributions do not alter activities. 

The extension is licensed under [AGPL-3.0](LICENSE.txt).

## Requirements

* PHP v5.4+
* CiviCRM 5.x+

## Installation (Web UI)

This extension has not yet been published for installation via the web UI.

## Installation (CLI, Zip)

Sysadmins and developers may download the `.zip` file for this extension and
install it with the command-line tool [cv](https://github.com/civicrm/cv).

```bash
cd <extension-dir>
cv dl org.archive.eoyremind@https://github.com/FIXME/org.archive.eoyremind/archive/master.zip
```

## Installation (CLI, Git)

Sysadmins and developers may clone the [Git](https://en.wikipedia.org/wiki/Git) repo for this extension and
install it with the command-line tool [cv](https://github.com/civicrm/cv).

```bash
git clone https://github.com/lcdservices/org.archive.eoyremind.git
cv en eoyremind
```
