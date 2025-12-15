# 📧 Email Configuration Guide for SmashZone

## 🚨 Current Issue
Your emails are being **logged to files** instead of being sent because `MAIL_MAILER=log` in your `.env` file.

## ⚠️ **IMPORTANT: Render.com Limitations**
If you're deploying on **Render.com**, note that:
- ❌ **SMTP is BLOCKED**: Render blocks direct SMTP connections (ports 25, 587, 465)
- ✅ **Gmail API is ALLOWED**: You can use Gmail API via OAuth 2.0
- ✅ **Email APIs are ALLOWED**: Services like SendGrid, Mailgun, Resend work perfectly

**For Render deployments, use one of the API-based solutions below (Options 4-7).**

## ✅ Solution: Configure Email Sending

### For Local Development / Non-Render Hosting

## Option 1: Gmail SMTP (Local/Non-Render Only)

### Option 1: Gmail SMTP (Recommended)

#### Step 1: Prepare Gmail Account
1. **Use your Gmail account** or create a new one for SmashZone
2. **Enable 2-Factor Authentication**:
   - Go to Google Account settings
   - Security → 2-Step Verification → Turn on
3. **Create App Password**:
   - Go to Google Account settings
   - Security → App passwords
   - Generate password for "Mail"
   - **Save this password** (you'll need it)

#### Step 2: Update .env File
Replace your current mail settings in `.env` with:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-gmail@gmail.com
MAIL_PASSWORD=your-16-character-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-gmail@gmail.com
MAIL_FROM_NAME="SmashZone"
```

#### Step 3: Clear Configuration Cache
```bash
php artisan config:clear
```

### Option 2: Outlook SMTP

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp-mail.outlook.com
MAIL_PORT=587
MAIL_USERNAME=your-email@outlook.com
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@outlook.com
MAIL_FROM_NAME="SmashZone"
```

### Option 3: Yahoo SMTP (Local/Non-Render Only)

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mail.yahoo.com
MAIL_PORT=587
MAIL_USERNAME=your-email@yahoo.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@yahoo.com
MAIL_FROM_NAME="SmashZone"
```

---

## For Render.com Deployments (Recommended)

### Option 4: SendGrid (Recommended for Render) ⭐

SendGrid offers a free tier (100 emails/day) and works perfectly on Render.

#### Step 1: Create SendGrid Account
1. Sign up at [sendgrid.com](https://sendgrid.com)
2. Verify your account
3. Go to Settings → API Keys
4. Create a new API key with "Full Access" or "Mail Send" permissions
5. **Save the API key** (you'll only see it once!)

#### Step 2: Update .env File
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your-sendgrid-api-key-here
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="SmashZone"
```

### Option 5: Mailgun (Great for Render)

#### Step 1: Create Mailgun Account
1. Sign up at [mailgun.com](https://www.mailgun.com)
2. Verify your domain (or use sandbox domain for testing)
3. Go to Sending → Domain Settings → API Keys
4. Copy your API key

#### Step 2: Update .env File
```env
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=your-domain.com
MAILGUN_SECRET=your-mailgun-api-key
MAILGUN_ENDPOINT=api.mailgun.net
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="SmashZone"
```

### Option 6: Resend (Modern & Simple)

#### Step 1: Create Resend Account
1. Sign up at [resend.com](https://resend.com)
2. Verify your domain
3. Go to API Keys → Create API Key
4. Copy your API key

#### Step 2: Install Resend Package
```bash
composer require resend/resend-php
```

#### Step 3: Update .env File
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.resend.com
MAIL_PORT=587
MAIL_USERNAME=resend
MAIL_PASSWORD=your-resend-api-key
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="SmashZone"
```

### Option 7: Gmail API (For Render - Advanced)

If you want to use Gmail specifically on Render, you'll need to use Gmail API instead of SMTP.

#### Step 1: Setup Google Cloud Project
1. Go to [Google Cloud Console](https://console.cloud.google.com)
2. Create a new project
3. Enable Gmail API
4. Create OAuth 2.0 credentials
5. Download credentials JSON

#### Step 2: Install Google API Client
```bash
composer require google/apiclient
```

#### Step 3: Configure Gmail API
This requires custom code implementation. Consider using a package like `google/apiclient` or a Laravel package that wraps Gmail API.

**Note**: Gmail API setup is more complex. For Render, we recommend **SendGrid** or **Resend** for easier setup.

## 🧪 Testing Your Configuration

### Test 1: Run Email Test Script
```bash
php test_email_sending.php
```

### Test 2: Create a Test Staff Member
1. Go to `/staff` page
2. Click "Add New Staff"
3. Fill in the form with a real email address
4. Submit the form
5. Check the email inbox (and spam folder)

### Test 3: Check Laravel Logs
```bash
tail -f storage/logs/laravel.log
```

## 🔧 Troubleshooting

### Common Issues:

#### 1. "Authentication failed"
- **Gmail**: Make sure you're using App Password, not regular password
- **Outlook**: Check if 2FA is enabled
- **Yahoo**: Use App Password, not regular password

#### 2. "Connection refused" (On Render)
- **This is expected on Render!** Render blocks SMTP ports
- **Solution**: Use SendGrid, Mailgun, or Resend instead (API-based)
- If on local development, check your internet connection and SMTP settings

#### 3. "Emails not received"
- Check spam/junk folder
- Verify email address is correct
- Check if email provider is blocking emails

#### 4. "Still getting logged emails"
- Run `php artisan config:clear`
- Restart your development server
- Double-check `.env` file syntax

## 📋 Quick Setup Checklist

### For Local Development:
- [ ] Choose email provider (Gmail SMTP recommended)
- [ ] Enable 2FA on email account
- [ ] Generate App Password
- [ ] Update `.env` file with SMTP settings
- [ ] Run `php artisan config:clear`
- [ ] Test by creating a staff member
- [ ] Check email inbox and spam folder

### For Render.com:
- [ ] Choose API-based service (SendGrid recommended)
- [ ] Create account and get API key
- [ ] Update `.env` file with API settings
- [ ] Add environment variables to Render dashboard
- [ ] Run `php artisan config:clear` (via Render shell)
- [ ] Test by creating a staff member
- [ ] Check email inbox and spam folder

## 🎯 Expected Results

After proper configuration:
- ✅ Staff creation emails will be sent to real email addresses
- ✅ Staff members will receive account details via email
- ✅ No more emails logged to files
- ✅ Professional email notifications working

## 📞 Need Help?

If you're still having issues:
1. Check the Laravel logs: `storage/logs/laravel.log`
2. Verify your SMTP settings are correct
3. Test with a simple email first
4. Make sure your email provider allows SMTP access

---

**Status**: Ready for configuration  
**Difficulty**: Easy to Medium  
**Time Required**: 5-10 minutes
