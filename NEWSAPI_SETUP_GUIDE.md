# 📰 NewsAPI Setup Guide

## Overview
This guide will help you set up NewsAPI integration to display live badminton news on your SmashZone dashboard.

## 🔑 **Step 1: Get NewsAPI Key**

1. **Visit NewsAPI**: Go to [https://newsapi.org/](https://newsapi.org/)
2. **Sign Up**: Create a free account
3. **Get API Key**: Copy your API key from the dashboard
4. **Free Tier**: 1000 requests per day (perfect for testing)

## ⚙️ **Step 2: Configure Environment**

Add your NewsAPI key to the `.env` file:

```env
# NewsAPI Configuration
NEWSAPI_KEY=your_api_key_here
```

## 🚀 **Step 3: Test the Integration**

1. **Visit Dashboard**: Go to `http://127.0.0.1:8002/dashboard`
2. **Check Status**: Look for the news status indicator:
   - 🟢 **Live**: NewsAPI is working correctly
   - 🟡 **Demo Mode**: No API key configured (shows fallback content)
   - 🔴 **Offline**: API error or connection issue

## 📊 **Features Implemented**

### ✅ **Live News Display**
- **Real-time Articles**: Latest badminton news from NewsAPI
- **Dynamic Content**: Updates automatically with new articles
- **Image Support**: Displays article images when available
- **Click to Read**: Click articles to open in new tab

### ✅ **Smart Fallback**
- **Demo Mode**: Shows sample content when API key not configured
- **Error Handling**: Graceful fallback when API is unavailable
- **Caching**: 5-minute cache to reduce API calls

### ✅ **Visual Design**
- **Status Indicator**: Shows API connection status
- **Responsive Layout**: Works on all device sizes
- **Hover Effects**: Interactive article cards
- **Featured Article**: Highlights the most recent news

## 🎯 **News Sources**

The integration searches for:
- **Badminton-specific news**: Tournaments, championships, world events
- **Sports news**: General sports updates
- **Equipment news**: Racket and gear updates
- **Training content**: Tips and techniques

## 🔧 **Configuration Options**

### **Customize News Sources**
Edit `app/Services/NewsApiService.php`:

```php
// Change search terms
'q' => 'badminton OR "badminton tournament" OR "badminton championship"',

// Change language
'language' => 'en', // or 'ms' for Malay

// Change number of articles
$badmintonNews = $this->newsService->getBadmintonNews(6); // 6 articles
```

### **Adjust Cache Duration**
```php
// In NewsApiService.php
return Cache::remember($cacheKey, 300, function () use ($limit) { // 5 minutes
    // Change 300 to desired seconds (e.g., 600 for 10 minutes)
});
```

## 📱 **Dashboard Integration**

### **News Section Location**
- **Position**: Bottom of dashboard page
- **Visibility**: All user types (owner, staff, customer)
- **Responsive**: Adapts to screen size

### **Article Display**
- **Grid Layout**: 3 columns on desktop, responsive on mobile
- **Article Cards**: Title, description, source, publish date
- **Images**: Article thumbnails when available
- **External Links**: Click to read full articles

## 🧪 **Testing**

### **Test Scenarios**
1. **With API Key**: Should show live news articles
2. **Without API Key**: Should show demo content
3. **API Error**: Should show fallback content
4. **No Internet**: Should show cached content or fallback

### **Expected Results**
- ✅ **Live News**: Real articles from NewsAPI
- ✅ **Images**: Article thumbnails display correctly
- ✅ **Links**: Clicking opens articles in new tab
- ✅ **Status**: Correct status indicator displayed
- ✅ **Responsive**: Works on mobile and desktop

## 🔍 **Troubleshooting**

### **Common Issues**

#### **"Demo Mode" Status**
- **Cause**: No API key configured
- **Fix**: Add `NEWSAPI_KEY=your_key` to `.env` file

#### **"Offline" Status**
- **Cause**: API key invalid or network issue
- **Fix**: Check API key and internet connection

#### **No Articles Displayed**
- **Cause**: API quota exceeded or search terms too specific
- **Fix**: Check API usage or broaden search terms

#### **Images Not Loading**
- **Cause**: CORS issues or broken image URLs
- **Fix**: This is normal - fallback icons will display

### **Debug Steps**
1. Check `.env` file has correct API key
2. Run `php artisan config:clear`
3. Check Laravel logs for errors
4. Test API key directly with curl

## 📈 **Performance Optimization**

### **Caching Strategy**
- **5-minute cache**: Reduces API calls
- **Fallback content**: Always available
- **Error handling**: Graceful degradation

### **API Usage**
- **Free tier**: 1000 requests/day
- **Cached requests**: Don't count against limit
- **Smart caching**: Only fetches when needed

## 🎨 **Customization**

### **Change News Topics**
```php
// In NewsApiService.php
'q' => 'badminton OR "badminton tournament" OR "badminton championship" OR "badminton world"',
```

### **Modify Display Count**
```php
// In DashboardController.php
$badmintonNews = $this->newsService->getBadmintonNews(6); // Change number
```

### **Update Styling**
Edit the news section in `resources/views/dashboard.blade.php`

## 🚀 **Production Deployment**

### **Environment Variables**
```env
NEWSAPI_KEY=your_production_api_key
```

### **Cache Configuration**
```php
// Use Redis or database cache for production
CACHE_DRIVER=redis
```

### **Error Monitoring**
- Monitor API usage and errors
- Set up alerts for API failures
- Track cache hit rates

## 📊 **Analytics**

### **Track Usage**
- Monitor API request counts
- Track article click-through rates
- Analyze user engagement

### **Optimize Content**
- Adjust search terms based on popular articles
- Modify cache duration based on usage patterns
- A/B test different news layouts

---

**Status**: ✅ **READY FOR SETUP**  
**API Required**: NewsAPI (Free tier available)  
**Setup Time**: 5-10 minutes  
**Documentation**: Complete
