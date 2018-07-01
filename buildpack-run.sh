#!/bin/sh

export DATABASE_URL;
echo "Import initial production data dump to joliquiz database : ${DATABASE_URL}";
psql --file joliquiz-initial-prod-data.dump.sql ${DATABASE_URL};
