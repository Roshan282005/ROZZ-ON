# Stage 1: Build React app
FROM node:18 AS build

WORKDIR /app

# Copy package files & install dependencies
COPY package*.json ./
RUN npm install

# Copy the rest of the source code
COPY . ./

# Build production React app
RUN npm run build

# Stage 2: Serve with Nginx
FROM nginx:alpine

# Copy React build files to nginx html folder
COPY --from=build /app/build /usr/share/nginx/html

# Replace default nginx.conf
COPY nginx.conf /etc/nginx/conf.d/default.conf

EXPOSE 9000

CMD ["nginx", "-g", "daemon off;"]
