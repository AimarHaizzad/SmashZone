# 🗄️ How to Create a Database on Render

## Step-by-Step Guide

### Option 1: PostgreSQL (Recommended - Free Tier Available)

1. **Go to your Render Dashboard**
   - Navigate to your project: `smashzone-ywoa`

2. **Click "+ New" button** (top right)
   - Select **"PostgreSQL"** from the dropdown

3. **Configure PostgreSQL Database**
   - **Name**: `smashzone-db` (or any name you prefer)
   - **Database**: Leave default (or name it `smashzone`)
   - **User**: Leave default
   - **Region**: Choose closest to your web service
   - **PostgreSQL Version**: Latest (15 or 16)
   - **Plan**: 
     - **Free**: For development/testing (spins down after inactivity)
     - **Starter ($7/month)**: For production (always on)

4. **Click "Create Database"**
   - Render will automatically create the database
   - **Important**: Render automatically provides connection variables to your web service!

5. **Get Database Connection Details**
   - After creating the database, click on it in your dashboard
   - Go to the **"Info"** or **"Connections"** tab
   - You'll see connection details like:
     - **Internal Database URL**: `postgresql://user:password@host:port/database`
     - **Host**: `dpg-xxxxx-a.singapore-postgres.render.com`
     - **Port**: `5432`
     - **Database Name**: `smashzone` (or the name you chose)
     - **User**: `smashzone_user` (or auto-generated)
     - **Password**: (shown in the dashboard)

6. **Add Connection Variables Manually** (if not auto-added)
   - Go to your **Web Service** → **Environment** tab
   - Click **"+ Add Environment Variable"**
   - Add these variables one by one:

### Option 2: MySQL (External Service Required)

If you prefer MySQL, you'll need to use an external MySQL service:

1. **Use a MySQL hosting service** (e.g., PlanetScale, Aiven, or Railway MySQL)
2. **Add connection variables manually** in Render:
   ```
   DB_CONNECTION=mysql
   DB_HOST=your-mysql-host.com
   DB_PORT=3306
   DB_DATABASE=your_database_name
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

## ✅ After Creating Database

1. **Redeploy your web service**
   - Render will automatically connect the database
   - The updated `render-start.sh` script will detect and configure it

2. **Check deployment logs**
   - You should see: `✅ Detected Render PostgreSQL (DATABASE_URL)`
   - Migrations should run automatically

3. **Verify connection**
   - Visit your app URL
   - The database connection error should be gone!

## 🔧 Manual Environment Variables Setup

### Step-by-Step: Add Database Variables Manually

1. **Go to your PostgreSQL database dashboard**
   - Click on your database (e.g., `smashzone-db`)
   - Go to **"Info"** or **"Connections"** tab
   - Copy the connection details

2. **Go to your Web Service**
   - Click on your web service (e.g., `smashzone-ywoa`)
   - Go to **"Environment"** tab
   - Click **"+ Add Environment Variable"**

3. **Add these variables one by one:**

   **Option A: Using DATABASE_URL (Easier)**
   ```
   Key: DATABASE_URL
   Value: postgresql://username:password@host:port/database
   ```
   Example:
   ```
   DATABASE_URL=postgresql://smashzone_user:abc123xyz@dpg-xxxxx-a.singapore-postgres.render.com:5432/smashzone
   ```

   **Option B: Using Individual Variables (More Control)**
   ```
   Key: DB_CONNECTION
   Value: pgsql
   
   Key: DB_HOST
   Value: dpg-xxxxx-a.singapore-postgres.render.com
   
   Key: DB_PORT
   Value: 5432
   
   Key: DB_DATABASE
   Value: smashzone
   
   Key: DB_USERNAME
   Value: smashzone_user
   
   Key: DB_PASSWORD
   Value: your_password_here
   ```

4. **Save and Redeploy**
   - Click **"Save Changes"**
   - Your service will automatically redeploy
   - The `render-start.sh` script will detect and configure the database

### For PostgreSQL (Individual Variables):
```
DB_CONNECTION=pgsql
DB_HOST=your-postgres-host.onrender.com
DB_PORT=5432
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### For MySQL:
```
DB_CONNECTION=mysql
DB_HOST=your-mysql-host.com
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

## 📝 Notes

- **Free PostgreSQL**: Spins down after 90 days of inactivity (data preserved)
- **Starter PostgreSQL ($7/month)**: Always on, better for production
- Render automatically provides `DATABASE_URL` when you create a PostgreSQL database
- The updated `render-start.sh` script will automatically parse and configure it

