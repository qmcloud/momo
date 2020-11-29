## Janus Gateway
Video conferences are implemented using Janus's VideoRoom plugin. Hence we will use this [Janus Docker Image](https://github.com/gjovanov/docker/tree/master/janus-slim) of Janus.

It's recommendad that Janus is attached directly to the Dockers `host` network, to avoid issues with ICE Candidates gathering and the Docker Port mapping.
``` bash
docker run -d \
  --name="janus" \
  --restart="always" \
  --network="host" \
  gjovanov/janus-slim

```