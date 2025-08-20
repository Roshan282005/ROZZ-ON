# Stage 1: Build React app
FROM node:20 AS build

WORKDIR /app

COPY package*.json ./
RUN npm install

COPY public ./public
COPY src ./src
COPY . .

RUN npm run build

# Stage 2: Serve with Nginx
FROM nginx:alpine

# Copy built files
COPY --from=build /app/build /usr/share/nginx/html

# Expose Railway's port
EXPOSE 9000

# Update Nginx to listen on 9000
RUN sed -i 's/listen       80;/listen       9000;/' /etc/nginx/conf.d/default.conf

CMD ["nginx", "-g", "daemon off;"]
