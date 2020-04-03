#!/bin/sh

export DATABASE_URL=$(cat $ENV_DIR/DATABASE_URL)

#psql --file initial-prod-data-structure.pgsql.dump.sql $DATABASE_URL;
psql --file initial-prod-data-languages.pgsql.dump.sql $DATABASE_URL;
psql --file initial-prod-data-users.pgsql.dump.sql $DATABASE_URL;
#psql --file initial-prod-data-exemples.pgsql.dump.sql $DATABASE_URL;
