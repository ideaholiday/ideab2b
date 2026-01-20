#!/bin/bash

# IdeaB2B Google Cloud Deployment Script
# Deploys application to Google Cloud Run with all necessary setup

set -e

# Color codes
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
PROJECT_ID="173142818446"
REGION="asia-south2"
SERVICE_ACCOUNT="ideab2b-cloudrun"
REPOSITORY="ideab2b"
API_SERVICE="ideab2b-api"
WEB_SERVICE="ideab2b-web"
DB_INSTANCE="ideab2b-mysql"
REDIS_INSTANCE="ideab2b-redis"

# Functions
print_header() {
    echo -e "${BLUE}========================================${NC}"
    echo -e "${BLUE}$1${NC}"
    echo -e "${BLUE}========================================${NC}"
}

print_success() {
    echo -e "${GREEN}✓ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠ $1${NC}"
}

print_error() {
    echo -e "${RED}✗ $1${NC}"
}

check_requirements() {
    print_header "Checking Requirements"
    
    command -v gcloud >/dev/null 2>&1 || { print_error "gcloud CLI is not installed"; exit 1; }
    print_success "gcloud CLI found"
    
    command -v docker >/dev/null 2>&1 || { print_warning "Docker not found - skipping local build"; }
    print_success "Docker found"
    
    command -v git >/dev/null 2>&1 || { print_error "git is not installed"; exit 1; }
    print_success "git found"
}

setup_gcp_services() {
    print_header "Setting up GCP Services"
    
    # Enable required APIs
    echo "Enabling required APIs..."
    gcloud services enable \
        artifactregistry.googleapis.com \
        cloudbuild.googleapis.com \
        run.googleapis.com \
        sqladmin.googleapis.com \
        redis.googleapis.com \
        secretmanager.googleapis.com \
        cloudresourcemanager.googleapis.com \
        iam.googleapis.com \
        --project=$PROJECT_ID
    
    print_success "GCP APIs enabled"
}

create_service_account() {
    print_header "Creating Service Account"
    
    if gcloud iam service-accounts describe $SERVICE_ACCOUNT@$PROJECT_ID.iam.gserviceaccount.com --project=$PROJECT_ID >/dev/null 2>&1; then
        print_warning "Service account already exists"
    else
        gcloud iam service-accounts create $SERVICE_ACCOUNT \
            --display-name="IdeaB2B Cloud Run Service Account" \
            --project=$PROJECT_ID
        print_success "Service account created"
    fi
    
    # Grant roles
    gcloud projects add-iam-policy-binding $PROJECT_ID \
        --member="serviceAccount:$SERVICE_ACCOUNT@$PROJECT_ID.iam.gserviceaccount.com" \
        --role="roles/run.admin"
    
    gcloud projects add-iam-policy-binding $PROJECT_ID \
        --member="serviceAccount:$SERVICE_ACCOUNT@$PROJECT_ID.iam.gserviceaccount.com" \
        --role="roles/cloudsql.client"
    
    print_success "IAM roles assigned"
}

create_artifact_registry() {
    print_header "Creating Artifact Registry Repository"
    
    if gcloud artifacts repositories describe $REPOSITORY --location=$REGION --project=$PROJECT_ID >/dev/null 2>&1; then
        print_warning "Artifact Registry repository already exists"
    else
        gcloud artifacts repositories create $REPOSITORY \
            --repository-format=docker \
            --location=$REGION \
            --project=$PROJECT_ID
        print_success "Artifact Registry repository created"
    fi
    
    # Configure Docker
    gcloud auth configure-docker $REGION-docker.pkg.dev
    print_success "Docker authentication configured"
}

build_and_push_images() {
    print_header "Building and Pushing Docker Images"
    
    API_IMAGE="$REGION-docker.pkg.dev/$PROJECT_ID/$REPOSITORY/$API_SERVICE:latest"
    WEB_IMAGE="$REGION-docker.pkg.dev/$PROJECT_ID/$REPOSITORY/$WEB_SERVICE:latest"
    
    # Build backend
    echo "Building backend image..."
    docker build -t $API_IMAGE -f backend/Dockerfile ./backend
    print_success "Backend image built"
    
    # Push backend
    echo "Pushing backend image..."
    docker push $API_IMAGE
    print_success "Backend image pushed"
    
    # Build frontend
    echo "Building frontend image..."
    docker build -t $WEB_IMAGE -f frontend/Dockerfile ./frontend
    print_success "Frontend image built"
    
    # Push frontend
    echo "Pushing frontend image..."
    docker push $WEB_IMAGE
    print_success "Frontend image pushed"
}

deploy_services() {
    print_header "Deploying Services to Cloud Run"
    
    API_IMAGE="$REGION-docker.pkg.dev/$PROJECT_ID/$REPOSITORY/$API_SERVICE:latest"
    WEB_IMAGE="$REGION-docker.pkg.dev/$PROJECT_ID/$REPOSITORY/$WEB_SERVICE:latest"
    
    # Deploy API
    echo "Deploying API service..."
    gcloud run deploy $API_SERVICE \
        --image=$API_IMAGE \
        --platform=managed \
        --region=$REGION \
        --allow-unauthenticated \
        --service-account=$SERVICE_ACCOUNT@$PROJECT_ID.iam.gserviceaccount.com \
        --cpu=2 \
        --memory=512Mi \
        --timeout=3600 \
        --max-instances=100 \
        --project=$PROJECT_ID
    
    print_success "API service deployed"
    
    # Deploy Web
    echo "Deploying Web service..."
    gcloud run deploy $WEB_SERVICE \
        --image=$WEB_IMAGE \
        --platform=managed \
        --region=$REGION \
        --allow-unauthenticated \
        --service-account=$SERVICE_ACCOUNT@$PROJECT_ID.iam.gserviceaccount.com \
        --cpu=1 \
        --memory=256Mi \
        --timeout=3600 \
        --max-instances=50 \
        --project=$PROJECT_ID
    
    print_success "Web service deployed"
}

display_deployment_info() {
    print_header "Deployment Complete!"
    
    API_URL=$(gcloud run services describe $API_SERVICE --platform=managed --region=$REGION --project=$PROJECT_ID --format='value(status.url)')
    WEB_URL=$(gcloud run services describe $WEB_SERVICE --platform=managed --region=$REGION --project=$PROJECT_ID --format='value(status.url)')
    
    echo -e "${GREEN}API URL:${NC} $API_URL"
    echo -e "${GREEN}Web URL:${NC} $WEB_URL"
    echo ""
    echo "Next steps:"
    echo "1. Run database migrations:"
    echo "   gcloud run jobs create ideab2b-migrate --image=$API_IMAGE --task-timeout=600s --region=$REGION"
    echo ""
    echo "2. Seed test data:"
    echo "   gcloud run jobs create ideab2b-seed --image=$API_IMAGE --task-timeout=600s --region=$REGION"
    echo ""
    echo "3. Monitor logs:"
    echo "   gcloud run services logs read $API_SERVICE --region=$REGION"
}

# Main execution
main() {
    print_header "IdeaB2B Google Cloud Deployment Script"
    
    # Parse arguments
    case "${1:-all}" in
        all)
            check_requirements
            setup_gcp_services
            create_service_account
            create_artifact_registry
            build_and_push_images
            deploy_services
            display_deployment_info
            ;;
        build)
            build_and_push_images
            ;;
        deploy)
            deploy_services
            display_deployment_info
            ;;
        setup)
            setup_gcp_services
            create_service_account
            create_artifact_registry
            ;;
        *)
            echo "Usage: $0 [all|build|deploy|setup]"
            exit 1
            ;;
    esac
}

main "$@"
