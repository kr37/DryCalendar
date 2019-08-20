# DryCalendar Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) and this project adheres to [Semantic Versioning](http://semver.org/).

## Unreleased

## To Do
- DryCalendarService:line 82'mail("kelsangrinzin...'
  Make this a settings item
- Update internal date formats to use DateTime
- Move all output to twig templates
- Fix htmlBefore and htmlAfter on calUpdate page and in controller

## 3.1.1+20190819
### Changed
- Occurrences for entries that are disabled will now not be shown.

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
