chmod -R 777 docker
mkdir docker/server
cd src/frontend
ng build
cd ../../
cp -rf src/frontend/dist/sype/* docker/server
cp -rf src/api/* docker/server
