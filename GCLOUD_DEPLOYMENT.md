# 🚀 Google Cloud Deployment Guide

## Overview
This guide explains how to deploy the IdeaB2B Travel Platform to Google Cloud with automatic CI/CD triggers.

---

## 📋 Prerequisites

### Required
- Google Cloud Account (with billing enabled)
- GitHub Account with repository access
- `gcloud` CLI installed locally
- Docker installed locally
- Git configured

### Recommended
- Service Account with appropriate permissions
- Workload Identity Federation setup

---

## 🔧 Step 1: Initial Google Cloud Setup

### 1.1 Set Your Project ID
```bash
export PROJECT_ID="your-gcp-project-id"
gcloud config set project $PROJECT_ID
```

### 1.2 Enable Required APIs
```bash
gcloud services enable \
    cloudbuild.googleapis.com \
    run.googleapis.com \
    artifactregistry.googleapis.com \
    container.googleapis.com \
    sqladmin.googleapis.com \
    cloudresourcemanager.googleapis.com
```

### 1.3 Authenticate to Google Cloud
```bash
gcloud auth login
gcloud auth application-default login
```

---

## 🐳 Step 2: Set up Artifact Registry

### 2.1 Create Artifact Registry Repository
```bash
gcloud artifacts repositories create ideab2b \
    --repository-format=docker \
    --location=asia-south2 \
    --description="IdeaB2B Travel Platform Docker Images"
```

### 2.2 Configure Docker Authentication
```bash
gcloud auth configure-docker asia-south2-docker.pkg.dev
```

---

## 🔐 Step 3: Set up Workload Identity Federation (Recommended)

### 3.1 Create Service Account
```bash
gcloud iam service-accounts create github-actions \
    --display-name="GitHub Actions Service Account"
```

### 3.2 Grant Permissions
```bash
gcloud projects add-iam-policy-binding $PROJECT_ID \
    --member="serviceAccount:github-actions@${PROJECT_ID}.iam.gserviceaccount.com" \
    --role="roles/run.admin"

gcloud projects add-iam-policy-binding $PROJECT_ID \
    --member="serviceAccount:github-actions@${PROJECT_ID}.iam.gserviceaccount.com" \
    --role="roles/artifactregistry.admin"

gcloud projects add-iam-policy-binding $PROJECT_ID \
    --member="serviceAccount:github-actions@${PROJECT_ID}.iam.gserviceaccount.com" \
    --role="roles/iam.serviceAccountUser"
```

### 3.3 Create Workload Identity Provider
```bash
gcloud iam workload-identity-pools create "github" \
    --project="${PROJECT_ID}" \
    --location="global" \
    --display-name="GitHub"

gcloud iam workload-identity-pools providers create-oidc "github" \
    --project="${PROJECT_ID}" \
    --location="global" \
    --display-name="GitHub" \
    --attribute-mapping="google.subject=assertion.sub,attribute.actor=assertion.actor,attribute.aud=assertion.aud,attribute.repository=assertion.repository" \
    --issuer-uri="https://token.actions.githubusercontent.com"

gcloud iam service-accounts add-iam-policy-binding \
    "github-actions@${PROJECT_ID}.iam.gserviceaccount.com" \
    --project="${PROJECT_ID}" \
    --role="roles/iam.workloadIdentityUser" \
    --member="principalSet://iam.googleapis.com/projects/${PROJECT_ID}/locations/global/workloadIdentityPools/github/attribute.repository/ideaholiday/ideab2b"
```

---

## 🔑 Step 4: Configure GitHub Secrets

### 4.1 Get Workload Identity Provider and Service Account
```bash
# Get WIF Provider
gcloud iam workload-identity-pools providers describe "github" \
    --project="${PROJECT_ID}" \
    --location="global" \
    --format="value(name)"

# Get Service Account Email
echo "github-actions@${PROJECT_ID}.iam.gserviceaccount.com"
```

### 4.2 Add Secrets to GitHub Repository
Go to: `Settings > Secrets and variables > Actions`

Add these secrets:
- **GCP_PROJECT_ID**: Your GCP project ID
- **WIF_PROVIDER**: The Workload Identity Provider resource name (from step 4.1)
- **WIF_SERVICE_ACCOUNT**: The service account email (from step 4.1)

---

## 📝 Step 5: Deploy Manually (First Time)

### 5.1 Using the Configuration Script
```bash
cd /Users/jitendramaury/ideab2b/travel-platform

# Make script executable
chmod +x .gcloud-config.sh

# Run setup
./.gcloud-config.sh setup-all

# Deploy
./.gcloud-config.sh deploy-all
```

### 5.2 Or Deploy Manually
```bash
# Build Backend
docker build \
    -t asia-south2-docker.pkg.dev/${PROJECT_ID}/ideab2b/api:latest \
    -f backend/Dockerfile \
    .

# Build Frontend
docker build \
    -t asia-south2-docker.pkg.dev/${PROJECT_ID}/ideab2b/web:latest \
    -f frontend/Dockerfile \
    .

# Push images
docker push asia-south2-docker.pkg.dev/${PROJECT_ID}/ideab2b/api:latest
docker push asia-south2-docker.pkg.dev/${PROJECT_ID}/ideab2b/web:latest

# Deploy Backend
gcloud run deploy ideab2b-api \
    --image=asia-south2-docker.pkg.dev/${PROJECT_ID}/ideab2b/api:latest \
    --region=asia-south2 \
    --platform=managed \
    --allow-unauthenticated \
    --set-env-vars=APP_ENV=production,APP_DEBUG=false \
    --memory=1Gi \
    --cpu=1

# Deploy Frontend
gcloud run deploy ideab2b-web \
    --image=asia-south2-docker.pkg.dev/${PROJECT_ID}/ideab2b/web:latest \
    --region=asia-south2 \
    --platform=managed \
    --allow-unauthenticated \
    --memory=512Mi \
    --cpu=1
```

---

## ⚙️ Step 6: Set up Cloud Build Trigger

### 6.1 Connect GitHub Repository
```bash
gcloud builds connect github \
    --authorizer-token=GITHUB_TOKEN \
    --region=asia-south2
```

### 6.2 Create Trigger
```bash
gcloud builds triggers create github \
    --repo-name="ideab2b" \
    --repo-owner="ideaholiday" \
    --branch-pattern="^(main|develop)$" \
    --build-config="cloudbuild.yaml" \
    --name="deploy-ideab2b"
```

---

## 🔄 Step 7: Automatic Deployment Trigger

### Every Push to main or develop will:
1. ✅ Build Docker images
2. ✅ Push to Artifact Registry
3. ✅ Deploy to Cloud Run
4. ✅ Update service URLs

### Monitor Deployments
```bash
# View build history
gcloud builds list --limit=10

# View specific build logs
gcloud builds log BUILD_ID

# View Cloud Run deployments
gcloud run services list --region=asia-south2
```

---

## 📊 Verify Deployment

### 6.1 Get Service URLs
```bash
# Backend API URL
gcloud run services describe ideab2b-api \
    --region=asia-south2 \
    --format='value(status.url)'

# Frontend Web URL
gcloud run services describe ideab2b-web \
    --region=asia-south2 \
    --format='value(status.url)'
```

### 6.2 Test Services
```bash
# Test backend API
curl "$(gcloud run services describe ideab2b-api --region=asia-south2 --format='value(status.url)')/api/health"

# Test frontend
curl "$(gcloud run services describe ideab2b-web --region=asia-south2 --format='value(status.url)')"
```

---

## 🔐 Environment Variables (Production)

Update in Cloud Run:
```bash
gcloud run services update ideab2b-api \
    --region=asia-south2 \
    --update-env-vars=\
APP_ENV=production,\
APP_DEBUG=false,\
APP_KEY=base64:your_app_key,\
DB_CONNECTION=mysql,\
DB_HOST=your_db_host,\
DB_PORT=3306,\
DB_DATABASE=ideab2b,\
DB_USERNAME=root,\
DB_PASSWORD=your_password
```

---

## 📈 Monitoring & Logs

### View Logs
```bash
# Backend logs
gcloud run services describe ideab2b-api --region=asia-south2

# View detailed logs
gcloud logging read "resource.type=cloud_run_revision AND resource.labels.service_name=ideab2b-api" --limit=50 --format=json
```

### Set up Alerts
1. Go to Google Cloud Console
2. Cloud Run > Services > ideab2b-api
3. Click "Metrics"
4. Set up alerts for:
   - Error rate > 5%
   - Request latency > 2s
   - CPU usage > 80%

---

## 🚀 Advanced: Custom Domain

```bash
# Map custom domain
gcloud run domain-mappings create \
    --service=ideab2b-api \
    --domain=api.yourdomain.com \
    --region=asia-south2

gcloud run domain-mappings create \
    --service=ideab2b-web \
    --domain=yourdomain.com \
    --region=asia-south2
```

---

## 🐛 Troubleshooting

### Issue: Permission Denied
```bash
# Check service account permissions
gcloud projects get-iam-policy $PROJECT_ID \
    --flatten="bindings[].members" \
    --filter="bindings.members:github-actions*"
```

### Issue: Build Fails
```bash
# Check build logs
gcloud builds log LATEST --stream

# Rebuild manually
gcloud builds submit . --config=cloudbuild.yaml
```

### Issue: Service Won't Start
```bash
# Check Cloud Run service logs
gcloud run services describe ideab2b-api --region=asia-south2
gcloud logging read "resource.type=cloud_run_revision"
```

---

## 📋 Deployment Checklist

- [ ] Google Cloud Project created
- [ ] APIs enabled (Cloud Run, Artifact Registry, Cloud Build)
- [ ] Service Account created
- [ ] Workload Identity configured
- [ ] GitHub Secrets added
- [ ] Docker images built and tested locally
- [ ] First deployment successful
- [ ] Cloud Build trigger configured
- [ ] Service URLs working
- [ ] Environment variables set in Cloud Run
- [ ] Monitoring and alerts configured
- [ ] Custom domain mapped (optional)

---

## 📞 Support

For issues or questions:
1. Check Google Cloud documentation
2. Review Cloud Build logs
3. Check service health metrics
4. Contact Google Cloud Support (if premium)

---

**Platform is now production-ready on Google Cloud! 🎉**
