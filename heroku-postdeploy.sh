#!/bin/sh

echo "HEROKU postdeploy XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX";
echo "psql --file joliquiz-initial-prod-data.dump.sql $DATABASE_URL";
