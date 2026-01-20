# Contributing to IdeaB2B Travel Platform

Thank you for your interest in contributing to IdeaB2B! This document provides guidelines and instructions for contributing.

## Code of Conduct

- Be respectful and inclusive
- Assume good intent
- Respect different opinions
- Keep discussions professional

## Getting Started

### Fork & Clone

```bash
# Fork repository on GitHub
# Clone your fork
git clone https://github.com/YOUR_USERNAME/ideab2b.git
cd ideab2b

# Add upstream remote
git remote add upstream https://github.com/ideaholiday/ideab2b.git
```

### Create Feature Branch

```bash
# Create branch from develop
git checkout develop
git pull upstream develop
git checkout -b feature/your-feature-name
```

## Development Setup

### Backend Setup

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
```

### Frontend Setup

```bash
cd frontend
npm install
npm run dev
```

### Local Testing

```bash
# Start all services
docker-compose up -d

# Run migrations
docker exec ideab2b-api php artisan migrate

# Seed test data
docker exec ideab2b-api php artisan db:seed
```

## Making Changes

### Code Style

**PHP (Backend)**
```bash
# Run PHP CS Fixer
./vendor/bin/php-cs-fixer fix app/

# Run PHPStan
./vendor/bin/phpstan analyse app/
```

**JavaScript (Frontend)**
```bash
# Run ESLint
npm run lint

# Format with Prettier
npm run format
```

### Commit Messages

Follow [Conventional Commits](https://www.conventionalcommits.org/):

```
type(scope): description

body (optional)

footer (optional)
```

**Types:**
- `feat:` New feature
- `fix:` Bug fix
- `docs:` Documentation
- `style:` Code style
- `refactor:` Code refactoring
- `test:` Adding/updating tests
- `ci:` CI/CD changes
- `chore:` Maintenance

**Examples:**

```
feat(itinerary): add itinerary sharing feature

- Add share button to itinerary detail
- Send email notification to recipient
- Create unique share link with token

Closes #123
```

```
fix(api): fix user authentication error

Handle expired tokens correctly in API middleware.
```

## Testing

### Backend Tests

```bash
cd backend
php artisan test

# With coverage
php artisan test --coverage
```

### Frontend Tests

```bash
cd frontend
npm test

# With coverage
npm test -- --coverage
```

## Submitting Changes

### Push Your Branch

```bash
git push origin feature/your-feature-name
```

### Create Pull Request

1. Go to GitHub repository
2. Click "New Pull Request"
3. Select your branch
4. Fill in PR template:
   - Description of changes
   - Related issues
   - Type of change
   - Testing performed
   - Screenshots (if UI changes)

### PR Requirements

- ✓ All tests passing
- ✓ Code follows style guidelines
- ✓ Documentation updated
- ✓ No breaking changes (or clearly documented)
- ✓ Commit messages follow conventions
- ✓ At least 1 code review approval

### Code Review

- Respond to comments constructively
- Request changes if needed
- Push additional commits to same branch
- Do not force push while under review

## Large Changes

For significant changes:

1. **Discuss first**: Open an issue to discuss approach
2. **Design document**: Provide architecture/design details
3. **Implementation**: Follow the development process
4. **Review**: Plan for extended review period

## Documentation

Update documentation for:
- New features
- API changes
- Configuration changes
- Breaking changes

Documentation files:
- `README.md` - Project overview
- `ARCHITECTURE.md` - System design
- `SETUP_GUIDE.md` - Setup instructions
- `DEPLOYMENT_GUIDE.md` - Deployment process

## Reporting Issues

### Bug Reports

Include:
- [ ] Clear description of bug
- [ ] Steps to reproduce
- [ ] Expected behavior
- [ ] Actual behavior
- [ ] Environment (OS, browser, versions)
- [ ] Error messages/logs
- [ ] Screenshots (if applicable)

### Feature Requests

Include:
- [ ] Clear description of feature
- [ ] Use case/motivation
- [ ] Proposed solution
- [ ] Alternative solutions considered
- [ ] Potential impact

## Performance

- Minimize database queries
- Use caching appropriately
- Optimize asset sizes
- Consider mobile performance
- Profile before optimizing

## Security

- Don't commit secrets
- Use parameterized queries
- Validate all inputs
- Follow OWASP guidelines
- Report security issues privately

## Release Process

1. Create PR from develop to main
2. Increment version number
3. Update CHANGELOG.md
4. Merge to main
5. Create git tag
6. Create GitHub Release

Version Format: `vMAJOR.MINOR.PATCH`

## CI/CD Pipeline

All commits trigger:
- ✓ Linting checks
- ✓ Unit tests
- ✓ Integration tests
- ✓ Security scanning
- ✓ Docker builds

All must pass before merge.

## Need Help?

- **Questions**: Open a Discussion
- **Issues**: Create an Issue
- **Chat**: Check Discord/Slack
- **Email**: team@ideaholiday.com

## Recognition

Contributors will be:
- Listed in CONTRIBUTORS.md
- Mentioned in release notes
- Recognized in documentation

Thank you for contributing! 🚀

---

**Last Updated**: January 20, 2026  
**Maintainers**: ideaholiday.com
