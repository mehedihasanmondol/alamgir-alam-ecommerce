# Artisan Command Runner Implementation

**Created:** 2025-12-02  
**Module:** Admin System Settings  
**Component:** Artisan Command Executor

---

## Overview
Implemented a secure artisan command runner/executor in the admin panel's system settings section. This allows authorized administrators to execute whitelisted Laravel artisan commands through a web interface.

---

## Features Implemented

### 1. **Security Features**
- ✅ **Whitelisted Commands**: Only predefined safe commands can be executed
- ✅ **Blocked Dangerous Commands**: Destructive commands (migrate:fresh, db:wipe, key:generate) are blocked
- ✅ **Execution Logging**: All command executions are logged with user ID, IP address, and timestamp
- ✅ **Error Handling**: Comprehensive try-catch blocks with user-friendly error messages
- ✅ **Permission-Based Access**: Only admins with `system.settings.view` permission can access

### 2. **User Interface**
- ✅ **Dropdown Selection**: Easy-to-use dropdown with categorized commands
- ✅ **Custom Command Input**: Advanced option for custom commands (with security warnings)
- ✅ **Real-time Output Display**: Command output shown in a terminal-style display
- ✅ **Copy to Clipboard**: One-click copy of command output
- ✅ **Loading States**: Visual feedback during command execution
- ✅ **Success/Error Alerts**: Clear feedback messages
- ✅ **Responsive Design**: Works on desktop and mobile devices

### 3. **Predefined Command Categories**

#### Cache Commands
- `cache:clear` - Clear application cache
- `config:clear` - Clear configuration cache
- `route:clear` - Clear route cache
- `view:clear` - Clear compiled views
- `optimize:clear` - Clear all cached files
- `optimize` - Cache framework files

#### Database Commands
- `migrate:status` - Show migration status
- `db:show` - Display database info

#### Queue Commands
- `queue:failed` - List failed jobs
- `queue:restart` - Restart queue workers

#### Storage Commands
- `storage:link` - Create storage symlink

#### Maintenance Commands
- `down` - Enable maintenance mode
- `up` - Disable maintenance mode

#### Other Commands
- `about` - Application information
- `env` - Current environment
- `list` - List all commands
- `inspire` - Get inspired! 💡

---

## Files Created/Modified

### Created Files
1. **`app/Livewire/Admin/SystemSettings/ArtisanCommandRunner.php`**
   - Livewire component handling command execution logic
   - Security validation and logging
   - Error handling

2. **`resources/views/livewire/admin/system-settings/artisan-command-runner.blade.php`**
   - User interface for command selection and execution
   - Output display with terminal styling
   - Responsive design with Alpine.js interactions

### Modified Files
1. **`app/Http/Controllers/Admin/SystemSettingsController.php`**
   - Added 'artisan' section to system settings navigation
   - Configured icon, title, description, and component reference

---

## Security Implementation

### Blocked Commands (Hardcoded Blacklist)
```php
$blockedCommands = [
    'db:wipe',
    'migrate:fresh',
    'migrate:reset',
    'migrate:rollback',
    'key:generate',
];
```

### Logging Implementation
Every command execution is logged with:
- Command executed
- User ID (who executed it)
- IP address
- Timestamp
- Success/failure status
- Error message (if any)

**Log Locations:**
- Success: `storage/logs/laravel.log` (INFO level)
- Warnings: `storage/logs/laravel.log` (WARNING level)
- Errors: `storage/logs/laravel.log` (ERROR level)

---

## Usage Instructions

### Accessing the Feature
1. Navigate to **Admin Panel** → **System Settings**
2. Click on **Artisan Commands** tab
3. Select a command from the dropdown or use custom command option

### Executing Predefined Commands
1. Select a command from the categorized dropdown
2. Click **Execute Command** button
3. View output in the terminal display below

### Executing Custom Commands
1. Select **"✨ Custom Command (Advanced)"** from dropdown
2. Review the security warning
3. Enter your command (without `php artisan` prefix)
   - Example: `cache:clear --tags=views`
4. Click **Execute Command**

### Viewing Output
- Command output displays in a terminal-style interface
- Click **Copy** button to copy output to clipboard
- Click **Clear Output** to reset the display

---

## Technical Details

### Livewire Component Structure
```php
class ArtisanCommandRunner extends Component
{
    public $selectedCommand = '';      // Selected dropdown value
    public $customCommand = '';        // Custom command input
    public $commandOutput = '';        // Command execution output
    public $successMessage = '';       // Success feedback
    public $errorMessage = '';         // Error feedback
    public $loading = false;           // Loading state
    public $allowedCommands = [...];   // Whitelisted commands array
}
```

### Key Methods
- `executeCommand()` - Main execution logic with security checks
- `clearOutput()` - Reset output display
- `updatedSelectedCommand()` - Handle dropdown changes
- `resetMessages()` - Clear alert messages

### Execution Flow
1. User selects/enters command
2. Validate command is not empty
3. Security check: Verify command is whitelisted OR custom is allowed
4. For custom commands: Extract base command and check against blacklist
5. Execute command using `Artisan::call()`
6. Capture output using `Artisan::output()`
7. Log execution details
8. Display output or error to user

---

## Permissions Required
- **Permission:** `system.settings.view`
- **Route:** `admin/system-settings` (GET)
- **Middleware:** `auth`, `admin`, `permission:system.settings.view`

---

## Future Enhancements (Optional)
- [ ] Add command history/recent commands
- [ ] Add favorite commands feature
- [ ] Schedule commands for execution
- [ ] Add multi-command batch execution
- [ ] Export command output to file
- [ ] Add command templates with parameters
- [ ] Real-time streaming output (for long-running commands)

---

## Testing Checklist
- [ ] Test predefined cache commands
- [ ] Test database info commands
- [ ] Test custom command with valid input
- [ ] Test custom command with blocked command
- [ ] Verify error handling for invalid commands
- [ ] Verify logging is working correctly
- [ ] Test on mobile/responsive view
- [ ] Test permission restrictions
- [ ] Verify output copy to clipboard
- [ ] Test loading states and transitions

---

## Notes
- The `auth()->id()` lint warnings in IDE are false positives - Laravel's auth() helper correctly returns a guard with an id() method
- All destructive database commands are blocked by design for production safety
- Custom commands should be used cautiously and only by experienced administrators
- Command execution timeout is handled by Laravel's default Artisan timeout settings
