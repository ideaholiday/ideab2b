# Google Cloud Deployment Configuration
# File: .gcloud-config.sh

#!/bin/bash

# Google Cloud Project Settings
export PROJECT_ID="your-gcp-project-id"
export REGION="asia-south2"
export SERVICE_NAME="ideab2b"

# Container Registry Settings
export REGISTRY="asia-south2-docker.pkg.dev"
export BACKEND_IMAGE="${REGISTRY}/${PROJECT_ID}/${SERVICE_NAME}/api"
export FRONTEND_IMAGE="${REGISTRY}/${PROJECT_ID}/${SERVICE_NAME}/web"

# Cloud Run Settings
export BACKEND_SERVICE="ideab2b-api"
export FRONTEND_SERVICE="ideab2b-web"

# Cloud SQL Settings (if using)
export DB_INSTANCE="ideab2b-db"
export DB_USER="root"

# Environment
export APP_ENV="production"
export APP_DEBUG="false"

# Functions
setup_gcloud() {
    echo "🔧 Setting up Google Cloud..."
    gcloud config set project $PROJECT_ID
    gcloud auth login
    gcloud auth configure-docker $REGISTRY
}

enable_apis() {
    echo "📡 Enabling required Google Cloud APIs..."
    gcloud services enable \
        cloudbuild.googleapis.com \
        run.googleapis.com \
        artifactregistry.googleapis.com \
        container.googleapis.com \
        sqladmin.googleapis.com
}

create_artifact_registry() {
    echo "📦 Creating Artifact Registry..."
    gcloud artifacts repositories create ${SERVICE_NAME} \
        --repository-format=docker \
        --location=${REGION} \
        --description="IdeaB2B Travel Platform Docker Images"
}

deploy_backend() {
    echo "🚀 Deploying Backend..."
    gcloud run deploy ${BACKEND_SERVICE} \
        --image=${BACKEND_IMAGE}:latest \
        --region=${REGION} \
        --platform=managed \
        --allow-unauthenticated \
        --set-env-vars APP_ENV=${APP_ENV},APP_DEBUG=${APP_DEBUG} \
        --memory=1Gi \
        --cpu=1
}

deploy_frontend() {
    echo "🚀 Deploying Frontend..."
    gcloud run deploy ${FRONTEND_SERVICE} \
        --image=${FRONTEND_IMAGE}:latest \
        --region=${REGION} \
        --platform=managed \
        --allow-unauthenticated \
        --memory=512Mi \
        --cpu=1
}

setup_cloud_build_trigger() {
    echo "🔄 Setting up Cloud Build Trigger..."
    gcloud builds connect github \
        --authorizer-token=GITHUB_TOKEN \
        --region=${REGION}
}

all_setup() {
    setup_gcloud
    enable_apis
    create_artifact_registry
}

all_deploy() {
    deploy_backend
    deploy_frontend
}

# Show menu
show_menu() {
    echo ""
    echo "==================================="
    echo "IdeaB2B Google Cloud Deployment"
    echo "==================================="
    echo "1. Setup Google Cloud"
    echo "2. Enable APIs"
    echo "3. Create Artifact Registry"
    echo "4. Deploy Backend"
    echo "5. Deploy Frontend"
    echo "6. Setup Cloud Build Trigger"
    echo "7. Setup All"
    echo "8. Deploy All"
    echo "9. Exit"
    echo ""
}

# Main menu
if [ "$#" -eq 0 ]; then
    while true; do
        show_menu
        read -p "Select option: " choice
        case $choice in
            1) setup_gcloud ;;
            2) enable_apis ;;
            3) create_artifact_registry ;;
            4) deploy_backend ;;
            5) deploy_frontend ;;
            6) setup_cloud_build_trigger ;;
            7) all_setup ;;
            8) all_deploy ;;
            9) exit 0 ;;
            *) echo "Invalid option" ;;
        esac
    done
else
    case "$1" in
        setup) setup_gcloud ;;
        enable) enable_apis ;;
        registry) create_artifact_registry ;;
        deploy-backend) deploy_backend ;;
        deploy-frontend) deploy_frontend ;;
        trigger) setup_cloud_build_trigger ;;
        setup-all) all_setup ;;
        deploy-all) all_deploy ;;
        *) echo "Unknown command: $1" ;;
    esac
fi
