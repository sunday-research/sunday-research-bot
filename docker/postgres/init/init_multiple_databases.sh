#!/bin/bash
set -e
set -u

function create_user_and_database() {
  local database=$1
  local user=$2
  local password=$3

  echo "Creating user '$user' and database '$database'"

  # Создание пользователя и базы
  psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "postgres" <<-EOSQL
    CREATE USER $user WITH PASSWORD '$password';
    CREATE DATABASE $database;
EOSQL

  # Назначение прав и смена владельца схемы public в новой БД
  psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "$database" <<-EOSQL
    GRANT ALL PRIVILEGES ON DATABASE $database TO $user;
    ALTER SCHEMA public OWNER TO $user;
EOSQL
}

if [ -n "${POSTGRES_MULTIPLE_DATABASES:-}" ]; then
  echo "Multiple database creation requested: $POSTGRES_MULTIPLE_DATABASES"
  IFS=',' read -ra DB_CONFIGS <<< "$POSTGRES_MULTIPLE_DATABASES"
  for db_config in "${DB_CONFIGS[@]}"; do
    IFS=':' read -r db user password <<< "$db_config"
    create_user_and_database "$db" "$user" "$password"
  done
  echo "Multiple databases created"
fi
