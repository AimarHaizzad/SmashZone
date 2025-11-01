#!/bin/bash

# ☁️ SmashZone Azure Deployment Script
# This script deploys your Laravel app to Azure using the FREE tier

echo "☁️ SmashZone Azure Deployment Script"
echo "===================================="

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
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

print_azure() {
    echo -e "${PURPLE}[AZURE]${NC} $1"
}

# Check if Azure CLI is installed
if ! command -v az &> /dev/null; then
    print_error "Azure CLI is not installed. Please install it first:"
    echo "https://docs.microsoft.com/en-us/cli/azure/install-azure-cli"
    exit 1
fi

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    print_error "Please run this script from the Laravel project root directory"
    exit 1
fi

# Variables
RESOURCE_GROUP="smashzone-rg"
APP_NAME="smashzone-app-$(date +%s)"  # Add timestamp to make unique
SQL_SERVER="smashzone-sql-$(date +%s)"
DATABASE_NAME="smashzone_db"
LOCATION="Southeast Asia"
ADMIN_USER="smashzoneadmin"
ADMIN_PASSWORD="SmashZone2024!"

print_azure "Starting Azure deployment for SmashZone..."

# Step 1: Login to Azure
print_status "Logging into Azure..."
az login
if [ $? -eq 0 ]; then
    print_success "Successfully logged into Azure"
else
    print_error "Failed to login to Azure"
    exit 1
fi

# Step 2: Create Resource Group
print_status "Creating resource group: $RESOURCE_GROUP"
az group create --name $RESOURCE_GROUP --location "$LOCATION" --output none
if [ $? -eq 0 ]; then
    print_success "Resource group created successfully"
else
    print_error "Failed to create resource group"
    exit 1
fi

# Step 3: Create Azure SQL Server (Logical Server)
print_status "Creating Azure SQL Server: $SQL_SERVER"
az sql server create \
  --resource-group $RESOURCE_GROUP \
  --name $SQL_SERVER \
  --location "$LOCATION" \
  --admin-user $ADMIN_USER \
  --admin-password "$ADMIN_PASSWORD" \
  --output none

if [ $? -eq 0 ]; then
    print_success "Azure SQL Server created successfully"
else
    print_error "Failed to create Azure SQL Server"
    exit 1
fi

# Step 4: Create Azure SQL Database (Basic Tier - Low Cost)
print_status "Creating Azure SQL Database (Basic tier - low cost): $DATABASE_NAME"
az sql db create \
  --resource-group $RESOURCE_GROUP \
  --server $SQL_SERVER \
  --name $DATABASE_NAME \
  --service-objective Basic \
  --backup-storage-redundancy Local \
  --yes \
  --output none

if [ $? -eq 0 ]; then
    print_success "Azure SQL Database created successfully (Basic tier - ~$5/month)"
else
    print_error "Failed to create database"
    exit 1
fi

# Step 5: Configure Firewall Rules
print_status "Configuring SQL Server firewall rules..."
az sql server firewall-rule create \
  --resource-group $RESOURCE_GROUP \
  --server $SQL_SERVER \
  --name AllowAzureServices \
  --start-ip-address 0.0.0.0 \
  --end-ip-address 0.0.0.0 \
  --output none

if [ $? -eq 0 ]; then
    print_success "Firewall rules configured"
else
    print_warning "Failed to configure firewall rules"
fi

# Step 6: Create App Service Plan
print_status "Creating App Service plan..."
az appservice plan create \
  --name smashzone-plan \
  --resource-group $RESOURCE_GROUP \
  --location "$LOCATION" \
  --is-linux \
  --sku F1 \
  --output none

if [ $? -eq 0 ]; then
    print_success "App Service plan created successfully"
else
    print_error "Failed to create App Service plan"
    exit 1
fi

# Step 7: Create Web App
print_status "Creating web app: $APP_NAME"
az webapp create \
  --resource-group $RESOURCE_GROUP \
  --plan smashzone-plan \
  --name $APP_NAME \
  --runtime "PHP|8.2" \
  --output none

if [ $? -eq 0 ]; then
    print_success "Web app created successfully"
else
    print_error "Failed to create web app"
    exit 1
fi

# Step 8: Configure App Settings
print_status "Configuring application settings..."

# Generate a random app key
APP_KEY="base64:$(openssl rand -base64 32)"

az webapp config appsettings set \
  --resource-group $RESOURCE_GROUP \
  --name $APP_NAME \
  --settings \
    APP_ENV=production \
    APP_DEBUG=false \
    APP_KEY="$APP_KEY" \
    APP_URL="https://$APP_NAME.azurewebsites.net" \
    DB_CONNECTION=sqlsrv \
    DB_HOST="$SQL_SERVER.database.windows.net" \
    DB_PORT=1433 \
    DB_DATABASE=$DATABASE_NAME \
    DB_USERNAME="$ADMIN_USER" \
    DB_PASSWORD="$ADMIN_PASSWORD" \
    DB_ENCRYPT=yes \
    DB_TRUST_SERVER_CERTIFICATE=false \
    MAIL_MAILER=smtp \
    MAIL_HOST=smtp.gmail.com \
    MAIL_PORT=587 \
    MAIL_USERNAME="your_email@gmail.com" \
    MAIL_PASSWORD="your_app_password" \
    MAIL_ENCRYPTION=tls \
    MAIL_FROM_ADDRESS="noreply@$APP_NAME.azurewebsites.net" \
    MAIL_FROM_NAME="SmashZone" \
    FIREBASE_PROJECT_ID="your_firebase_project_id" \
    FIREBASE_PRIVATE_KEY_ID="your_private_key_id" \
    FIREBASE_PRIVATE_KEY="-----BEGIN PRIVATE KEY-----\nYOUR_PRIVATE_KEY\n-----END PRIVATE KEY-----\n" \
    FIREBASE_CLIENT_EMAIL="your_service_account_email" \
    FIREBASE_CLIENT_ID="your_client_id" \
    FIREBASE_AUTH_URI="https://accounts.google.com/o/oauth2/auth" \
    FIREBASE_TOKEN_URI="https://oauth2.googleapis.com/token" \
    FIREBASE_AUTH_PROVIDER_X509_CERT_URL="https://www.googleapis.com/oauth2/v1/certs" \
    FIREBASE_CLIENT_X509_CERT_URL="your_cert_url" \
    NEWS_API_KEY="your_news_api_key" \
  --output none

if [ $? -eq 0 ]; then
    print_success "Application settings configured"
else
    print_warning "Some settings may not have been configured properly"
fi

# Step 9: Enable SSH
print_status "Enabling SSH for the web app..."
az webapp ssh enable --resource-group $RESOURCE_GROUP --name $APP_NAME --output none
if [ $? -eq 0 ]; then
    print_success "SSH enabled successfully"
else
    print_warning "Failed to enable SSH"
fi

# Step 10: Prepare deployment package
print_status "Preparing deployment package..."

# Create .deployment file for Azure
cat > .deployment << EOF
[config]
SCM_DO_BUILD_DURING_DEPLOYMENT=true
ENABLE_ORYX_BUILD=true
EOF

# Create composer.json for Azure (if not exists)
if [ ! -f "composer.json" ]; then
    print_error "composer.json not found. Please ensure you're in the Laravel project directory."
    exit 1
fi

# Create .htaccess for Azure
cat > public/.htaccess << 'EOF'
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
EOF

# Step 11: Deploy Code
print_status "Deploying code to Azure..."

# Initialize git if not already done
if [ ! -d ".git" ]; then
    git init
    git add .
    git commit -m "Initial commit for Azure deployment"
fi

# Configure deployment source
az webapp deployment source config-local-git \
  --resource-group $RESOURCE_GROUP \
  --name $APP_NAME \
  --output none

# Get deployment URL
DEPLOYMENT_URL=$(az webapp deployment source show \
  --resource-group $RESOURCE_GROUP \
  --name $APP_NAME \
  --query url --output tsv)

if [ $? -eq 0 ] && [ ! -z "$DEPLOYMENT_URL" ]; then
    print_success "Deployment URL configured: $DEPLOYMENT_URL"
    
    # Add Azure remote
    git remote remove azure 2>/dev/null || true
    git remote add azure $DEPLOYMENT_URL
    
    # Push to Azure
    print_status "Pushing code to Azure..."
    git push azure main --force
    
    if [ $? -eq 0 ]; then
        print_success "Code deployed successfully!"
    else
        print_warning "Code deployment may have issues. You can try manual deployment."
    fi
else
    print_warning "Failed to get deployment URL. You may need to deploy manually."
fi

# Step 12: Display Results
echo ""
echo "🎉 Azure Deployment Complete!"
echo "=============================="
echo ""
print_success "Your SmashZone app is now deployed to Azure!"
echo ""
echo "📋 Deployment Details:"
echo "  🌐 App URL: https://$APP_NAME.azurewebsites.net"
echo "  📊 Resource Group: $RESOURCE_GROUP"
echo "  🗄️  SQL Server: $SQL_SERVER.database.windows.net"
echo "  💾 Database: $DATABASE_NAME (Basic tier - ~$5/month)"
echo "  👤 Admin User: $ADMIN_USER"
echo "  🔑 Admin Password: $ADMIN_PASSWORD"
echo ""
echo "📋 Next Steps:"
echo "1. 🌐 Visit your app: https://$APP_NAME.azurewebsites.net"
echo "2. 🔧 SSH into your app:"
echo "   az webapp ssh --resource-group $RESOURCE_GROUP --name $APP_NAME"
echo "3. 🗄️  Run database migrations:"
echo "   php artisan migrate --force"
echo "4. 🔗 Create storage link:"
echo "   php artisan storage:link"
echo "5. ⚡ Optimize for production:"
echo "   php artisan config:cache"
echo "   php artisan route:cache"
echo "   php artisan view:cache"
echo ""
echo "🔧 Manual Database Setup (if needed):"
echo "   az sql server firewall-rule create \\"
echo "     --resource-group $RESOURCE_GROUP \\"
echo "     --server $SQL_SERVER \\"
echo "     --name AllowMyIP \\"
echo "     --start-ip-address YOUR_IP \\"
echo "     --end-ip-address YOUR_IP"
echo ""
echo "📚 For detailed instructions, see: AZURE_DEPLOYMENT_GUIDE.md"
echo ""
print_success "SmashZone is now live on Azure! 🚀"
