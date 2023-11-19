# DryCalendar Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) and this project adheres to [Semantic Versioning](http://semver.org/).

## Unreleased

## To Do
- DryCalendarService:line 82'mail("kelsan...'
  Make this a settings item
- Update internal date formats to use DateTime
- Fix htmlBefore and htmlAfter on calUpdate page and in controller

## 4.0.2+20231118
### Changed
- Updated templates for settings and calendarOccurrencesField to include isLivestreamed overrides for category, entry override, and occurrence override
- Added 'streamed' to the addOccurrence controller.
- Added 'streamed' to calUpdate.
### Fixed
- Changed the Garnish JS to calendarText instead of calendarTitle.
- Fixed the count on the occurrences field, by changing to canonicalId.
- Calupdate 'update' button is styled.
## 4.0.1+20231115
### Changed
- Clarified settings instructions
- Minor code tidying
## 4.0.0+20231112
### Changed
- Added fields 'url' and 'streamed' to drycalendar table.
- Updated composer.json (added schemaVersion)
- Removed old migrations
### Fixed
- Added migrations/Install.php for creating and removing tables on install/uninstall
- Fixed not using settings for field handles on category CSS, main category.
## 3.1.1+20200225
### Fixed
- Fixed CSS for minical CP field that broke after the Craft 3.4 update

## 3.1.0+20181211
### Changed
- Field is functional (still doesn't deal with multiple occurrences on a day)
- Urls are now relative

## 3.0.0-alpha+20180429
### Changed
- Got the field to show up. Clicking on it still doesn't do anything.

## 3.0.0-alpha+20180425
### Changed
- Initial attempt to port from Craft 2 to Craft 3
