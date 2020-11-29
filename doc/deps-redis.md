## Redis
Since Roomler app is started in a Cluster mode using `pm.js` (there will be as many Web API processes the number of CPU cores on the machine), in order for these processes to be able to communicate with each other, we will rely on Redis PUB/SUB mechanism.

``` bash
docker run -d --name redis `
    -e ALLOW_EMPTY_PASSWORD=yes `
	--restart=always `
	-p 6379:6379 `
    bitnami/redis:latest

# attach redis container to backend network
docker network connect backend redis