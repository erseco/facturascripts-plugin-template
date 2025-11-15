# Makefile to facilitate the use of Docker for FacturaScripts plugin development

.PHONY: help up upd down pull build shell clean package enable-plugin rebuild test logs ps fresh check-docker

# Define SED_INPLACE based on the operating system
ifeq ($(shell uname), Darwin)
  SED_INPLACE = sed -i ''
else
  SED_INPLACE = sed -i
endif

# Detect the operating system
ifeq ($(OS),Windows_NT)
    ifdef MSYSTEM
        SYSTEM_OS := unix
    else ifdef CYGWIN
        SYSTEM_OS := unix
    else
        SYSTEM_OS := windows
    endif
else
    SYSTEM_OS := unix
endif

# Check if Docker is running
check-docker:
ifeq ($(SYSTEM_OS),windows)
	@echo "Detected system: Windows (cmd, powershell)"
	@docker version > NUL 2>&1 || (echo. & echo Error: Docker is not running. Please make sure Docker is installed and running. & echo. & exit 1)
else
	@echo "Detected system: Unix (Linux/macOS/Cygwin/MinGW)"
	@docker version > /dev/null 2>&1 || (echo "" && echo "Error: Docker is not running. Please make sure Docker is installed and running." && echo "" && exit 1)
endif

# Start Docker containers in interactive mode
up: check-docker
	docker compose up --remove-orphans

# Start Docker containers in background mode (daemon)
upd: check-docker
	docker compose up --detach --remove-orphans

# Stop and remove Docker containers
down: check-docker
	docker compose down

# Pull the latest images from the registry
pull: check-docker
	docker compose -f docker-compose.yml pull

# Build or rebuild Docker containers
build: check-docker
	docker compose build

# Open a shell inside the facturascripts container
shell: check-docker
	docker compose exec facturascripts sh

# Clean up and stop Docker containers, removing volumes and orphan containers
clean: check-docker
	docker compose down -v --remove-orphans

# Generate the PluginTemplate-X.X.X.zip package
package:
	@if [ -z "$(VERSION)" ]; then \
		echo "Error: VERSION not specified. Use 'make package VERSION=1.2.3'"; \
		exit 1; \
	fi
	@echo "Updating version to $(VERSION) in facturascripts.ini..."
	$(SED_INPLACE) 's/^\(version[[:space:]]*=[[:space:]]*\).*$$/\1$(VERSION)/' facturascripts.ini
	@echo "Creating ZIP archive: PluginTemplate-$(VERSION).zip..."
	@mkdir -p dist
	@zip -r dist/PluginTemplate-$(VERSION).zip . \
		-x "*.git*" \
		-x "*examples/*" \
		-x "*dist/*" \
		-x "*vendor/*" \
		-x "*node_modules/*" \
		-x "*.DS_Store" \
		-x "*Makefile" \
		-x "*docker-compose.yml" \
		-x "*.md"
	@echo "Restoring version in facturascripts.ini..."
	$(SED_INPLACE) 's/^\(version[[:space:]]*=[[:space:]]*\).*$$/\11.0/' facturascripts.ini
	@echo "Package created: dist/PluginTemplate-$(VERSION).zip"

# Enable the plugin in FacturaScripts
enable-plugin: check-docker
	@echo "Enabling PluginTemplate plugin..."
	@docker compose exec facturascripts sh -c "cd /var/www/html && php84 index.php"
	@echo "Plugin enabled! Access FacturaScripts at http://localhost:8080"
	@echo "Login with admin/admin"

# Rebuild FacturaScripts dynamic classes
rebuild: check-docker
	@echo "Rebuilding FacturaScripts..."
	@docker compose exec facturascripts sh -c "curl -s http://localhost:8080/deploy?action=rebuild > /dev/null"
	@echo "Rebuild complete!"

# Run unit tests (requires local FacturaScripts with testing tools)
test:
	@echo ""
	@echo "╔════════════════════════════════════════════════════════════════╗"
	@echo "║                    UNIT TESTING INFORMATION                     ║"
	@echo "╚════════════════════════════════════════════════════════════════╝"
	@echo ""
	@echo "⚠️  Local testing requires a full FacturaScripts development setup."
	@echo ""
	@echo "📋 Recommended approach:"
	@echo "   • Push your code to GitHub"
	@echo "   • Tests will run automatically via GitHub Actions"
	@echo "   • View results in the 'Actions' tab"
	@echo ""
	@echo "🔧 Advanced: Local testing with FacturaScripts:"
	@echo "   1. Clone FacturaScripts: git clone https://github.com/NeoRazorX/facturascripts.git"
	@echo "   2. Install dependencies: cd facturascripts && composer install"
	@echo "   3. Copy plugin: cp -r /path/to/PluginTemplate Plugins/"
	@echo "   4. Copy tests: cp -r Plugins/PluginTemplate/Test/main/* Test/Plugins/"
	@echo "   5. Run tests: php Test/install-plugins.php && vendor/bin/phpunit -c phpunit-plugins.xml"
	@echo ""
	@echo "📚 Documentation: See README.md for more details"
	@echo ""

# View logs
logs:
	docker compose logs -f --tail=200

# Show container status
ps:
	docker compose ps

# Fresh start (clean and start)
fresh: clean upd

# Display help with available commands
help:
	@echo ""
	@echo "Usage: make <command>"
	@echo ""
	@echo "Docker management:"
	@echo "  up                - Start Docker containers in interactive mode"
	@echo "  upd               - Start Docker containers in background mode (detached)"
	@echo "  down              - Stop and remove Docker containers"
	@echo "  logs              - Tail container logs"
	@echo "  ps                - Show container status"
	@echo "  build             - Build or rebuild Docker containers"
	@echo "  pull              - Pull the latest images from the registry"
	@echo "  clean             - Stop containers and remove volumes and orphans"
	@echo "  fresh             - Clean volumes and start again (fresh DB)"
	@echo "  shell             - Open a shell inside the facturascripts container"
	@echo ""
	@echo "Plugin management:"
	@echo "  enable-plugin     - Enable the plugin in FacturaScripts"
	@echo "  rebuild           - Rebuild FacturaScripts dynamic classes"
	@echo ""
	@echo "Testing:"
	@echo "  test              - Show unit testing information"
	@echo ""
	@echo "Packaging:"
	@echo "  package           - Generate a .zip package of the plugin with version tag"
	@echo "                      Usage: make package VERSION=1.2.3"
	@echo ""
	@echo "Other:"
	@echo "  help              - Show this help message"
	@echo ""

# Set help as the default goal if no target is specified
.DEFAULT_GOAL := help
