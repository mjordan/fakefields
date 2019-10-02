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

### Rough notes for how to index fake fields managed by this module

#### Create the storage field

1. Create a field in your Islandora content type(s) to hold the raw fake field data. This field should be of type 'Text (plain, long)'. Note the new field's machine name, e.g., `field_fake_fields` since you will need it below.
1. Adjust permissions on this field so it is not viewable by users other than the owner and admin users. You do not want general users to be able to view this field.

Your storage field is now ready and can be populated with fake field data.

#### Populate the storage field

To do.

#### Index the fake field data

1. Visit the Search API configuration at `admin/config/search/search-api`.
1. Click on "Default Solr content index".
1. Click on the "Processors" tab.
1. Enable the "Index fake fields" processor.
1. Scroll down and click on the "Index fake fields" tab.
1. In the "Machine namd of field that holds your 'fake fields' field, add the machine name of your storage field.
1. In the "Fake field names" field, add (one per line) the names of your fake fields.
1. Click "Save".
1. At the top of the page, click on the "Fields" tab.
1. Click on the "Add fields" button.
1. Add your fake fields (don't forget to click on the "Done" button at the bottom of the field list) and choose the desired label and type for each. Refer to the "Data types" guide at the bottom of the page to determine which type to choose.
1. At the bottom of the page, click on the "Save changed" button.

Now, when nodes are created or updated, or you reindex your site, the fake field data will appear in the Solr index just like any other data.

## Current maintainer

* [Mark Jordan](https://github.com/mjordan)

## License

[GPLv2](http://www.gnu.org/licenses/gpl-2.0.txt)
