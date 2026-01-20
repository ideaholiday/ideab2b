# 🚀 Google Cloud Deployment Infrastructure - Complete Setup

## Summary

Complete Docker & Google Cloud deployment configuration for IdeaB2B Travel Platform deployed to Google Cloud Run in `asia-south2` region.

**Project Details:**
- Project ID: `173142818446`
- Project Name: `ideab2b`
- Region: `asia-south2`
- URL: https://ideab2b-173142818446.asia-south2.run.app
- Organization: ideaholiday.com
- GitHub: https://github.com/ideaholiday/ideab2b

---

## 📦 Files Created

### Docker Files
```
backend/
├── Dockerfile                 # Multi-stage Laravel build
├── nginx.conf                 # Nginx configuration
├── default.conf               # Nginx virtual host
├── supervisor.conf            # Process manager config
└── .dockerignore             # Docker build exclusions

frontend/
├── Dockerfile                 # Node.js React build
└── .dockerignore             # Docker build exclusions
```

### Docker Compose
```
docker-compose.yml            # Local development environment with MySQL, Redis, Nginx
```

### Google Cloud Configuration
```
cloudbuild.yaml               # Cloud Build CI/CD pipeline
.gcloudignore                 # GCP deployment exclusions
.env.cloud                    # Production environment template
DEPLOYMENT_GUIDE.md           # Detailed deployment instructions
QUICKSTART_DEPLOY.md          # Quick start guide
deploy.sh                     # Automated deployment script
```

### GitHub Actions
```
.github/workflows/
├── deploy-gcloud.yml         # Automated GCP deployment (main branch)
└── tests.yml                 # Build, test, and quality checks
```

### Kubernetes (GKE)
```
k8s/
└── deployment.yaml           # K8s manifests with HPA, networking policies
```

### Environment Files
```
backend/.env.gcloud.example   # Backend production variables
frontend/.env.gcloud.example  # Frontend production variables
```

---

## 🏗️ Architecture Overview

### Docker Images
```
Backend (PHP 8.1)
├── Laravel 9.x
├── Nginx + PHP-FPM
├── MySQL client
└── Redis client

Frontend (Node 18)
├── React 18.x
├── Vite 4.x
├── Serve (static server)
└── Tailwind CSS
```

### Services
```
Google Cloud Run Services:
├── ideab2b-api  (Backend API)
│   └── 2 CPU, 512Mi RAM
│   └── Max 100 instances
│
└── ideab2b-web  (Frontend)
    └── 1 CPU, 256Mi RAM
    └── Max 50 instances

Artifact Registry:
└── asia-south2-docker.pkg.dev/173142818446/ideab2b/

Supporting Services:
├── Cloud SQL (MySQL 8.0)
├── Cloud Memorystore (Redis 7.0)
├── Secret Manager
└── Cloud Build
```

---

## 🚀 Quick Start (3 Steps)

### Step 1: Setup GCP
```bash
chmod +x deploy.sh
./deploy.sh setup
```

### Step 2: Build & Push Docker Images
```bash
./deploy.sh build
```

### Step 3: Deploy to Cloud Run
```bash
./deploy.sh deploy
```

---

## 📋 Detailed Setup Instructions

### Full Automated Deployment
```bash
./deploy.sh all
```

This will:
1. ✅ Check requirements (gcloud, Docker, git)
2. ✅ Enable required GCP APIs
3. ✅ Create service account with proper IAM roles
4. ✅ Create Artifact Registry repository
5. ✅ Build Docker images
6. ✅ Push images to Artifact Registry
7. ✅ Deploy backend to Cloud Run
8. ✅ Deploy frontend to Cloud Run
9. ✅ Display service URLs and next steps

### Manual Setup
See [DEPLOYMENT_GUIDE.md](./DEPLOYMENT_GUIDE.md) for step-by-step instructions.

---

## 🐳 Local Testing with Docker Compose

### Start Services
```bash
cp .env.cloud .env
docker-compose up -d
```

### Services Started
- **MySQL**: localhost:3306 (ideab2b_dev)
- **API**: localhost:8000 (Laravel)
- **Web**: localhost:5173 (React)
- **Redis**: localhost:6379 (Cache)
- **Nginx**: localhost (Reverse proxy)

### Run Migrations & Seed
```bash
docker exec ideab2b-api php artisan migrate
docker exec ideab2b-api php artisan db:seed
```

### Test Users
```
admin@example.com          / password
agent1@example.com         / password
hotel1@example.com         / password
operator1@example.com      / password
```

---

## 🔄 CI/CD Pipeline

### GitHub Actions Workflows

#### 1. Tests Workflow (tests.yml)
Runs on every push to main/develop and PRs:
- ✅ Backend tests (PHP + MySQL)
- ✅ Frontend tests (JavaScript)
- ✅ Code quality (PHP CodeSniffer, PHPStan, ESLint)
- ✅ Docker build verification
- ✅ Security scanning (Trivy)

#### 2. Deploy Workflow (deploy-gcloud.yml)
Runs when main branch is pushed:
- ✅ Build Docker images
- ✅ Push to Artifact Registry
- ✅ Deploy to Cloud Run
- ✅ Automatic DNS updates

### GitHub Secrets Required
```
WIF_PROVIDER              # Workload Identity Federation provider
WIF_SERVICE_ACCOUNT       # GCP service account email
SANCTUM_DOMAINS           # Allowed domains for API
SESSION_DOMAIN            # Session cookie domain
```

---

## 📊 Performance & Scaling

### Auto-Scaling Configuration
```
Backend API:
- Min replicas: 3
- Max replicas: 100
- CPU target: 70%
- Memory target: 80%

Frontend Web:
- Min replicas: 2
- Max replicas: 50
- CPU target: 75%
```

### Resource Requests/Limits
```
Backend:
- Request: 500m CPU, 256Mi RAM
- Limit: 1000m CPU, 512Mi RAM

Frontend:
- Request: 250m CPU, 128Mi RAM
- Limit: 500m CPU, 256Mi RAM
```

---

## 🔐 Security Features

### Container Security
- ✅ Non-root user execution
- ✅ Read-only filesystems
- ✅ Security headers (HSTS, CSP, X-Frame-Options)
- ✅ Health checks with timeouts
- ✅ Resource limits

### Network Security
- ✅ Private databases (VPC)
- ✅ Network policies
- ✅ Private Redis
- ✅ HTTPS enforced
- ✅ CORS configured

### Secret Management
- ✅ Cloud Secret Manager
- ✅ No secrets in code
- ✅ Automatic rotation support
- ✅ Service account with least privileges

---

## 📈 Monitoring & Logging

### Cloud Logging
```bash
# View API logs
gcloud run services logs read ideab2b-api --region=asia-south2 --limit=100

# Stream logs in real-time
gcloud run services logs read ideab2b-api --region=asia-south2 --follow
```

### Cloud Monitoring
- ✅ Request metrics
- ✅ CPU & memory usage
- ✅ Error rates
- ✅ Latency tracking
- ✅ Custom dashboards

### Health Checks
- Liveness probe: `/health` endpoint
- Readiness probe: HTTP status 200
- Check interval: 10s for API, 10s for Web

---

## 🌐 Network Configuration

### Ingress Points
```
Frontend (Public):
https://ideab2b-173142818446.asia-south2.run.app

Backend (Public):
https://ideab2b-api-[hash]-asia-south2.a.run.app/api

Custom Domains:
api.ideaholiday.com → Backend
ideaholiday.com → Frontend
```

### Load Balancing
- ✅ Cloud Load Balancer (automatically configured)
- ✅ Health-based routing
- ✅ Session affinity
- ✅ Request forwarding

---

## 📝 Environment Configuration

### Backend (.env.gcloud.example)
```
APP_ENV=production
DB_HOST=Cloud SQL private IP
DB_USERNAME=ideab2b
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
JWT_SECRET=<secret>
SANCTUM_STATEFUL_DOMAINS=...
```

### Frontend (.env.gcloud.example)
```
VITE_API_URL=https://ideab2b-api.../api
VITE_APP_ENV=production
VITE_ENABLE_DEBUG=false
VITE_SENTRY_ENABLED=true
```

---

## 🔄 Deployment Process

### Typical Flow
```
1. Developer pushes to GitHub
   ↓
2. GitHub Actions triggered
   ↓
3. Run tests & quality checks
   ↓
4. If main branch:
   - Build Docker images
   - Push to Artifact Registry
   - Deploy to Cloud Run
   ↓
5. Cloud Run automatically routes traffic
   ↓
6. Health checks validate deployment
   ↓
7. Monitoring dashboards updated
```

### Rollback Procedure
```bash
# Deploy previous image version
gcloud run deploy ideab2b-api \
  --image=asia-south2-docker.pkg.dev/173142818446/ideab2b/api:previous-tag \
  --region=asia-south2
```

---

## 🗄️ Database & Cache

### Cloud SQL Setup
```
Instance: ideab2b-mysql
Engine: MySQL 8.0
Region: asia-south2
Tier: db-f1-micro (development)
Database: ideab2b
User: ideab2b
```

### Cloud Memorystore Setup
```
Instance: ideab2b-redis
Version: Redis 7.0
Region: asia-south2
Size: 1GB (development)
```

### Connection
```
Via Private IP (internal network):
- DB: 10.0.0.3:3306
- Redis: 10.0.0.4:6379
```

---

## 📦 Deployment Checklist

- [ ] Clone repository: `git clone https://github.com/ideaholiday/ideab2b.git`
- [ ] Install gcloud CLI
- [ ] Authenticate: `gcloud auth login`
- [ ] Set project: `gcloud config set project 173142818446`
- [ ] Run setup: `./deploy.sh setup`
- [ ] Build images: `./deploy.sh build`
- [ ] Deploy services: `./deploy.sh deploy`
- [ ] Verify URLs: `gcloud run services list --region=asia-south2`
- [ ] Run migrations: Create Cloud Run job for `php artisan migrate --force`
- [ ] Seed data: Create Cloud Run job for `php artisan db:seed`
- [ ] Test login: Visit frontend URL, login with test credentials
- [ ] Check logs: `gcloud run services logs read ideab2b-api`

---

## 🆘 Troubleshooting

### Services Not Starting
```bash
# Check service status
gcloud run services describe ideab2b-api --region=asia-south2

# View error logs
gcloud run services logs read ideab2b-api --limit=50
```

### Database Connection Issues
```bash
# Check Cloud SQL instance
gcloud sql instances describe ideab2b-mysql

# Test connection
gcloud sql connect ideab2b-mysql --user=root
```

### Image Push Issues
```bash
# Re-authenticate Docker
gcloud auth configure-docker asia-south2-docker.pkg.dev

# Check Artifact Registry
gcloud artifacts repositories describe ideab2b --location=asia-south2
```

---

## 📚 Documentation

- [DEPLOYMENT_GUIDE.md](./DEPLOYMENT_GUIDE.md) - Full setup guide
- [QUICKSTART_DEPLOY.md](./QUICKSTART_DEPLOY.md) - Quick reference
- [ARCHITECTURE.md](./ARCHITECTURE.md) - System architecture
- [README.md](./README.md) - Project overview

---

## 🔗 Useful Links

- [Google Cloud Console](https://console.cloud.google.com/welcome?project=173142818446)
- [Cloud Run Services](https://console.cloud.google.com/run?region=asia-south2&project=173142818446)
- [Artifact Registry](https://console.cloud.google.com/artifacts?project=173142818446)
- [GitHub Repository](https://github.com/ideaholiday/ideab2b)

---

## 📞 Support

For issues or questions:
1. Check logs: `gcloud run services logs read`
2. Review [DEPLOYMENT_GUIDE.md](./DEPLOYMENT_GUIDE.md)
3. Check GitHub Actions workflows
4. Contact: support@ideaholiday.com

---

**Status**: ✅ Ready for Production  
**Last Updated**: January 20, 2026  
**Version**: 1.0.0
