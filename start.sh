#!/bin/bash

# Running containers and log output
echo "Launching containers..."
docker-compose up -d
docker-compose logs -f &


# Running commands in the php container and outputting logs
echo "Installing Composer Dependencies..."
docker exec -it $(docker ps -qf "name=app") sh -c "composer install"
echo "Creating table"

# Copying table.sql to the PostgreSQL container
docker cp table.sql $(docker ps -qf "name=postgresql"):/table.sql

# Executing the SQL script
docker exec -i $(docker ps -qf "name=postgresql") psql -U your_username -d postgres -f /table.sql

# Outputs a message about a successful start
echo -e "\033[0;32mThe project went up successfully!\033[0m"