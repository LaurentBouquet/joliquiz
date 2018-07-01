#!/bin/sh

export DATABASE_URL=$(cat $ENV_DIR/DATABASE_URL)

psql --file joliquiz-initial-prod-data.dump.sql $DATABASE_URL;
