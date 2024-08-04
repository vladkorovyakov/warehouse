ARG APP_IMAGE

FROM ${APP_IMAGE} AS assets

FROM nginx:1.25.1-alpine

ARG USER_ID=1000
ARG GROUP_ID=1000

RUN apk add shadow && usermod -u ${USER_ID} -o nginx && groupmod -g ${GROUP_ID} -o nginx

# auto_envsubst
# https://github.com/nginxinc/docker-nginx/blob/793319d7251c03eccecbf27b60e0cfbbd2d1f400/mainline/alpine/20-envsubst-on-templates.sh
ENV NGINX_ENVSUBST_TEMPLATE_DIR="/etc/nginx/conf.d"
ENV NGINX_ENVSUBST_TEMPLATE_SUFFIX=".tmpl"

COPY ./docker/nginx /etc/nginx
COPY --from=assets /app/public /app/public