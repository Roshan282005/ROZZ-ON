# Stage 1: Build React app
FROM node:20-alpine AS build

WORKDIR /app

# Copy package.json and lock first for caching
COPY package.json package-lock.json ./
RUN npm install

# Copy all files and build
COPY . ./
RUN npm run build

# Stage 2: Serve with nginx
FROM nginx:stable-alpine

# Remove default nginx website
RUN rm -rf /usr/share/nginx/html/*

# Copy build output to nginx html folder
COPY --from=build /app/build /usr/share/nginx/html

# Copy custom nginx config
COPY nginx.conf /etc/nginx/conf.d/default.conf

# Expose port (Railway uses $PORT env variable)
EXPOSE 9000

# Start nginx
CMD ["nginx", "-g", "daemon off;"]
