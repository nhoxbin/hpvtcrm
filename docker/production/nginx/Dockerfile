FROM nginx:alpine

COPY ./docker/production/nginx/nginx.conf /etc/nginx/nginx.conf
COPY public/ /var/www/public/

WORKDIR /var/www/public
EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]
