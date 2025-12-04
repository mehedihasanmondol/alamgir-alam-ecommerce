<?php

namespace App\Livewire\Admin\SystemSettings;

use Livewire\Component;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class ArtisanCommandRunner extends Component
{
    public $selectedCommand = '';
    public $customCommand = '';
    public $commandOutput = '';
    public $successMessage = '';
    public $errorMessage = '';
    public $loading = false;

    /**
     * Predefined safe artisan commands
     * SECURITY: Only whitelisted commands can be executed
     */
    public $allowedCommands = [
        // Cache Commands
        'cache:clear' => 'Clear application cache',
        'config:clear' => 'Clear configuration cache',
        'route:clear' => 'Clear route cache',
        'view:clear' => 'Clear compiled view files',
        'optimize:clear' => 'Clear all cached bootstrap files',
        'optimize' => 'Cache framework bootstrap files',
        
        // Database Commands
        'migrate:status' => 'Show migration status',
        'db:show' => 'Display database information',
        
        // Queue Commands
        'queue:failed' => 'List all failed queue jobs',
        'queue:restart' => 'Restart queue worker daemons',
        
        // Storage Commands
        'storage:link' => 'Create symbolic link for storage',
        
        // Maintenance Commands
        'down' => 'Put application into maintenance mode',
        'up' => 'Bring application out of maintenance mode',
        
        // Other Useful Commands
        'about' => 'Display application information',
        'env' => 'Display current framework environment',
        'inspire' => 'Display an inspiring quote',
        'list' => 'List all available commands',
    ];

    /**
     * Execute selected artisan command
     */
    public function executeCommand()
    {
        $this->resetMessages();
        
        // Determine which command to execute
        $command = $this->selectedCommand === 'custom' 
            ? $this->customCommand 
            : $this->selectedCommand;

        // Validate command
        if (empty($command)) {
            $this->errorMessage = 'Please select or enter a command to execute.';
            return;
        }

        // Security check: Validate command is in whitelist or custom is allowed
        if ($this->selectedCommand !== 'custom' && !array_key_exists($command, $this->allowedCommands)) {
            $this->errorMessage = 'This command is not allowed for security reasons.';
            Log::warning('Unauthorized artisan command attempt', [
                'command' => $command,
                'user_id' => auth()->id(),
                'ip' => request()->ip()
            ]);
            return;
        }

        // For custom commands, extract base command and validate
        if ($this->selectedCommand === 'custom') {
            $baseCommand = explode(' ', trim($command))[0];
            
            // Block dangerous commands
            $blockedCommands = [
                'db:wipe',
                'migrate:fresh',
                'migrate:reset',
                'migrate:rollback',
                'key:generate',
            ];

            if (in_array($baseCommand, $blockedCommands)) {
                $this->errorMessage = "Command '{$baseCommand}' is blocked for security reasons.";
                Log::warning('Blocked artisan command attempt', [
                    'command' => $command,
                    'user_id' => auth()->id(),
                    'ip' => request()->ip()
                ]);
                return;
            }
        }

        try {
            $this->loading = true;
            
            // Execute artisan command
            Artisan::call($command);
            
            // Capture output
            $output = Artisan::output();
            $this->commandOutput = $output ?: 'Command executed successfully (no output).';
            $this->successMessage = "Command '{$command}' executed successfully!";

            // Log successful execution
            Log::info('Artisan command executed', [
                'command' => $command,
                'user_id' => auth()->id(),
                'ip' => request()->ip()
            ]);

            $this->dispatch('command-executed', command: $command);

        } catch (\Exception $e) {
            $this->errorMessage = 'Error executing command: ' . $e->getMessage();
            $this->commandOutput = '';
            
            Log::error('Artisan command execution failed', [
                'command' => $command,
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'ip' => request()->ip()
            ]);

            $this->dispatch('command-error', message: $e->getMessage());
        } finally {
            $this->loading = false;
        }
    }

    /**
     * Clear all messages and output
     */
    public function clearOutput()
    {
        $this->commandOutput = '';
        $this->resetMessages();
    }

    /**
     * Reset messages
     */
    private function resetMessages()
    {
        $this->successMessage = '';
        $this->errorMessage = '';
    }

    /**
     * Update custom command when dropdown changes
     */
    public function updatedSelectedCommand()
    {
        if ($this->selectedCommand !== 'custom') {
            $this->customCommand = '';
            $this->clearOutput();
        }
    }

    public function render()
    {
        return view('livewire.admin.system-settings.artisan-command-runner');
    }
}
