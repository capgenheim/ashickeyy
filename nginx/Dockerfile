# ============================================
# Stage 1: Build Flutter Web
# ============================================
FROM ghcr.io/cirruslabs/flutter:3.27.1 AS flutter-build

WORKDIR /app

# Copy pubspec first for dependency caching
COPY frontend/pubspec.yaml frontend/pubspec.lock* ./
RUN flutter pub get

# Copy source and build
COPY frontend/ .
RUN flutter clean && flutter pub get
RUN flutter build web --release --web-renderer canvaskit

# ============================================
# Stage 2: Nginx with Flutter build
# ============================================
FROM nginx:1.27-alpine

# Remove default config
RUN rm /etc/nginx/conf.d/default.conf

# Copy nginx config
COPY nginx/nginx.conf /etc/nginx/nginx.conf

# Copy Flutter build output
COPY --from=flutter-build /app/build/web /usr/share/nginx/html

# Create uploads directory
RUN mkdir -p /usr/share/nginx/uploads

# Create non-root user
RUN addgroup -S appgroup && adduser -S appuser -G appgroup
RUN touch /tmp/nginx.pid && \
    chown -R appuser:appgroup /var/cache/nginx /var/log/nginx /tmp/nginx.pid /usr/share/nginx

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
