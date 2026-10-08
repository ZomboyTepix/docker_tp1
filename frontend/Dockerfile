FROM node:20-alpine

WORKDIR /app

COPY package.json ./
RUN npm install --legacy-peer-deps --force

# On copie le reste des fichiers
COPY . .

# Pour React, forcer le mode dev et chokidar (hot reload)
ENV WATCHPACK_POLLING=true
ENV WDS_SOCKET_PORT=0

EXPOSE 3000

# Lancement du serveur de développement React
CMD ["npm", "start"]
