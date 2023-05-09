mkdir server
cd src/frontend
ng build
cd ../../
cp -r -force src/frontend/dist/sype/* server
cp -r -force src/api/* server
