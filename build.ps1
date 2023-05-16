mkdir docker/server
cd src/frontend
npm i
ng build
cd ../../
cp -r -force src/frontend/dist/sype/* docker/server
cp -r -force src/api/* docker/server
