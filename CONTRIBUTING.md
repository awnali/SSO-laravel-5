# Contributing to Laravel 12 SSO

We love your input! We want to make contributing to Laravel 12 SSO as easy and transparent as possible, whether it's:

- Reporting a bug
- Discussing the current state of the code
- Submitting a fix
- Proposing new features
- Becoming a maintainer

## 🚀 Quick Start for Contributors

### Development Setup

1. **Fork the repository**
   ```bash
   git clone https://github.com/your-username/SSO-laravel-5.git
   cd SSO-laravel-5
   ```

2. **Install dependencies**
   ```bash
   # Install root dependencies for integration tests
   composer install
   
   # Install server dependencies
   cd server && composer install && cd ..
   
   # Install broker dependencies
   cd broker1 && composer install && cd ..
   cd broker2 && composer install && cd ..
   ```

3. **Setup development environment**
   ```bash
   # Copy environment files
   cp server/.env.example server/.env
   cp broker1/.env.example broker1/.env
   cp broker2/.env.example broker2/.env
   
   # Generate application keys
   cd server && php artisan key:generate && cd ..
   cd broker1 && php artisan key:generate && cd ..
   cd broker2 && php artisan key:generate && cd ..
   ```

4. **Run tests to ensure everything works**
   ```bash
   ./run-tests.sh all
   ```

## 🧪 Testing Guidelines

### Before Submitting Changes

Always run the complete test suite:

```bash
# Run all tests
./run-tests.sh all

# Or run specific test types
./run-tests.sh unit
./run-tests.sh feature
./run-tests.sh integration
```

### Writing Tests

- **Unit Tests**: Test individual classes and methods
- **Feature Tests**: Test HTTP endpoints and user interactions
- **Integration Tests**: Test complete SSO flows across applications

### Test Coverage Requirements

- All new features must include tests
- Bug fixes should include regression tests
- Aim for 100% test coverage on new code

## 📝 Code Style Guidelines

### PHP Code Standards

- Follow PSR-12 coding standards
- Use meaningful variable and method names
- Add PHPDoc comments for public methods
- Keep methods focused and single-purpose

### Laravel Best Practices

- Use Laravel's built-in features when possible
- Follow Laravel naming conventions
- Use dependency injection
- Implement proper error handling

### SSO-Specific Guidelines

- Maintain security best practices
- Document any changes to authentication flow
- Ensure backward compatibility with existing brokers
- Test with multiple brokers when making changes

## 🐛 Bug Reports

Great bug reports tend to have:

- A quick summary and/or background
- Steps to reproduce
  - Be specific!
  - Give sample code if you can
- What you expected would happen
- What actually happens
- Notes (possibly including why you think this might be happening, or stuff you tried that didn't work)

### Bug Report Template

```markdown
**Environment:**
- PHP Version: 
- Laravel Version: 
- OS: 
- Browser: 

**Steps to Reproduce:**
1. 
2. 
3. 

**Expected Behavior:**

**Actual Behavior:**

**Additional Context:**
```

## 🚀 Feature Requests

We welcome feature requests! Please:

1. Check if the feature already exists
2. Search existing issues for similar requests
3. Provide a clear use case
4. Consider the impact on existing functionality
5. Think about implementation complexity

### Feature Request Template

```markdown
**Problem Statement:**
What problem does this solve?

**Proposed Solution:**
How should this work?

**Use Case:**
Who would benefit and how?

**Implementation Ideas:**
Any thoughts on how to implement this?
```

## 🔄 Pull Request Process

### Before Creating a PR

1. **Create an issue** first to discuss the change
2. **Fork the repository** and create a feature branch
3. **Write tests** for your changes
4. **Ensure all tests pass**
5. **Update documentation** if needed

### PR Requirements

- [ ] Tests pass (`./run-tests.sh all`)
- [ ] Code follows style guidelines
- [ ] Documentation updated (if applicable)
- [ ] No breaking changes (or clearly documented)
- [ ] Descriptive commit messages

### PR Review Process

1. **Automated checks** must pass (GitHub Actions)
2. **Code review** by maintainers
3. **Testing** in development environment
4. **Merge** after approval

## 📚 Documentation

### What Needs Documentation

- New features and their usage
- Configuration changes
- API endpoint changes
- Breaking changes
- Migration guides

### Documentation Standards

- Clear, concise explanations
- Code examples where helpful
- Step-by-step instructions
- Screenshots for UI changes

## 🏷️ Versioning

We use [Semantic Versioning](http://semver.org/):

- **MAJOR**: Incompatible API changes
- **MINOR**: New functionality (backward compatible)
- **PATCH**: Bug fixes (backward compatible)

## 📄 License

By contributing, you agree that your contributions will be licensed under the MIT License.

## 🤝 Community

### Getting Help

- **GitHub Issues**: For bugs and feature requests
- **Discussions**: For questions and community support
- **Email**: For security issues (security@example.com)

### Code of Conduct

- Be respectful and inclusive
- Focus on constructive feedback
- Help others learn and grow
- Maintain a professional environment

## 🎯 Areas We Need Help

### High Priority

- [ ] Additional broker implementations
- [ ] Performance optimizations
- [ ] Security enhancements
- [ ] Documentation improvements

### Medium Priority

- [ ] UI/UX improvements
- [ ] Additional database drivers
- [ ] Monitoring and logging features
- [ ] Docker containerization

### Low Priority

- [ ] Additional language support
- [ ] Mobile app integration examples
- [ ] Advanced configuration options
- [ ] Performance benchmarking

## 🚀 Recognition

Contributors will be:

- Listed in the CONTRIBUTORS.md file
- Mentioned in release notes
- Given credit in documentation
- Invited to join the maintainer team (for significant contributions)

Thank you for contributing to Laravel 12 SSO! 🎉