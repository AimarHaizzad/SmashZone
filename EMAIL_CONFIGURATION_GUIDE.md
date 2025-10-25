# 📧 Email Configuration Guide for SmashZone

## 🚨 Current Issue
Your emails are being **logged to files** instead of being sent because `MAIL_MAILER=log` in your `.env` file.

## ✅ Solution: Configure SMTP

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

### Option 3: Yahoo SMTP

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

#### 2. "Connection refused"
- Check your internet connection
- Verify SMTP host and port settings
- Try different port (465 with SSL instead of 587 with TLS)

#### 3. "Emails not received"
- Check spam/junk folder
- Verify email address is correct
- Check if email provider is blocking emails

#### 4. "Still getting logged emails"
- Run `php artisan config:clear`
- Restart your development server
- Double-check `.env` file syntax

## 📋 Quick Setup Checklist

- [ ] Choose email provider (Gmail recommended)
- [ ] Enable 2FA on email account
- [ ] Generate App Password
- [ ] Update `.env` file with SMTP settings
- [ ] Run `php artisan config:clear`
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
