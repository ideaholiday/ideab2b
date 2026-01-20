# GitHub Repository Setup Guide

## Repository Information

- **Repository**: https://github.com/ideaholiday/ideab2b
- **Organization**: ideaholiday.com
- **Organization ID**: 671238948318

## Initial Setup

### 1. Clone the Repository

```bash
git clone https://github.com/ideaholiday/ideab2b.git
cd ideab2b
```

### 2. Add Project Files

All project files have been created in the working directory. Copy them to the cloned repository:

```bash
# Copy all files (excluding .git)
cp -r travel-platform/* .
```

### 3. Initialize Git (if fresh repository)

```bash
# Create initial commit
git add .
git commit -m "Initial commit: B2B Travel Itinerary Platform with GCP deployment"

# Push to main branch
git branch -M main
git push -u origin main
```

### 4. Create Development Branch

```bash
git checkout -b develop
git push -u origin develop
```

## Branch Strategy

```
main (production)
  ↓
develop (staging)
  ↓
feature/* (feature branches)
```

### Protected Branches

Configure in GitHub Settings → Branches:

**Main Branch Protection:**
- ✓ Require pull request reviews before merging
- ✓ Require status checks to pass before merging
- ✓ Include administrators
- ✓ Require branches to be up to date

**Develop Branch Protection:**
- ✓ Require pull request reviews before merging
- ✓ Require status checks to pass before merging

## GitHub Actions Setup

### 1. Create Secrets

In GitHub Settings → Secrets and variables → Actions:

#### GCP Secrets:
```
WIF_PROVIDER
  Value: projects/173142818446/locations/global/workloadIdentityPools/github/providers/github-provider

WIF_SERVICE_ACCOUNT
  Value: ideab2b-cloudrun@173142818446.iam.gserviceaccount.com

GCP_PROJECT_ID
  Value: 173142818446

SANCTUM_DOMAINS
  Value: ideab2b-173142818446.asia-south2.run.app

SESSION_DOMAIN
  Value: .ideab2b-173142818446.asia-south2.run.app
```

### 2. Workflows Included

**tests.yml** - Runs on every push and PR:
- Backend tests (PHP + MySQL)
- Frontend tests (JavaScript)
- Code quality checks
- Docker build verification
- Security scanning

**deploy-gcloud.yml** - Runs on main branch push:
- Build Docker images
- Push to Artifact Registry
- Deploy to Cloud Run
- Run database migrations
- Health checks

## Contributing Guidelines

### Commit Messages

```
Type: Subject

Body (optional)

Footer (optional)
```

**Types:**
- `feat:` New feature
- `fix:` Bug fix
- `docs:` Documentation
- `style:` Code style
- `refactor:` Code refactoring
- `test:` Adding tests
- `ci:` CI/CD changes
- `chore:` Maintenance

### Examples:

```
feat: Add itinerary sharing feature

Implements sharing of created itineraries via email
- Add share button to itinerary detail page
- Send email notification to recipient
- Create shared link with unique token

Closes #123
```

## Code Review Process

1. Create feature branch from develop
2. Make changes and push to GitHub
3. Create Pull Request to develop
4. Wait for CI/CD checks to pass
5. Request review from team members
6. Address review comments
7. Merge to develop
8. Create PR from develop to main for release

## Release Process

1. Create release branch from develop: `release/v1.0.0`
2. Update version numbers
3. Create PR to main
4. Merge to main (triggers deployment)
5. Create git tag: `git tag -a v1.0.0 -m "Version 1.0.0"`
6. Push tag: `git push origin v1.0.0`
7. Merge main back to develop

## Deployment Workflow

### Automatic Deployment (CI/CD)

1. **Push to develop**: Runs tests, builds Docker images
2. **Push to main**: Runs tests + deploys to Cloud Run

### Manual Deployment

```bash
# From main branch
chmod +x deploy.sh
./deploy.sh build
./deploy.sh deploy
```

## Repository Structure

```
ideab2b/
├── backend/                          # Laravel API
│   ├── app/
│   ├── database/
│   ├── routes/
│   ├── Dockerfile
│   └── composer.json
├── frontend/                         # React App
│   ├── src/
│   ├── public/
│   ├── Dockerfile
│   └── package.json
├── k8s/                             # Kubernetes configs
├── .github/workflows/               # CI/CD workflows
├── docker-compose.yml               # Local dev
├── cloudbuild.yaml                  # Cloud Build config
├── deploy.sh                        # Deployment script
└── README.md                        # Project docs
```

## Security Considerations

### Secrets Management

Never commit:
- `.env` files
- AWS credentials
- API keys
- Database passwords
- JWT secrets

Use GitHub Secrets for CI/CD and Secret Manager for GCP.

### Code Scanning

GitHub Advanced Security (if enabled):
- ✓ Code scanning with CodeQL
- ✓ Dependency scanning
- ✓ Secret scanning

### IP Allowlist

If your organization uses IP allowlist, add GitHub Actions runner IPs.

## Backup & Disaster Recovery

### Regular Backups

```bash
# Mirror clone (full backup)
git clone --mirror https://github.com/ideaholiday/ideab2b.git ideab2b.git

# Push to backup location
cd ideab2b.git
git push --mirror https://backup.example.com/ideab2b.git
```

### Restore from Backup

```bash
git clone --mirror /path/to/backup/ideab2b.git
cd ideab2b.git
git push --mirror https://github.com/ideaholiday/ideab2b.git
```

## Team Management

### Add Team Members

In GitHub Repository → Settings → Collaborators:

```
Role assignments:
- Admin: DevOps, Tech Lead
- Maintain: Senior Developers
- Triage: Developers
- Push: Contributors
- Pull: Viewers
```

### Code Ownership

Create `CODEOWNERS` file:

```
# Global owners
* @owner1 @owner2

# Backend
backend/ @backend-team

# Frontend
frontend/ @frontend-team

# DevOps
k8s/ @devops-team
.github/ @devops-team
```

## Useful GitHub Commands

```bash
# Clone with SSH (if configured)
git clone git@github.com:ideaholiday/ideab2b.git

# Sync fork
git remote add upstream https://github.com/ideaholiday/ideab2b.git
git fetch upstream
git merge upstream/main

# Push to multiple remotes
git remote add backup https://backup.example.com/ideab2b.git
git push --all
```

## Troubleshooting

### Authentication Issues

```bash
# If using HTTPS, create Personal Access Token (PAT)
git remote set-url origin https://YOUR_TOKEN@github.com/ideaholiday/ideab2b.git

# If using SSH, add key to agent
ssh-add ~/.ssh/id_ed25519
```

### Merge Conflicts

```bash
# View conflicts
git status

# Resolve manually, then
git add .
git commit -m "Resolve merge conflicts"
git push
```

### Revert Commits

```bash
# Revert last commit
git revert HEAD

# Revert specific commit
git revert <commit-hash>

# Hard reset (⚠️ use with caution)
git reset --hard <commit-hash>
```

## Additional Resources

- [GitHub Documentation](https://docs.github.com)
- [Git Book](https://git-scm.com/book)
- [GitHub Flow Guide](https://guides.github.com/introduction/flow/)
- [Conventional Commits](https://www.conventionalcommits.org/)

## Support

For issues related to:
- **Deployment**: See DEPLOYMENT_GUIDE.md
- **Architecture**: See ARCHITECTURE.md
- **Setup**: See SETUP_GUIDE.md

---

**Repository URL**: https://github.com/ideaholiday/ideab2b  
**Last Updated**: January 20, 2026
