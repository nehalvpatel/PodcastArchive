#!/bin/bash
docker stop painkilleralready_app
docker rm painkilleralready_app
docker build -t painkilleralready_app -f Dockerfile .
docker run -v $PWD:/var/www/html --name painkilleralready_app -d -p 8080:80 painkilleralready_app
#sudo docker exec -i -t painkilleralready_app /bin/bash