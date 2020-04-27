# docker_project
Run next commands from repository:

1) docker-compose build
2) docker-compose up -d
3) docker-compose exec php php artisan key:generate (run once)
4) docker-compose exec php php artisan migrate (run after each docker-compose up-d)

Entypoint: http://localhost:8088/api/documentation
