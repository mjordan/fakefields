# Fake Fields

## Introduction

Drupal 8 module that parses out "fake fields", key/value pairs that are stored in a text field, and indexes them in Solr. The use case for this ability is allow the inclusion of "fields" on a node that are not true Drupal fields. Specifically, in the Islandora context, to allow Islandora content types to only contain core fields, and to have extra/non-standard/unicorn "fields" to be stored as a group. Such a group might look like this"

```
myfirstunicornfield: "I am a value"
mysecondnonstandardfield: "Anothe value"
athird_unicorn_field: ["first value", "second value"]
```

This module can add these key/value pairs to Drupal's Solr index like any other fields, with the required configuration.

Simon Fraser University has a large number of Islandora 7.x collections that have unique fields that do not fit nicely into MODS. My goal with this module is to explore ways of storing custom fields such that we do not need a content type per collection. 

## Installation and configuration

Best not to, unless you want to help hack out a solution to a central problem: since the `$properties` array defined in `getPropertyDefinitions()` is keyed using field names, we must get the fake field names present in the node's storage field. Loading the current node within `getPropertyDefinitions()` being indexed is eluding me. If this module is going to index these fake fields, it needs to be able to set their properties dynamically (without configuration) within `getPropertyDefinitions()`.

## Current maintainer

* [Mark Jordan](https://github.com/mjordan)

## License

[GPLv2](http://www.gnu.org/licenses/gpl-2.0.txt)
