#!/bin/sh

export DATABASE_URL=$(cat $ENV_DIR/DATABASE_URL)

#psql --file joliquiz-initial-prod-structure.dump.sql $DATABASE_URL;
psql --file joliquiz-initial-prod-languages.dump.sql $DATABASE_URL;
psql --file joliquiz-initial-prod-users.dump.sql $DATABASE_URL;
#psql --file joliquiz-initial-prod-exemples.dump.sql $DATABASE_URL;
