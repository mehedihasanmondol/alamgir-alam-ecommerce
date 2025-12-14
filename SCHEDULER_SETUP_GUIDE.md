# Laravel Scheduler Setup Guide

## Overview

This guide explains how to set up Laravel's task scheduler on your server to automatically run the YouTube comment import daily.

Laravel's scheduler allows you to run scheduled tasks (like importing YouTube comments) at specified intervals. The scheduler only requires a single cron entry on your server.

---

## Windows Setup (Task Scheduler)

### Step 1: Open Task Scheduler

1. Press `Win + R` to open the Run dialog
2. Type `taskschd.msc` and press Enter
3. Task Scheduler will open

### Step 2: Create a New Basic Task

1. In the Task Scheduler, click **"Create Basic Task"** in the right panel
2. Give it a name: `Laravel Scheduler`
3. Add a description (optional): `Runs Laravel scheduled tasks including YouTube comment import`
4. Click **Next**

### Step 3: Set the Trigger

1. Select **Daily**
2. Click **Next**
3. Set the start date and time (e.g., today at 00:00)
4. Set recur every **1 day**
5. Click **Next**

### Step 4: Set the Action

1. Select **Start a program**
2. Click **Next**
3. **Program/script**: `php`
4. **Add arguments**: `"C:\path\to\your\project\artisan" schedule:run`
   - Replace `C:\path\to\your\project\` with your actual project path
   - Example: `"C:\Users\Love Station\Documents\alom vai\website\alamgir-alam-ecommerce\artisan" schedule:run`
5. **Start in**: `C:\path\to\your\project`
   - Example: `C:\Users\Love Station\Documents\alom vai\website\alamgir-alam-ecommerce`
6. Click **Next**

### Step 5: Advanced Settings (Important!)

1. After finishing the wizard, right-click the task and select **Properties**
2. Go to the **Triggers** tab
3. Edit the trigger and click **Advanced settings**
4. Check **"Repeat task every"**: Select **1 minute**
5. **For a duration of**: Select **Indefinitely**
6. Click **OK**

### Step 6: Additional Settings

1. In the **General** tab:
   - Select **"Run whether user is logged on or not"**
   - Check **"Run with highest privileges"**
2. In the **Conditions** tab:
   - Uncheck **"Start the task only if the computer is on AC power"**
3. Click **OK** and enter your Windows password if prompted

---

## Linux Setup (Cron)

### Step 1: Open Crontab

```bash
crontab -e
```

### Step 2: Add the Schedule Entry

Add this line at the end of the file:

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

**Replace** `/path-to-your-project` with your actual project path.

Example:
```bash
* * * * * cd /var/www/alamgir-alam-ecommerce && php artisan schedule:run >> /dev/null 2>&1
```

### Step 3: Save and Exit

- If using `nano`: Press `Ctrl+X`, then `Y`, then `Enter`
- If using `vim`: Press `Esc`, type `:wq`, press `Enter`

---

## Verify the Scheduler is Working

### Test Manually

Run this command to manually trigger the scheduler:

```bash
php artisan schedule:run
```

You should see output indicating which tasks ran.

### Test the YouTube Import Command

Run the YouTube import manually to verify it works:

```bash
php artisan youtube:import-comments
```

This will:
1. Test the YouTube API connection
2. Fetch comments from your channel
3. Import them as feedback
4. Display statistics

### Check Logs

Laravel logs scheduler activities. Check the log file:

**Windows**: `storage\logs\laravel.log`  
**Linux**: `storage/logs/laravel.log`

---

## Scheduler Configuration

The YouTube import is configured to run **daily** in `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('youtube:import-comments')->daily();
}
```

### Customizing the Schedule

You can change when the import runs by modifying the schedule:

```php
// Run daily at 2am
$schedule->command('youtube:import-comments')->dailyAt('02:00');

// Run every 6 hours
$schedule->command('youtube:import-comments')->everySixHours();

// Run weekly on Sundays at 3am
$schedule->command('youtube:import-comments')->weekly()->sundays()->at('03:00');
```

---

## Troubleshooting

### Scheduler Not Running

1. **Check cron/task is active**: Windows Task Scheduler should show "Running" status
2. **Check PHP path**: Ensure `php` command is in your PATH
3. **Check permissions**: Ensure the web server user has write access to `storage/logs`

### YouTube Import Not Working

1. **Check API credentials**: Go to Site Settings > Feedback Sites and verify your YouTube API key and Channel ID
2. **Enable import**: Ensure "Enable YouTube Import" is checked in Site Settings
3. **Test connection**: Use the "Test Connection" button in Site Settings
4. **Check logs**: Review `storage/logs/laravel.log` for errors

### Manual Testing

You can always run the import manually for testing:

```bash
php artisan youtube:import-comments --force
```

The `--force` flag bypasses the "enabled" check in settings.

---

## Need Help?

Check the Laravel documentation on task scheduling:
https://laravel.com/docs/10.x/scheduling
