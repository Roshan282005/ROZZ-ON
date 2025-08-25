# Use Node.js for React build
FROM node:18

WORKDIR /app

# Copy package.json and install
COPY package*.json ./
RUN npm install

# Copy all files
COPY . .

# Build React
RUN npm run build

# Serve with a simple static server
RUN npm install -g serve
EXPOSE 8080

CMD ["serve", "-s", "build", "-l", "8080"]
