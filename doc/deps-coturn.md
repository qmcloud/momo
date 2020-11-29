## Coturn
For enabling peers, that are behind NAT (Private LANs), to create WebRTC PeerConnections with Janus, it's recommended to setup a TURN server. It's also recommended that your TURN server is running on the Docker `host` network. 

Hence we will your Instrumentos docker images:

``` bash
docker run -d \
  --name="coturn" \
  --restart="always" \
  --net=host \
       instrumentisto/coturn -n \
         --lt-cred-mech --fingerprint \
         --no-multicast-peers \
         --cli-password=MyTopSecret \
         --no-tlsv1 \
         --no-tlsv1_1 \
         --fingerprint \
         --lt-cred-mech \
         --verbose \
         --user=SuperUser:MyTopSecret \
         --server-name=your_domain \
         --realm=your_domain \
         --listening-ip='$(detect-external-ip)' \
         --min-port=10200 \
         --max-port=49200
```

To test your TURN server, check [this](https://stackoverflow.com/questions/28772212/stun-turn-server-connectivity-test) post.