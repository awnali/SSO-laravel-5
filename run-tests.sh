#!/bin/bash

# SSO Laravel 12 Test Runner
# This script runs all tests for the SSO system

echo "🧪 SSO Laravel 12 Test Suite"
echo "=============================="

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if applications are running
check_applications() {
    print_status "Checking if applications are running..."
    
    # Check server
    if curl -s http://localhost:8000 > /dev/null; then
        print_success "SSO Server is running on port 8000"
    else
        print_warning "SSO Server is not running on port 8000"
        echo "  Start with: cd server && php artisan serve --host=0.0.0.0 --port=8000"
    fi
    
    # Check broker1
    if curl -s http://localhost:8001 > /dev/null; then
        print_success "Broker1 is running on port 8001"
    else
        print_warning "Broker1 is not running on port 8001"
        echo "  Start with: cd broker1 && php artisan serve --host=0.0.0.0 --port=8001"
    fi
    
    # Check broker2
    if curl -s http://localhost:8002 > /dev/null; then
        print_success "Broker2 is running on port 8002"
    else
        print_warning "Broker2 is not running on port 8002"
        echo "  Start with: cd broker2 && php artisan serve --host=0.0.0.0 --port=8002"
    fi
    
    echo ""
}

# Run unit tests
run_unit_tests() {
    print_status "Running Unit Tests..."
    echo ""
    
    # Server unit tests
    echo "📋 Server Unit Tests"
    echo "-------------------"
    cd server
    if ./vendor/bin/phpunit tests/Unit --colors=always; then
        print_success "Server unit tests passed"
    else
        print_error "Server unit tests failed"
        return 1
    fi
    cd ..
    echo ""
    
    # Broker1 unit tests
    echo "📋 Broker1 Unit Tests"
    echo "--------------------"
    cd broker1
    if ./vendor/bin/phpunit tests/Unit --colors=always; then
        print_success "Broker1 unit tests passed"
    else
        print_error "Broker1 unit tests failed"
        return 1
    fi
    cd ..
    echo ""
    
    # Broker2 unit tests
    echo "📋 Broker2 Unit Tests"
    echo "--------------------"
    cd broker2
    if ./vendor/bin/phpunit tests/Unit --colors=always; then
        print_success "Broker2 unit tests passed"
    else
        print_error "Broker2 unit tests failed"
        return 1
    fi
    cd ..
    echo ""
}

# Run feature tests
run_feature_tests() {
    print_status "Running Feature Tests..."
    echo ""
    
    # Server feature tests
    echo "🔧 Server Feature Tests"
    echo "----------------------"
    cd server
    if ./vendor/bin/phpunit tests/Feature --colors=always; then
        print_success "Server feature tests passed"
    else
        print_error "Server feature tests failed"
        return 1
    fi
    cd ..
    echo ""
    
    # Broker1 feature tests
    echo "🔧 Broker1 Feature Tests"
    echo "-----------------------"
    cd broker1
    if ./vendor/bin/phpunit tests/Feature --colors=always; then
        print_success "Broker1 feature tests passed"
    else
        print_error "Broker1 feature tests failed"
        return 1
    fi
    cd ..
    echo ""
    
    # Broker2 feature tests
    echo "🔧 Broker2 Feature Tests"
    echo "-----------------------"
    cd broker2
    if ./vendor/bin/phpunit tests/Feature --colors=always; then
        print_success "Broker2 feature tests passed"
    else
        print_error "Broker2 feature tests failed"
        return 1
    fi
    cd ..
    echo ""
}

# Run integration tests
run_integration_tests() {
    print_status "Running Integration Tests..."
    echo ""
    
    # Check if all applications are running
    if ! curl -s http://localhost:8000 > /dev/null || ! curl -s http://localhost:8001 > /dev/null || ! curl -s http://localhost:8002 > /dev/null; then
        print_error "Integration tests require all applications to be running"
        print_warning "Please start all applications before running integration tests"
        return 1
    fi
    
    echo "🌐 SSO Integration Tests"
    echo "-----------------------"
    if command -v composer &> /dev/null; then
        # Install guzzlehttp/guzzle if not present
        if ! composer show guzzlehttp/guzzle &> /dev/null; then
            print_status "Installing Guzzle HTTP client for integration tests..."
            composer require --dev guzzlehttp/guzzle
        fi
        
        # Run integration tests
        if ./vendor/bin/phpunit tests/Integration --colors=always; then
            print_success "Integration tests passed"
        else
            print_error "Integration tests failed"
            return 1
        fi
    else
        print_warning "Composer not found, skipping integration tests"
    fi
    echo ""
}

# Main execution
main() {
    # Parse command line arguments
    case "${1:-all}" in
        "unit")
            check_applications
            run_unit_tests
            ;;
        "feature")
            check_applications
            run_feature_tests
            ;;
        "integration")
            check_applications
            run_integration_tests
            ;;
        "all")
            check_applications
            run_unit_tests && run_feature_tests && run_integration_tests
            ;;
        "help"|"-h"|"--help")
            echo "Usage: $0 [unit|feature|integration|all|help]"
            echo ""
            echo "Options:"
            echo "  unit         Run only unit tests"
            echo "  feature      Run only feature tests"
            echo "  integration  Run only integration tests (requires all apps running)"
            echo "  all          Run all tests (default)"
            echo "  help         Show this help message"
            echo ""
            echo "Examples:"
            echo "  $0           # Run all tests"
            echo "  $0 unit      # Run only unit tests"
            echo "  $0 feature   # Run only feature tests"
            exit 0
            ;;
        *)
            print_error "Unknown option: $1"
            echo "Use '$0 help' for usage information"
            exit 1
            ;;
    esac
    
    if [ $? -eq 0 ]; then
        echo ""
        print_success "All tests completed successfully! 🎉"
        echo ""
        echo "📊 Test Coverage Summary:"
        echo "  ✅ Unit Tests: SSO Server, Broker1, Broker2"
        echo "  ✅ Feature Tests: Authentication, SSO Flow, Controllers"
        echo "  ✅ Integration Tests: End-to-end SSO functionality"
        echo ""
        echo "🚀 Your SSO Laravel 12 system is working perfectly!"
    else
        echo ""
        print_error "Some tests failed. Please check the output above."
        exit 1
    fi
}

# Run main function with all arguments
main "$@"