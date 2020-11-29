## Nginx

``` bash
docker run -d --name nginx \
    --hostname nginx \
    --restart always \
    -v /your_path/nginx.conf:/etc/nginx/nginx.conf \
    -v /your_path/conf.d/:/etc/nginx/conf.d/ \
    -v /your_path/cert/:/etc/nginx/cert/ \
    -v /your_path/logs/:/etc/nginx/logs/ \
    -p YOUR_PUBLIC_IP:80:80 \
    -p YOUR_PUBLIC_IP:443:443/tcp \
    -p YOUR_PUBLIC_IP:443:443/udp \
    gjovanov/nginx

# attach nginx container to frontend network
docker network connect frontend nginx
```

You can a basic `conf` file for `roomler` container e.g. in `/your_path/conf.d/roomler.live.conf`:
``` conf
# HTTP server
server {
       listen         80;
       listen         [::]:80;
       server_name    roomler.live; # replace it with your domain
       return         301 https://$server_name$request_uri;
}

# HTTPS server
server {
    # Enable QUIC and HTTP/3.
    listen 443 quic reuseport;
    # Ensure that HTTP/2 is enabled for the server
    listen 443 ssl http2;
    server_name  roomler.live; # replace it with your domain

    http2_push_preload on;

    client_max_body_size 0;

    gzip on;
    gzip_http_version 1.1;
    gzip_vary on;
    gzip_comp_level 6;
    gzip_proxied any;
    gzip_types text/plain text/css application/json application/javascript application/x-javascript text/javascript;

    brotli_static on;
    brotli on;
    brotli_types text/plain text/css application/json application/javascript application/x-javascript text/javascript;
    brotli_comp_level 4;

    # Enable TLS versions (TLSv1.3 is required for QUIC).
    ssl_protocols TLSv1.2 TLSv1.3;

    ssl_certificate /etc/nginx/cert/roomler.live.pem;  # replace with your CERT
    ssl_certificate_key /etc/nginx/cert/roomler.live.key;   # replace with your CERT KEY

    ssl_session_cache    shared:SSL:1m;
    ssl_session_timeout  5m;

    # Enable TLSv1.3's 0-RTT. Use $ssl_early_data when reverse proxying to
    # prevent replay attacks.
    #
    # @see: http://nginx.org/en/docs/http/ngx_http_ssl_module.html#ssl_early_data
    ssl_early_data on;
    ssl_ciphers  HIGH:!aNULL:!MD5;
    ssl_prefer_server_ciphers  on;

    # Add Alt-Svc header to negotiate HTTP/3.
    add_header alt-svc 'h3-27=":443"; ma=86400';
    # Debug 0-RTT.
    add_header X-Early-Data $tls1_3_early_data;

    add_header x-frame-options "deny";
    add_header Strict-Transport-Security "max-age=31536000" always;

    location / {
        proxy_set_header   X-Real-IP $remote_addr;
        proxy_set_header   Host      $http_host;
        proxy_pass         http://roomler:3000;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;

        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "upgrade";

        proxy_read_timeout 1800;
        proxy_connect_timeout 1800;
        proxy_send_timeout 1800;
        send_timeout 1800;
    }
}

```

As well a basic `conf` file for `janus` container e.g. in `/your_path/conf.d/janus.roomler.live.conf`:

``` conf
server {
       listen         80;
       listen         [::]:80;
       server_name    janus.roomler.live; # replace it with your janus domain
       return         301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name janus.roomler.live;  # replace it with your janus domain
    client_max_body_size 0;

    ssl_certificate /etc/nginx/cert/your_cert.pem;
    ssl_certificate_key /etc/nginx/cert/your_cert.key;

    location / {
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header Host $host;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "upgrade";
        proxy_set_header Host $host;
        proxy_redirect off;

        proxy_pass http://janus:8080;
    }
    location /janus_ws {
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header Host $host;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "upgrade";
        proxy_set_header Host $host;
        proxy_redirect off;

        proxy_pass http://janus:8188;
    }

    location /janus_http {
        proxy_pass http://janus:8088/janus;
    }

    location /janus_admin {
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header Host $host;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "upgrade";
        proxy_set_header Host $host;
        proxy_pass http://janus:7188;
    }

    location /janus_admin_http {
        proxy_pass http://janus:7088/admin;
    }
}

```