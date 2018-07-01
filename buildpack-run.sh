#!/bin/sh

echo "$ENV_DIR";

for e in $(ls $ENV_DIR); do
  echo "$e=$(cat $env_dir/$e)"
  export "$e=$(cat $env_dir/$e)"
  :
done

# env;
# config;
echo "Import initial production data dump to joliquiz database : $DATABASE_URL";
psql --file joliquiz-initial-prod-data.dump.sql $DATABASE_URL;
