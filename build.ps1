mkdir docker/server
cd src/frontend
ng build
cd ../../
cp -r -force src/frontend/dist/sype/* docker/server
cp -r -force src/api/* docker/server
