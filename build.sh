mkdir docker/server
chmod -R 777 docker
cd src/frontend
npm i
ng build
cd ../../
cp -rf src/frontend/dist/sype/* docker/server
cp -rf src/api/* docker/server
