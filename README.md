# ERCore Migrate
Migrating ERCore data from Drupal 7 to Drupal 8. Customization of fields per site is expected.
This is intended to be used as a template for ERCore migrations. Since all Drupal 7 ERCore (ER) modules appear to be
customized, each migration will be a unique process. Download this module and customize it as necessary to work with your
specific instance of the ERCore module. If you make improvements on this module that would benefit later users, add a
pull request to add them to the master branch. If your changes are unique to your site, create a jurisdiction specific branch
and make your changes there.

## Instructions
This will import the default ERCore fields from the original ER module as it
is, it will not import some fields from the updated 7.2 version of the module.
This version will need some alternative feeds mapped.

All migrations have been tested using Drush from the command line. The
Migration UI has only been minimally tested and wasn't reliably working due
to limitations of the test environment.

## Migration Dependencies

The migration dependencies seems to be buggy.They had to be deleted from this
module to get this to work.

### Migration Order

- Components
- Institutions
- Users
- Engagements
- Events
- Collaborators
- Collaborations
- Any other migrations

All migrations can be reran using the `--update` argument. Once they have all
run once, they can be run in any order.