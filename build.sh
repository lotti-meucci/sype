mkdir server
cd src/frontend
ng build
cd ../../
cp -rf src/frontend/dist/sype/* server
cp -rf src/api/* server
