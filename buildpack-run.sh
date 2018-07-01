#!/bin/sh

heroku config -s
heroku run printenv
echo "Import initial production data dump to joliquiz database : $DATABASE_URL";
psql --file joliquiz-initial-prod-data.dump.sql $DATABASE_URL;
