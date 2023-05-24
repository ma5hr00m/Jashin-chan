# 使用官方Nginx镜像
FROM nginx

# 将配置文件复制到容器中
COPY nginx.conf /etc/nginx/nginx.conf

# 将源代码复制到容器中
COPY /path/to/your/php/code /usr/share/nginx/html

# 安装PHP和MySQL客户端
RUN apt-get update && apt-get install -y php-fpm php-mysql mysql-client && \
    rm /etc/nginx/conf.d/default.conf && \
    chown -R www-data:www-data /usr/share/nginx/html && \
    chmod -R 755 /usr/share/nginx/html && \
    echo "upstream php-upstream { server php:9000; }" > /etc/nginx/conf.d/upstream.conf

# 暴露80端口
EXPOSE 80

# 启动Nginx和PHP-FPM
CMD ["nginx", "-g", "daemon off;"]