FROM nginx:1.25-alpine AS webserver

RUN apk add --no-cache curl

RUN addgroup -g 1001 webuser && \
    adduser -u 1001 -G webuser -D webuser

RUN rm -f /etc/nginx/nginx.conf /etc/nginx/conf.d/default.conf

COPY nginx.conf /etc/nginx/nginx.conf
COPY default.conf /etc/nginx/conf.d/default.conf

RUN mkdir -p /var/log/nginx && \
    chown -R webuser:webuser /var/log/nginx

RUN mkdir -p /usr/share/nginx/html && \
    chown -R webuser:webuser /usr/share/nginx/html

HEALTHCHECK --interval=30s --timeout=10s --retries=3 --start-period=5s \
    CMD curl -f http://localhost/ || exit 1

EXPOSE 80

STOPSIGNAL SIGTERM

CMD ["nginx", "-g", "daemon off;"]
