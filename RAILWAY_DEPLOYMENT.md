# 🚂 Railway Deployment Guide for SmashZone

This guide will help you deploy your Laravel SmashZone application to Railway.

## 📋 Prerequisites

1. **Railway Account**: Sign up at [railway.app](https://railway.app)
2. **GitHub Repository**: Your code should be in a GitHub repository
3. **Database**: Railway MySQL service (or external database)

## 🚀 Step-by-Step Deployment

### Step 1: Prepare Your Repository

1. **Commit the deployment files** (if not already committed):
   ```bash
   git add Dockerfile railway.json railway-start.sh .dockerignore RAILWAY_DEPLOYMENT.md
   git commit -m "Add Railway deployment configuration"
   git push
   ```

### Alternative: Using PHP Built-in Server

If you prefer a lighter setup, you can use PHP's built-in server instead of Apache. Simply:
1. Replace `railway-start.sh` with `railway-start-php.sh` in the Dockerfile
2. Or modify the Dockerfile to use the PHP server version

### Step 2: Create a New Project on Railway

1. Go to [railway.app](https://railway.app) and sign in
2. Click **"New Project"**
3. Select **"Deploy from GitHub repo"**
4. Choose your SmashZone repository
5. Select the branch you want to deploy (usually `main` or `master`)

### Step 3: Add MySQL Database

1. In your Railway project, click **"+ New"**
2. Select **"Database"** → **"Add MySQL"**
3. Railway will automatically create a MySQL database
4. Note the connection details (they'll be available as environment variables)

### Step 4: Configure Environment Variables

In your Railway project, go to **Variables** tab and add the following:

#### Required Variables:

```env
APP_NAME=SmashZone
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_URL=https://your-app-name.up.railway.app

# Database (Railway automatically provides these, but you can override)
# IMPORTANT: DB_CONNECTION must be set to 'mysql' - Railway defaults might not set this
DB_CONNECTION=mysql
DB_HOST=${{MySQL.MYSQLHOST}}
DB_PORT=${{MySQL.MYSQLPORT}}
DB_DATABASE=${{MySQL.MYSQLDATABASE}}
DB_USERNAME=${{MySQL.MYSQLUSER}}
DB_PASSWORD=${{MySQL.MYSQLPASSWORD}}

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@smashzone.com
MAIL_FROM_NAME="SmashZone"

# Firebase Configuration
FIREBASE_PROJECT_ID=your_firebase_project_id
FIREBASE_PRIVATE_KEY_ID=your_private_key_id
FIREBASE_PRIVATE_KEY="-----BEGIN PRIVATE KEY-----\nYOUR_PRIVATE_KEY\n-----END PRIVATE KEY-----\n"
FIREBASE_CLIENT_EMAIL=your_service_account_email
FIREBASE_CLIENT_ID=your_client_id
FIREBASE_AUTH_URI=https://accounts.google.com/o/oauth2/auth
FIREBASE_TOKEN_URI=https://oauth2.googleapis.com/token
FIREBASE_AUTH_PROVIDER_X509_CERT_URL=https://www.googleapis.com/oauth2/v1/certs
FIREBASE_CLIENT_X509_CERT_URL=your_cert_url

# NewsAPI
NEWS_API_KEY=your_news_api_key

# Session & Cache (use file driver for Railway)
SESSION_DRIVER=file
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
```

#### Important Notes:

- **APP_KEY**: Generate one using `php artisan key:generate --show` locally, or Railway will generate it automatically
- **Database Variables**: Railway automatically provides `${{MySQL.MYSQLHOST}}`, etc. when you add a MySQL service
- **APP_URL**: Update this after deployment with your Railway domain

### Step 5: Deploy

1. Railway will automatically detect the `Dockerfile` and start building
2. The build process will:
   - Install PHP dependencies via Composer
   - Install Node.js dependencies
   - Build frontend assets
   - Set up Apache web server

3. Once deployed, Railway will provide you with a domain like `your-app-name.up.railway.app`

### Step 6: Run Database Migrations

After the first deployment:

1. Go to your Railway project
2. Click on your service
3. Go to **"Deployments"** tab
4. Click on the latest deployment
5. Open the **"Logs"** tab to see if migrations ran successfully

If migrations didn't run automatically, you can run them manually:

1. Go to your service → **"Settings"** → **"Deploy"**
2. Add a one-time command: `php artisan migrate --force`
3. Or use Railway CLI:
   ```bash
   railway run php artisan migrate --force
   ```

### Step 7: Set Up Custom Domain (Optional)

1. In your Railway project, go to **"Settings"** → **"Networking"**
2. Click **"Generate Domain"** or **"Add Custom Domain"**
3. Update your `APP_URL` environment variable with the new domain

### Step 8: Configure Storage

The application uses Laravel's file storage. For production, consider:

1. **Use Railway Volume** for persistent storage:
   - Add a volume in Railway
   - Mount it to `/var/www/html/storage`

2. **Or use S3/Cloud Storage**:
   - Update `config/filesystems.php` to use S3
   - Add AWS credentials to environment variables

## 🔧 Post-Deployment Tasks

### 1. Verify Deployment

Visit your Railway domain and check:
- ✅ Application loads correctly
- ✅ Database connection works
- ✅ Static assets load (CSS, JS)
- ✅ File uploads work (if applicable)

### 2. Set Up Queue Workers (if needed)

If you use queues, add a new service:

1. Click **"+ New"** → **"Empty Service"**
2. Use the same Dockerfile
3. Set start command: `php artisan queue:work --sleep=3 --tries=3`
4. Add all the same environment variables

### 3. Set Up Scheduled Tasks

Railway doesn't support cron directly. Use one of these options:

**Option A: Use Railway Cron Jobs** (if available)
- Add a cron service that runs `php artisan schedule:run` every minute

**Option B: Use External Cron Service**
- Use services like [cron-job.org](https://cron-job.org) to hit your app's scheduler endpoint
- Add a route: `Route::get('/schedule-run', fn() => Artisan::call('schedule:run'))`

**Option C: Use Queue-based Scheduling**
- Convert scheduled tasks to queued jobs

## 🐛 Troubleshooting

### Build Fails

1. **Check logs** in Railway dashboard
2. **Common issues**:
   - Missing dependencies in `composer.json`
   - Node.js build errors
   - Dockerfile syntax errors

### Application Won't Start

1. **Check environment variables** are set correctly
2. **Verify database connection**:
   - Check MySQL service is running
   - Verify database credentials
3. **Check logs**: Railway → Service → Logs

### Database Connection Errors

1. **Verify MySQL service** is added and running
2. **Check environment variables** use Railway's template variables:
   - `${{MySQL.MYSQLHOST}}`
   - `${{MySQL.MYSQLPORT}}`
   - etc.
3. **Test connection** using Railway CLI:
   ```bash
   railway run php artisan tinker
   ```

### Static Assets Not Loading

1. **Verify build completed**: Check that `npm run build` ran successfully
2. **Check public/build directory** exists
3. **Verify APP_URL** is set correctly

### Storage Permissions

If file uploads fail:

1. **Check storage permissions** in logs
2. **Verify storage directory** is writable
3. **Consider using external storage** (S3) for production

## 📊 Monitoring

### View Logs

1. Go to your Railway project
2. Click on your service
3. Open **"Logs"** tab
4. Real-time logs are available

### Monitor Performance

- Railway provides basic metrics in the dashboard
- Consider adding Laravel Telescope (dev only) or monitoring services

## 🔄 Updating Your Application

1. **Push changes** to your GitHub repository
2. Railway will **automatically detect** and deploy
3. **Monitor deployment** in the Railway dashboard
4. **Check logs** if deployment fails

## 💰 Cost Considerations

Railway pricing:
- **Free tier**: $5 credit/month
- **Hobby**: $5/month + usage
- **Pro**: $20/month + usage

**Estimated costs**:
- Small app: ~$5-10/month
- Medium app: ~$10-20/month
- Large app: ~$20-50/month

## 🎯 Best Practices

1. **Environment Variables**: Never commit `.env` file
2. **Database Backups**: Set up regular backups
3. **Monitoring**: Monitor logs and errors
4. **Security**: Keep dependencies updated
5. **Performance**: Use caching (config, routes, views)
6. **Storage**: Use external storage for files (S3, etc.)

## 📞 Need Help?

- **Railway Docs**: [docs.railway.app](https://docs.railway.app)
- **Railway Discord**: [discord.gg/railway](https://discord.gg/railway)
- **Laravel Docs**: [laravel.com/docs](https://laravel.com/docs)

## ✅ Deployment Checklist

- [ ] Repository is on GitHub
- [ ] Dockerfile and railway.json are committed
- [ ] MySQL database service added
- [ ] All environment variables configured
- [ ] APP_KEY generated
- [ ] Database migrations run
- [ ] Storage link created
- [ ] Custom domain configured (optional)
- [ ] Application tested and working
- [ ] Monitoring set up

**Your SmashZone app is now deployed on Railway! 🎉**

