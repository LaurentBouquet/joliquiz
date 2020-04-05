#!/bin/sh

export DATABASE_URL=$(cat $ENV_DIR/DATABASE_URL)

#psql --file initial-prod-data-structure.pgsql.sql $DATABASE_URL;
psql --file initial-prod-data-languages.pgsql.sql $DATABASE_URL;
psql --file initial-prod-data-users.pgsql.sql $DATABASE_URL;
#psql --file initial-prod-data-exemples.pgsql.sql $DATABASE_URL;
