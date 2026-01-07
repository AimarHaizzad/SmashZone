# Cloudinary Setup Guide

## Overview
This application now uses Cloudinary for image storage instead of local storage. This ensures images persist on Render and other cloud hosting platforms.

## Setup Instructions

### 1. Create a Cloudinary Account
1. Go to [https://cloudinary.com](https://cloudinary.com)
2. Sign up for a free account (free tier includes 25GB storage and 25GB bandwidth)
3. After signing up, you'll be taken to your dashboard

### 2. Get Your Cloudinary Credentials
1. In your Cloudinary dashboard, go to **Settings** (gear icon)
2. Click on **Product Environment Credentials**
3. You'll see:
   - **Cloud Name** (e.g., `your-cloud-name`)
   - **API Key** (e.g., `123456789012345`)
   - **API Secret** (e.g., `abcdefghijklmnopqrstuvwxyz123456`)

### 3. Add Credentials to Your .env File

You can use either format:

**Option 1: CLOUDINARY_URL (Recommended - Single Line)**
```env
CLOUDINARY_URL=cloudinary://626952429977852:1Gq6S8qHAz_kUyXBj5I3U26g9Z0@dcyonxvey
```

**Option 2: Individual Variables**
```env
CLOUDINARY_CLOUD_NAME=dcyonxvey
CLOUDINARY_API_KEY=626952429977852
CLOUDINARY_API_SECRET=1Gq6S8qHAz_kUyXBj5I3U26g9Z0
```

**Note**: The application supports both formats. Using `CLOUDINARY_URL` is simpler and matches Cloudinary's standard format.

### 4. For Render Deployment
1. Go to your Render dashboard: [https://dashboard.render.com](https://dashboard.render.com)
2. Select your SmashZone service
3. Go to **Environment** tab
4. Add this environment variable:
   - **Key**: `CLOUDINARY_URL`
   - **Value**: `cloudinary://626952429977852:1Gq6S8qHAz_kUyXBj5I3U26g9Z0@dcyonxvey`
5. Click **Save Changes**
6. Your service will automatically redeploy

**Alternative**: You can also add the three individual variables if you prefer:
   - `CLOUDINARY_CLOUD_NAME` = `dcyonxvey`
   - `CLOUDINARY_API_KEY` = `626952429977852`
   - `CLOUDINARY_API_SECRET` = `1Gq6S8qHAz_kUyXBj5I3U26g9Z0`

## How It Works

### Image Upload
- When owners upload product or court images, they are automatically uploaded to Cloudinary
- Images are stored in folders: `products/` and `courts/`
- The Cloudinary secure URL is saved in the database

### Image Display
- The application automatically uses Cloudinary URLs when displaying images
- Old local storage images will still work (backward compatible)
- New uploads will use Cloudinary

### Image Deletion
- When images are deleted, they are automatically removed from Cloudinary
- This helps manage your Cloudinary storage quota

## Testing

After setup, test by:
1. Logging in as an Owner
2. Creating a new product or court with an image
3. Verifying the image displays correctly
4. Checking that the image URL in the database is a Cloudinary URL (starts with `https://res.cloudinary.com`)

## Troubleshooting

### Images Not Showing
1. Check that your Cloudinary credentials are correct in `.env`
2. Verify the credentials are set in Render environment variables
3. Check the Laravel logs for Cloudinary errors
4. Ensure your Cloudinary account is active

### Upload Fails
1. Check file size (max 10MB per image)
2. Verify file is a valid image format (jpg, png, gif, webp)
3. Check Cloudinary dashboard for upload errors
4. Review Laravel logs for detailed error messages

### Old Images Not Showing
- Old images stored locally will still work if they exist
- To migrate old images to Cloudinary, you would need to re-upload them
- New uploads automatically go to Cloudinary

## Cloudinary Free Tier Limits
- **Storage**: 25GB
- **Bandwidth**: 25GB/month
- **Transformations**: Unlimited
- **Uploads**: 25GB/month

For most small to medium applications, the free tier is sufficient.

## Support
For Cloudinary-specific issues, visit: [https://cloudinary.com/documentation](https://cloudinary.com/documentation)

