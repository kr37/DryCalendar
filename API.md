# DryCalendar API
# 3.0.0-alpha+20180406 - 2018-04-10

## TWIG Variables

### oneDay() method

*Definition*
public function oneDay($reqDateYmd = null, $atts = null, $cal = null)

*Use*
{% set atts = {'dateformat' : 'l, F j', 'occurrenceFormat' : '%2s<br>%1s'} %}
{{ craft.DryCalendar.oneDay("",atts)|raw }}

*Notes*
$reqDateYmd: Anything fitting PHP strtotime seems to work, oddly enough.

### miniCal() method

*Definition*
public function miniCal($fromDateYmd = null, $toDateYmd = null)

*Use*

*Notes*

## Craft Field Types

## Control Panel Pages

## Settings

## Examples
