# Прототип приложения для учета товаров на складе

Тестовое задание на позицию backend-разработчик

## Установка

Для развертывания потребуются git, docker с установленным docker compose

1. `git clone https://github.com/vladkorovyakov/warehouse.git`
2. `cd warehouse/`
3. При необходимости изменить порты для nginx и postgresql в docker-compose.yaml
4. При необходимости в .env изменить NGINX_SERVER_NAME
5. `docker compose up -d`
6. `docker compose run app composer install`
7. `docker compose run app bin/console docktrine:migration:diff`
8. `docker compose run app bin/console docktrine:migration:migrate`

## API 

Описание API можно посмотреть по: 

    http://<domainName>/api/doc

