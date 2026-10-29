FROM nginx:1.25-alpine AS webserver

RUN apk add --no-cache curl

RUN addgroup -g 1001 webuser \
    && adduser -u 1001 -G webuser -D webuser

RUN rm -f /etc/nginx/nginx.conf /etc/nginx/conf.d/default.conf

COPY nginx.conf /etc/nginx/templates/nginx.conf.template
COPY default.conf /etc/nginx/conf.d/default.conf

# L'entrypoint officiel génère la configuration avec les variables reçues au run.
ENV NGINX_ENVSUBST_OUTPUT_DIR=/etc/nginx
ENV NGINX_ENVSUBST_FILTER="^NGINX_WORKER_"
ENV NGINX_WORKER_PROCESSES=1
ENV NGINX_WORKER_CONNECTIONS=1024

HEALTHCHECK --interval=10s --timeout=5s --retries=3 --start-period=10s \
    CMD curl -fsS http://127.0.0.1/api/hello > /dev/null \
        && curl -fsS http://127.0.0.1/ > /dev/null || exit 1

EXPOSE 80
STOPSIGNAL SIGTERM

CMD ["nginx", "-g", "daemon off;"]
