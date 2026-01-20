# 🚀 Google Cloud Deployment Guide

Complete guide to deploy IdeaB2B Travel Platform to Google Cloud Run.

## Prerequisites

- Google Cloud Project: `ideab2b` (Project ID: 173142818446)
- Region: `asia-south2`
- GitHub Repository: `https://github.com/ideaholiday/ideab2b`
- Docker installed locally
- `gcloud` CLI installed and configured
- Organization: ideaholiday.com

## 1. Initial Setup

### 1.1 Create GCP Service Account

```bash
# Set variables
PROJECT_ID="173142818446"
REGION="asia-south2"
SERVICE_ACCOUNT="ideab2b-cloudrun"

# Create service account
gcloud iam service-accounts create $SERVICE_ACCOUNT \
  --display-name="IdeaB2B Cloud Run Service Account" \
  --project=$PROJECT_ID

# Grant necessary roles
gcloud projects add-iam-policy-binding $PROJECT_ID \
  --member="serviceAccount:$SERVICE_ACCOUNT@$PROJECT_ID.iam.gserviceaccount.com" \
  --role="roles/run.admin"

gcloud projects add-iam-policy-binding $PROJECT_ID \
  --member="serviceAccount:$SERVICE_ACCOUNT@$PROJECT_ID.iam.gserviceaccount.com" \
  --role="roles/cloudsql.client"

gcloud projects add-iam-policy-binding $PROJECT_ID \
  --member="serviceAccount:$SERVICE_ACCOUNT@$PROJECT_ID.iam.gserviceaccount.com" \
  --role="roles/secretmanager.secretAccessor"
```

### 1.2 Create Artifact Registry Repository

```bash
# Enable service
gcloud services enable artifactregistry.googleapis.com --project=$PROJECT_ID

# Create repository
gcloud artifacts repositories create ideab2b \
  --repository-format=docker \
  --location=$REGION \
  --project=$PROJECT_ID

# Configure Docker authentication
gcloud auth configure-docker $REGION-docker.pkg.dev
```

### 1.3 Create Cloud SQL Instance

```bash
# Enable service
gcloud services enable sqladmin.googleapis.com --project=$PROJECT_ID

# Create instance
gcloud sql instances create ideab2b-mysql \
  --database-version=MYSQL_8_0 \
  --tier=db-f1-micro \
  --region=$REGION \
  --network=default \
  --backup-start-time=03:00 \
  --enable-bin-log \
  --project=$PROJECT_ID

# Create database
gcloud sql databases create ideab2b \
  --instance=ideab2b-mysql \
  --project=$PROJECT_ID

# Create user
gcloud sql users create ideab2b \
  --instance=ideab2b-mysql \
  --password=GENERATE_RANDOM_PASSWORD \
  --project=$PROJECT_ID
```

### 1.4 Create Cloud Memorystore (Redis)

```bash
# Enable service
gcloud services enable redis.googleapis.com --project=$PROJECT_ID

# Create Redis instance
gcloud redis instances create ideab2b-redis \
  --size=1 \
  --region=$REGION \
  --redis-version=7.0 \
  --project=$PROJECT_ID
```

### 1.5 Create Secrets in Secret Manager

```bash
# Enable service
gcloud services enable secretmanager.googleapis.com --project=$PROJECT_ID

# Create secrets
echo "your-app-key-here" | gcloud secrets create ideab2b-app-key \
  --data-file=- \
  --project=$PROJECT_ID

echo "your-jwt-secret-here" | gcloud secrets create ideab2b-jwt-secret \
  --data-file=- \
  --project=$PROJECT_ID

echo "db-password" | gcloud secrets create ideab2b-db-password \
  --data-file=- \
  --project=$PROJECT_ID
```

## 2. GitHub Actions Setup

### 2.1 Set up Workload Identity Federation

```bash
# Enable services
gcloud services enable cloudresourcemanager.googleapis.com \
  iamcredentials.googleapis.com \
  sts.googleapis.com \
  --project=$PROJECT_ID

# Create Workload Identity Pool
gcloud iam workload-identity-pools create "github" \
  --project=$PROJECT_ID \
  --location="global" \
  --display-name="GitHub Actions"

# Get pool resource name
WORKLOAD_IDENTITY_POOL_ID=$(gcloud iam workload-identity-pools describe "github" \
  --project=$PROJECT_ID \
  --location="global" \
  --format='value(name)')

# Create Workload Identity Provider
gcloud iam workload-identity-pools providers create-oidc "github-provider" \
  --project=$PROJECT_ID \
  --location="global" \
  --workload-identity-pool="github" \
  --display-name="GitHub provider" \
  --attribute-mapping="google.subject=assertion.sub,attribute.actor=assertion.actor,attribute.repository=assertion.repository,attribute.repository_owner=assertion.repository_owner" \
  --issuer-uri="https://token.actions.githubusercontent.com" \
  --attribute-condition="assertion.repository_owner == 'ideaholiday'"

# Get provider resource name
PROVIDER=$(gcloud iam workload-identity-pools providers describe "github-provider" \
  --project=$PROJECT_ID \
  --location="global" \
  --workload-identity-pool="github" \
  --format='value(name)')

# Grant permissions
gcloud iam service-accounts add-iam-policy-binding \
  "$SERVICE_ACCOUNT@$PROJECT_ID.iam.gserviceaccount.com" \
  --project=$PROJECT_ID \
  --role="roles/iam.workloadIdentityUser" \
  --subject="principalSet://iam.googleapis.com/$WORKLOAD_IDENTITY_POOL_ID/attribute.repository/ideaholiday/ideab2b"
```

### 2.2 Add GitHub Secrets

Add these secrets to your GitHub repository (https://github.com/ideaholiday/ideab2b/settings/secrets):

```
WIF_PROVIDER: projects/173142818446/locations/global/workloadIdentityPools/github/providers/github-provider
WIF_SERVICE_ACCOUNT: ideab2b-cloudrun@173142818446.iam.gserviceaccount.com
GCP_PROJECT_ID: 173142818446
SANCTUM_DOMAINS: ideab2b-173142818446.asia-south2.run.app
SESSION_DOMAIN: .ideab2b-173142818446.asia-south2.run.app
```

## 3. Docker Build & Local Testing

### 3.1 Build Docker Images Locally

```bash
# Build backend
docker build -t ideab2b-api:latest -f backend/Dockerfile ./backend

# Build frontend
docker build -t ideab2b-web:latest -f frontend/Dockerfile ./frontend
```

### 3.2 Test with Docker Compose

```bash
# Create .env file
cp .env.cloud .env

# Start services
docker-compose up -d

# Check logs
docker-compose logs -f

# Run migrations
docker exec ideab2b-api php artisan migrate

# Seed test data
docker exec ideab2b-api php artisan db:seed
```

### 3.3 Access Application

- Frontend: http://localhost:5173
- API: http://localhost:8000/api
- API Docs: http://localhost:8000/api/docs (if available)

## 4. Deploy to Google Cloud Run

### 4.1 Manual Deployment (Cloud Build)

```bash
# Submit build to Cloud Build
gcloud builds submit \
  --config=cloudbuild.yaml \
  --substitutions=_SERVICE_NAME=ideab2b,_REGION=asia-south2 \
  --project=$PROJECT_ID
```

### 4.2 Automated Deployment (GitHub Actions)

Push to main branch:

```bash
git push origin main
```

GitHub Actions will:
1. Build Docker images
2. Push to Artifact Registry
3. Deploy to Cloud Run
4. Run tests on pull requests

## 5. Post-Deployment

### 5.1 Run Database Migrations

```bash
# Deploy migration job
gcloud run jobs create ideab2b-migrate \
  --image=$REGION-docker.pkg.dev/$PROJECT_ID/ideab2b/api:latest \
  --task-timeout=600s \
  --region=$REGION \
  --command="php,artisan,migrate,--force" \
  --set-env-vars="DB_HOST=10.0.0.3,DB_DATABASE=ideab2b" \
  --set-secrets="DB_PASSWORD=ideab2b-db-password:latest" \
  --project=$PROJECT_ID

# Run the migration job
gcloud run jobs execute ideab2b-migrate \
  --region=$REGION \
  --project=$PROJECT_ID
```

### 5.2 Seed Test Data

```bash
gcloud run jobs create ideab2b-seed \
  --image=$REGION-docker.pkg.dev/$PROJECT_ID/ideab2b/api:latest \
  --task-timeout=600s \
  --region=$REGION \
  --command="php,artisan,db:seed" \
  --set-env-vars="DB_HOST=10.0.0.3,DB_DATABASE=ideab2b" \
  --set-secrets="DB_PASSWORD=ideab2b-db-password:latest" \
  --project=$PROJECT_ID
```

### 5.3 Monitor Deployment

```bash
# View Cloud Run services
gcloud run services list --region=$REGION --project=$PROJECT_ID

# View service details
gcloud run services describe ideab2b-api --region=$REGION --project=$PROJECT_ID

# View logs
gcloud run services logs read ideab2b-api --region=$REGION --project=$PROJECT_ID

# View metrics
gcloud monitoring dashboards list --project=$PROJECT_ID
```

## 6. Custom Domain Setup

```bash
# Map custom domain
gcloud run domain-mappings create \
  --service=ideab2b-api \
  --domain=api.ideaholiday.com \
  --region=$REGION \
  --project=$PROJECT_ID

gcloud run domain-mappings create \
  --service=ideab2b-web \
  --domain=ideaholiday.com \
  --region=$REGION \
  --project=$PROJECT_ID
```

Update your DNS records with the provided CNAME values.

## 7. Environment Configuration

Update `.env.cloud` with actual values:

```bash
# Database credentials from Cloud SQL
DB_HOST=<CLOUD_SQL_PRIVATE_IP>
DB_USERNAME=ideab2b
# DB_PASSWORD from Secret Manager

# Redis credentials from Cloud Memorystore
REDIS_HOST=<MEMORYSTORE_PRIVATE_IP>

# JWT Secret from Secret Manager
# JWT_SECRET from Secret Manager

# App key from Secret Manager
# APP_KEY from Secret Manager
```

## 8. Troubleshooting

### View Pod Logs

```bash
gcloud run services logs read ideab2b-api \
  --region=$REGION \
  --limit=100 \
  --project=$PROJECT_ID
```

### Check Service Status

```bash
gcloud run services describe ideab2b-api \
  --region=$REGION \
  --project=$PROJECT_ID
```

### Re-deploy Service

```bash
gcloud run deploy ideab2b-api \
  --image=$REGION-docker.pkg.dev/$PROJECT_ID/ideab2b/api:latest \
  --region=$REGION \
  --project=$PROJECT_ID
```

## 9. Backup & Recovery

### Backup Cloud SQL

```bash
gcloud sql backups create \
  --instance=ideab2b-mysql \
  --project=$PROJECT_ID
```

### View Backups

```bash
gcloud sql backups list \
  --instance=ideab2b-mysql \
  --project=$PROJECT_ID
```

## 10. Security Best Practices

- ✅ Use Secret Manager for sensitive data
- ✅ Enable Cloud SQL Auth proxy
- ✅ Use private IPs for databases
- ✅ Enable VPC connectors
- ✅ Configure Cloud Armor
- ✅ Enable audit logging
- ✅ Use least privilege IAM roles
- ✅ Enable binary logging for backups

## Resources

- [Google Cloud Run Documentation](https://cloud.google.com/run/docs)
- [Cloud SQL Documentation](https://cloud.google.com/sql/docs)
- [Cloud Memorystore Documentation](https://cloud.google.com/memorystore/docs)
- [Workload Identity Federation](https://cloud.google.com/iam/docs/workload-identity-federation)

---

**Deployment Status**: Ready for Production  
**Last Updated**: January 20, 2026  
**Organization**: ideaholiday.com
