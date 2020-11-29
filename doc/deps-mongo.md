## MongoDB
As a storage of `users`, `rooms`, `messages`, `calls` etc, we will be using MongoDB.

We will rely on the official MongoDB Docker image:

``` bash
docker run -d --name mongo \
    --restart=always \
    -p 27017:27017 \
    -v mongo_data:/data/db \
    mongo

# attach mongo container to backend network
docker network connect backend mongo
```
Make sure you create a db user for your mongodb e.g.

```javascript
db.createUser(
  {
    user: "roomler",
    pwd: "super_secret",
    roles: [ { role: "readWrite", db: "roomlerdb" } ]
  }
)
```