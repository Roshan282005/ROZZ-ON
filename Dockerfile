# Step 1: Build the app
FROM node:18-alpine AS builder

WORKDIR /app
COPY package*.json ./
RUN npm install

COPY . .
RUN npm run build

# Step 2: Serve with Vite preview
FROM node:18-alpine

WORKDIR /app
COPY --from=builder /app ./

# Railway sets PORT automatically
ENV PORT=8080

# Install Vite globally (for preview command)
RUN npm install -g vite

EXPOSE 8080

CMD ["vite", "preview", "--host", "0.0.0.0", "--port", "8080"]
