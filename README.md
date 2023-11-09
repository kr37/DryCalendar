# DryCalendar plugin for Craft CMS 3.x

Event calendar with unrestricted repetition

![Screenshot](resources/img/plugin-logo.png)

## Requirements

This plugin requires Craft CMS 3.0.0-beta.23 or later.

## Installation

To install the plugin, follow these instructions.

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Then tell Composer to load the plugin:

        composer require /drycalendar

3. In the Control Panel, go to Settings → Plugins and click the “Install” button for DryCalendar.

4. There are a number of settings you must fill in for the plugin to work. 
   In the CP, Settings → Plugins → DryCalendar Settings and fill in 
   especially the items further down, **Deep down admin stuff**

* timezone
* Category Field's Handle ~ in the entry, what field tells us the category?
* Entry's 'Calendar Text' Field's Handle ~ in the entry, this field will 
  usually provide the text for the event (if blank, it defaults to the title of
  the entry)
* _Start Date Field Handle_ ~ In the entry, what field tells us the start date
* _Image Field Handle_ ~ In the entry, what field contains an optional image 
  for these events
* _urlFieldHandle_ ~ Where to get the URL for any occurrence. THIS IS NOT CURRENTLY USED


Field-handles in categories
* _Category's CSS Field Handle_ ~ In the category, what field tells us the CSS

**It is also necessary to fill in some of the date formats. Examples are given.**

## DryCalendar Overview

-Insert text here-

## Configuring DryCalendar

-Insert text here-

## Using DryCalendar

-Insert text here-

## DryCalendar Roadmap

Some things to do, and ideas for potential features:

* Release it

Brought to you by [KR37](http://drycalendar.blogspot.com/)
