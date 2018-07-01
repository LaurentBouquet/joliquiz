#!/bin/sh

echo "$ENV_DIR";

for e in $(ls $ENV_DIR); do
  echo "$e=$(cat $ENV_DIR/$e)"
  export "$e=$(cat $ENV_DIR/$e)"
done

# env;
# config;
echo "Import initial production data dump to joliquiz database : $DATABASE_URL";
psql --file joliquiz-initial-prod-data.dump.sql $DATABASE_URL;
