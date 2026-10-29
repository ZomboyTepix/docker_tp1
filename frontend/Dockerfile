FROM node:20-alpine AS tooling

WORKDIR /app
COPY package.json ./

FROM tooling AS development

COPY package-lock.json ./
RUN npm ci --no-audit --no-fund

COPY . .

# Valeurs par défaut, également utilisables avec docker run.
ENV HOST=0.0.0.0
ENV PORT=3000
ENV WATCHPACK_POLLING=true
ENV WDS_SOCKET_PORT=0

EXPOSE 3000
STOPSIGNAL SIGTERM

# Lancement direct de Node pour transmettre les signaux au serveur React.
CMD ["node", "node_modules/react-scripts/scripts/start.js"]
