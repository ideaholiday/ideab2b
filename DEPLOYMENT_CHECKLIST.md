# 📋 Google Cloud Deployment Checklist

## Pre-Deployment Requirements

### GCP Project Setup
- [ ] Google Cloud Project created: `ideab2b` (173142818446)
- [ ] Billing account activated
- [ ] Region set to: `asia-south2`
- [ ] Organization: ideaholiday.com (ID: 671238948318)

### Local Requirements
- [ ] `gcloud` CLI installed (`brew install google-cloud-sdk`)
- [ ] Docker installed (`brew install docker` or Docker Desktop)
- [ ] Git installed with GitHub access
- [ ] Repository cloned: `https://github.com/ideaholiday/ideab2b`

### GitHub Configuration
- [ ] GitHub repository created/configured
- [ ] SSH/HTTPS access configured
- [ ] Branch protection rules set on `main`
- [ ] Branch naming: `main`, `develop`, `feature/*`

---

## Phase 1: Initial Setup (30 minutes)

### 1.1 Authenticate with GCP
```bash
- [ ] Run: gcloud auth login
- [ ] Set project: gcloud config set project 173142818446
- [ ] Verify: gcloud config get-value project
```

### 1.2 Enable Required APIs
```bash
- [ ] Run: gcloud services enable \
        artifactregistry.googleapis.com \
        cloudbuild.googleapis.com \
        run.googleapis.com \
        sqladmin.googleapis.com \
        redis.googleapis.com \
        secretmanager.googleapis.com
```

### 1.3 Create Service Account
```bash
- [ ] Run: gcloud iam service-accounts create ideab2b-cloudrun
- [ ] Grant Cloud Run Admin role
- [ ] Grant Cloud SQL Client role
- [ ] Grant Secret Manager Accessor role
```

### 1.4 Create Artifact Registry
```bash
- [ ] Run: gcloud artifacts repositories create ideab2b \
        --repository-format=docker \
        --location=asia-south2
- [ ] Configure Docker: gcloud auth configure-docker asia-south2-docker.pkg.dev
```

**✅ Phase 1 Complete Estimate: 15-20 minutes**

---

## Phase 2: Local Testing (20 minutes)

### 2.1 Prepare Environment
```bash
- [ ] Copy .env file: cp .env.cloud .env
- [ ] Review environment variables
- [ ] Ensure MySQL, Redis configurations correct
```

### 2.2 Build Docker Images Locally
```bash
- [ ] Backend: docker build -t ideab2b-api:latest -f backend/Dockerfile ./backend
- [ ] Frontend: docker build -t ideab2b-web:latest -f frontend/Dockerfile ./frontend
- [ ] Verify images: docker images | grep ideab2b
```

### 2.3 Start Local Stack
```bash
- [ ] Run: docker-compose up -d
- [ ] Verify MySQL: docker exec ideab2b-mysql mysql -uroot -proot -e "SELECT 1"
- [ ] Verify Redis: docker exec ideab2b-redis redis-cli ping
- [ ] Check frontend: curl http://localhost:5173
- [ ] Check API: curl http://localhost:8000/api
```

### 2.4 Run Migrations & Seeds
```bash
- [ ] Migrations: docker exec ideab2b-api php artisan migrate
- [ ] Seed users: docker exec ideab2b-api php artisan db:seed
- [ ] Verify: docker exec ideab2b-mysql mysql -uroot -proot ideab2b -e "SELECT COUNT(*) FROM users"
```

### 2.5 Test Application
```bash
- [ ] Frontend loads: http://localhost:5173
- [ ] Login page visible
- [ ] Test login: admin@example.com / password
- [ ] Dashboard loads after login
- [ ] API responds to requests
```

**✅ Phase 2 Complete Estimate: 15-20 minutes**

---

## Phase 3: Push to Artifact Registry (20 minutes)

### 3.1 Tag Images
```bash
- [ ] API: docker tag ideab2b-api:latest \
        asia-south2-docker.pkg.dev/173142818446/ideab2b/api:latest
- [ ] Web: docker tag ideab2b-web:latest \
        asia-south2-docker.pkg.dev/173142818446/ideab2b/web:latest
```

### 3.2 Push Images
```bash
- [ ] Push API: docker push asia-south2-docker.pkg.dev/173142818446/ideab2b/api:latest
- [ ] Push Web: docker push asia-south2-docker.pkg.dev/173142818446/ideab2b/web:latest
- [ ] Verify API image exists in Artifact Registry
- [ ] Verify Web image exists in Artifact Registry
```

**✅ Phase 3 Complete Estimate: 10-15 minutes**

---

## Phase 4: Deploy Infrastructure (30 minutes)

### 4.1 Create Cloud SQL Instance
```bash
- [ ] Run: gcloud sql instances create ideab2b-mysql \
        --database-version=MYSQL_8_0 \
        --tier=db-f1-micro \
        --region=asia-south2
- [ ] Create database: gcloud sql databases create ideab2b \
        --instance=ideab2b-mysql
- [ ] Create user: gcloud sql users create ideab2b \
        --instance=ideab2b-mysql \
        --password=SECURE_PASSWORD
- [ ] Note down: Private IP address
```

### 4.2 Create Cloud Memorystore (Redis)
```bash
- [ ] Run: gcloud redis instances create ideab2b-redis \
        --size=1 \
        --region=asia-south2 \
        --redis-version=7.0
- [ ] Wait for creation (2-3 minutes)
- [ ] Note down: Private IP address
```

### 4.3 Create Secrets
```bash
- [ ] APP_KEY: gcloud secrets create ideab2b-app-key --data-file=-
- [ ] JWT_SECRET: gcloud secrets create ideab2b-jwt-secret --data-file=-
- [ ] DB_PASSWORD: gcloud secrets create ideab2b-db-password --data-file=-
- [ ] Verify: gcloud secrets list
```

**✅ Phase 4 Complete Estimate: 20-30 minutes (waiting for service creation)**

---

## Phase 5: Deploy to Cloud Run (15 minutes)

### 5.1 Deploy Backend API
```bash
- [ ] Run deployment command (see DEPLOYMENT_GUIDE.md)
- [ ] Verify: gcloud run services list --region=asia-south2
- [ ] Note API URL
- [ ] Check logs: gcloud run services logs read ideab2b-api
```

### 5.2 Deploy Frontend
```bash
- [ ] Run deployment command (see DEPLOYMENT_GUIDE.md)
- [ ] Verify: gcloud run services list --region=asia-south2
- [ ] Note Frontend URL
- [ ] Check logs: gcloud run services logs read ideab2b-web
```

### 5.3 Verify Services
```bash
- [ ] Frontend loads: Visit Cloud Run URL
- [ ] Backend accessible: curl https://SERVICE_URL/api
- [ ] Health check passes: curl https://API_URL/health
- [ ] Services have public IPs
```

**✅ Phase 5 Complete Estimate: 10-15 minutes**

---

## Phase 6: Database Configuration (15 minutes)

### 6.1 Create Migration Job
```bash
- [ ] Create Cloud Run job for migrations
- [ ] Set command: php artisan migrate --force
- [ ] Set secrets and environment variables
- [ ] Execute job
- [ ] Verify tables created: Check Cloud SQL
```

### 6.2 Create Seed Job
```bash
- [ ] Create Cloud Run job for seeding
- [ ] Set command: php artisan db:seed
- [ ] Execute job
- [ ] Verify data: Check user count in database
```

### 6.3 Verify Database
```bash
- [ ] Connect to Cloud SQL: gcloud sql connect ideab2b-mysql
- [ ] Check tables: SHOW TABLES;
- [ ] Check users: SELECT COUNT(*) FROM users;
- [ ] Verify test data exists
```

**✅ Phase 6 Complete Estimate: 10-15 minutes**

---

## Phase 7: GitHub Actions Setup (20 minutes)

### 7.1 Configure Workload Identity Federation
```bash
- [ ] Create Workload Identity Pool
- [ ] Create Provider
- [ ] Get Provider resource name
- [ ] Grant service account permissions
```

### 7.2 Add GitHub Secrets
```bash
- [ ] WIF_PROVIDER: (from WIF setup)
- [ ] WIF_SERVICE_ACCOUNT: ideab2b-cloudrun@173142818446.iam.gserviceaccount.com
- [ ] SANCTUM_DOMAINS: ideab2b-173142818446.asia-south2.run.app
- [ ] SESSION_DOMAIN: .ideab2b-173142818446.asia-south2.run.app
```

### 7.3 Test CI/CD
```bash
- [ ] Make a commit to main branch
- [ ] Verify GitHub Actions triggered
- [ ] Check workflow status
- [ ] Verify Cloud Run deployment updated
```

**✅ Phase 7 Complete Estimate: 15-20 minutes**

---

## Phase 8: Testing & Verification (30 minutes)

### 8.1 Test Frontend
```bash
- [ ] Access: https://ideab2b-173142818446.asia-south2.run.app
- [ ] Page loads without errors
- [ ] Login form visible
- [ ] Can submit credentials
```

### 8.2 Test Authentication
```bash
- [ ] Login as admin: admin@example.com / password
- [ ] Token stored in localStorage
- [ ] Redirected to dashboard
- [ ] User info displayed
- [ ] Can logout
```

### 8.3 Test API Endpoints
```bash
- [ ] GET /api/me (with token)
- [ ] GET /api/destinations
- [ ] GET /api/cities/{id}
- [ ] GET /api/hotels/{city_id}
- [ ] GET /api/sightseeings/{city_id}
```

### 8.4 Test Role-Based Access
```bash
- [ ] Admin can access admin endpoints
- [ ] Agent can create itineraries
- [ ] Hotel partner can view hotels
- [ ] Operator can view assignments
- [ ] Unauthorized access returns 403
```

### 8.5 Performance Testing
```bash
- [ ] Response time < 500ms
- [ ] Database queries optimized
- [ ] No N+1 query issues
- [ ] Redis caching working
- [ ] Static assets cached
```

**✅ Phase 8 Complete Estimate: 20-30 minutes**

---

## Phase 9: Monitoring & Logging (10 minutes)

### 9.1 Setup Monitoring
```bash
- [ ] Enable Cloud Monitoring
- [ ] Create dashboard for metrics
- [ ] Set up alerts for errors
- [ ] Configure log retention (30 days)
```

### 9.2 Verify Logging
```bash
- [ ] Application logs flowing to Cloud Logging
- [ ] Error logs captured
- [ ] Request logs visible
- [ ] Can filter by service
- [ ] Can search by error message
```

**✅ Phase 9 Complete Estimate: 5-10 minutes**

---

## Phase 10: Custom Domain Setup (Optional, 20 minutes)

### 10.1 Register Domain
```bash
- [ ] Domain registered: ideaholiday.com
- [ ] DNS provider configured
- [ ] Domain transferred (if needed)
```

### 10.2 Map Custom Domain
```bash
- [ ] Create domain mapping for API: api.ideaholiday.com → ideab2b-api
- [ ] Create domain mapping for Web: ideaholiday.com → ideab2b-web
- [ ] Wait for SSL certificate (10-15 minutes)
- [ ] Update DNS records with provided IPs
```

### 10.3 Verify Custom Domain
```bash
- [ ] Frontend accessible via https://ideaholiday.com
- [ ] API accessible via https://api.ideaholiday.com/api
- [ ] SSL certificates valid
- [ ] Redirects working
```

**✅ Phase 10 Complete Estimate: 15-20 minutes (varies by DNS propagation)**

---

## Phase 11: Backup & Security (15 minutes)

### 11.1 Enable Backups
```bash
- [ ] Cloud SQL automated backups enabled
- [ ] Backup window set: 03:00 UTC
- [ ] Retention: 30 days
- [ ] Test restoration
```

### 11.2 Security Hardening
```bash
- [ ] Enable Cloud Armor (DDoS protection)
- [ ] Configure IAM roles (least privilege)
- [ ] Enable audit logging
- [ ] Set up VPC connectors
- [ ] Review and restrict API access
```

### 11.3 Compliance
```bash
- [ ] Data encryption at rest: Enabled
- [ ] Data encryption in transit: TLS 1.2+
- [ ] GDPR compliance: Reviewed
- [ ] Data retention policy: Set
```

**✅ Phase 11 Complete Estimate: 10-15 minutes**

---

## Post-Deployment Maintenance

### Weekly
- [ ] Review Cloud Logging for errors
- [ ] Check Cloud Monitoring dashboards
- [ ] Verify backup completion
- [ ] Review security alerts

### Monthly
- [ ] Test backup restoration
- [ ] Review and optimize costs
- [ ] Update dependencies
- [ ] Security audit

### Quarterly
- [ ] Load testing
- [ ] Disaster recovery drill
- [ ] Performance optimization review
- [ ] Architecture review

---

## Final Verification Checklist

### Services Running
- [ ] ideab2b-api service active and healthy
- [ ] ideab2b-web service active and healthy
- [ ] Cloud SQL instance running
- [ ] Cloud Memorystore running

### Data
- [ ] Database tables created
- [ ] Test users seeded
- [ ] Sample destinations/cities/hotels in database

### Access
- [ ] Frontend URL accessible
- [ ] API URL accessible
- [ ] Login working
- [ ] Dashboard displays correct role
- [ ] API endpoints responding

### Monitoring
- [ ] Logs being collected
- [ ] Metrics being recorded
- [ ] Alerts configured
- [ ] Dashboards accessible

### Documentation
- [ ] README.md updated
- [ ] Environment variables documented
- [ ] Deployment process documented
- [ ] Runbooks created

---

## Deployment Statistics

**Estimated Total Time:**
- Quick deployment: 1-2 hours
- Full deployment with testing: 3-4 hours
- Including custom domain & security: 4-5 hours

**Resource Costs (Approximate Monthly):**
- Cloud Run (API): $10-20
- Cloud Run (Web): $5-10
- Cloud SQL: $10-15
- Cloud Memorystore: $10-15
- Cloud Build: $0.003 per build minute
- **Total**: $35-60/month for development tier

**Files Created:**
- 2 Dockerfiles
- 1 docker-compose.yml
- 1 cloudbuild.yaml
- 2 GitHub Actions workflows
- 7 Configuration files
- 5 Documentation files
- 1 Kubernetes manifest

---

## Success Criteria

### Deployment Successful When:
✅ Frontend loads without errors  
✅ User can login and see dashboard  
✅ All API endpoints respond correctly  
✅ Database migrations completed  
✅ Test data present and accessible  
✅ Monitoring and logging active  
✅ Zero critical errors in logs  
✅ Response times < 500ms  
✅ All tests passing  
✅ Custom domain working (if configured)

---

## Emergency Rollback

If deployment fails:

```bash
# 1. Identify issue
gcloud run services logs read ideab2b-api --limit=50

# 2. Scale down new version
gcloud run update-traffic ideab2b-api --to-revisions PREVIOUS=100

# 3. Alternative: Deploy previous image
gcloud run deploy ideab2b-api \
  --image=PREVIOUS_IMAGE_URL \
  --region=asia-south2

# 4. Test rollback
curl https://ideab2b-api-...run.app/health

# 5. Investigate and fix
# ... debug and fix issues ...

# 6. Re-deploy
./deploy.sh deploy
```

---

**Deployment Date**: _______________  
**Deployed By**: _______________  
**Approval**: _______________  

✅ **All Phases Complete!** - Application ready for production use.
