FROM node:20-alpine

WORKDIR /app

COPY package.json ./
RUN npm install --legacy-peer-deps --force

COPY . .

ENV CI=false
RUN npm run build

# La copie se fait au DÉMARRAGE (CMD) pour écraser le volume partagé à chaque fois
CMD ["sh", "-c", "cp -r /app/build/* /usr/share/nginx/html/"]
