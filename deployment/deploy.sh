#!/bin/bash

# get latest main tag
git checkout main
git fetch
git pull

# calculate variables
if [ ! -z $1 ]; then
    VERSION=$1
else
    VERSION=$(git describe --tag)
fi;
TAG=hippy.azurecr.io/hippy-auth-api-v1:$VERSION

# # login to azure container registry
az acr login --name hippy

# # create the image
DOCKER_BUILDKIT=1 docker image build -t $TAG --build-arg version=$VERSION --no-cache .

# # push the image to the registry
docker push $TAG

# cleanup
docker rmi "$(docker images -f "reference=$TAG" -q)"
