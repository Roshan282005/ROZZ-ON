# Stage 1: Build the React app
FROM node:18 as build
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# Stage 2: Serve with nginx
FROM nginx:alpine
# Copy built files to nginx html directory
COPY --from=build /app/build /usr/share/nginx/html

# Copy custom nginx config (optional, see next step)
COPY nginx.conf /etc/nginx/conf.d/default.conf

EXPOSE 8080
CMD ["nginx", "-g", "daemon off;"]
