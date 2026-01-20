# Quick GCP Setup and Deploy Guide

## 1️⃣ Prerequisites

Ensure you have:
- Google Cloud Project: `ideab2b` (Project ID: 173142818446)
- `gcloud` CLI installed: https://cloud.google.com/sdk/docs/install
- Docker installed: https://docs.docker.com/install
- Git with GitHub access: https://github.com/ideaholiday/ideab2b

## 2️⃣ One-Command Deployment

### Option A: Automated Script (Recommended)

```bash
# Make script executable
chmod +x deploy.sh

# Run full deployment
./deploy.sh all

# Or specific commands
./deploy.sh setup   # Only setup GCP
./deploy.sh build   # Only build images
./deploy.sh deploy  # Only deploy services
```

### Option B: Manual Steps

```bash
# 1. Setup authentication
gcloud auth login
gcloud config set project 173142818446

# 2. Set variables
export PROJECT_ID="173142818446"
export REGION="asia-south2"
export SERVICE_ACCOUNT="ideab2b-cloudrun"

# 3. Enable APIs
gcloud services enable \
  artifactregistry.googleapis.com \
  cloudbuild.googleapis.com \
  run.googleapis.com \
  --project=$PROJECT_ID

# 4. Build images
docker build -t asia-south2-docker.pkg.dev/$PROJECT_ID/ideab2b/api:latest -f backend/Dockerfile ./backend
docker build -t asia-south2-docker.pkg.dev/$PROJECT_ID/ideab2b/web:latest -f frontend/Dockerfile ./frontend

# 5. Configure Docker auth
gcloud auth configure-docker asia-south2-docker.pkg.dev

# 6. Push images
docker push asia-south2-docker.pkg.dev/$PROJECT_ID/ideab2b/api:latest
docker push asia-south2-docker.pkg.dev/$PROJECT_ID/ideab2b/web:latest

# 7. Deploy to Cloud Run
gcloud run deploy ideab2b-api \
  --image=asia-south2-docker.pkg.dev/$PROJECT_ID/ideab2b/api:latest \
  --platform=managed \
  --region=$REGION \
  --allow-unauthenticated

gcloud run deploy ideab2b-web \
  --image=asia-south2-docker.pkg.dev/$PROJECT_ID/ideab2b/web:latest \
  --platform=managed \
  --region=$REGION \
  --allow-unauthenticated
```

## 3️⃣ Local Testing with Docker Compose

```bash
# Copy environment file
cp .env.cloud .env

# Start services
docker-compose up -d

# Run migrations
docker exec ideab2b-api php artisan migrate

# Seed test data
docker exec ideab2b-api php artisan db:seed

# Access application
# Frontend: http://localhost:5173
# API: http://localhost:8000/api
```

## 4️⃣ Test Users (After Seeding)

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@example.com | password |
| Agent | agent1@example.com | password |
| Hotel Partner | hotel1@example.com | password |
| Operator | operator1@example.com | password |

## 5️⃣ View Deployment Status

```bash
# Get service URLs
gcloud run services list --region=asia-south2

# View logs
gcloud run services logs read ideab2b-api --region=asia-south2

# Check service details
gcloud run services describe ideab2b-api --region=asia-south2
```

## 6️⃣ GitHub Actions CI/CD

Push to `main` branch to trigger automatic:
- ✅ Docker build
- ✅ Push to Artifact Registry
- ✅ Deploy to Cloud Run

Required GitHub Secrets:
```
WIF_PROVIDER: (from GCP Workload Identity)
WIF_SERVICE_ACCOUNT: (from GCP)
SANCTUM_DOMAINS: ideab2b-173142818446.asia-south2.run.app
SESSION_DOMAIN: .ideab2b-173142818446.asia-south2.run.app
```

## 7️⃣ Environment Files

- **Local**: `.env` (copy from `.env.example`)
- **Docker**: `.env.cloud`
- **GCP**: Create secrets via Secret Manager
- **Frontend**: `.env.local`

## 8️⃣ Troubleshooting

### Images not pushing?
```bash
gcloud auth configure-docker asia-south2-docker.pkg.dev
```

### Services not running?
```bash
# Check logs
gcloud run services logs read ideab2b-api --limit=50

# Check service
gcloud run services describe ideab2b-api
```

### Database connection issues?
- Ensure Cloud SQL instance is running
- Check Cloud SQL Auth proxy
- Verify environment variables

## 9️⃣ Cleanup

```bash
# Remove services
gcloud run services delete ideab2b-api --region=asia-south2
gcloud run services delete ideab2b-web --region=asia-south2

# Remove images
gcloud artifacts docker images delete asia-south2-docker.pkg.dev/173142818446/ideab2b/api

# Stop Docker Compose
docker-compose down
```

## 🔟 Additional Resources

- [DEPLOYMENT_GUIDE.md](./DEPLOYMENT_GUIDE.md) - Complete deployment guide
- [Cloud Run Documentation](https://cloud.google.com/run/docs)
- [GitHub Actions Docs](https://docs.github.com/en/actions)
- [Docker Documentation](https://docs.docker.com)

---

**Status**: Ready to Deploy ✅  
**URLs**:
- Web: https://ideab2b-173142818446.asia-south2.run.app
- API: https://ideab2b-api-[hash]-asia-south2.a.run.app
