<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class GenerateMigrationsFromDatabase extends Command
{
    protected $signature = 'migrations:generate-from-db';
    protected $description = 'Generate migration files from existing database schema';

    private $tables = [];
    private $foreignKeys = [];
    private $orderedTables = [];
    private $migrationNumber = 1;

    public function handle()
    {
        $this->info('🔍 Reading database schema...');
        
        // Get all tables
        $this->tables = $this->getAllTables();
        
        if (empty($this->tables)) {
            $this->error('No tables found in database!');
            return 1;
        }
        
        $this->info('Found ' . count($this->tables) . ' tables');
        
        // Analyze foreign key dependencies
        $this->info('🔗 Analyzing foreign key dependencies...');
        $this->analyzeForeignKeys();
        
        // Sort tables by dependency order
        $this->info('📊 Sorting tables by dependency order...');
        $this->orderedTables = $this->sortTablesByDependency();
        
        // Create output directory
        $outputDir = database_path('new-migrations');
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }
        
        // Generate migration files
        $this->info('📝 Generating migration files...');
        $this->generateMigrations($outputDir);
        
        $this->info('✅ Successfully generated ' . count($this->orderedTables) . ' migration files!');
        $this->info('📁 Location: ' . $outputDir);
        
        return 0;
    }

    private function getAllTables(): array
    {
        $tables = [];
        $connection = config('database.default');
        $database = config("database.connections.{$connection}.database");
        
        $result = DB::select("
            SELECT TABLE_NAME 
            FROM information_schema.TABLES 
            WHERE TABLE_SCHEMA = ? 
            AND TABLE_TYPE = 'BASE TABLE'
            ORDER BY TABLE_NAME
        ", [$database]);
        
        // Exclude the migrations table as Laravel creates it automatically
        $excludeTables = ['migrations'];
        
        foreach ($result as $row) {
            if (!in_array($row->TABLE_NAME, $excludeTables)) {
                $tables[] = $row->TABLE_NAME;
            }
        }
        
        return $tables;
    }

    private function analyzeForeignKeys(): void
    {
        $connection = config('database.default');
        $database = config("database.connections.{$connection}.database");
        
        foreach ($this->tables as $table) {
            $foreignKeys = DB::select("
                SELECT 
                    kcu.CONSTRAINT_NAME,
                    kcu.COLUMN_NAME,
                    kcu.REFERENCED_TABLE_NAME,
                    kcu.REFERENCED_COLUMN_NAME,
                    rc.UPDATE_RULE,
                    rc.DELETE_RULE
                FROM information_schema.KEY_COLUMN_USAGE kcu
                LEFT JOIN information_schema.REFERENTIAL_CONSTRAINTS rc
                    ON kcu.CONSTRAINT_NAME = rc.CONSTRAINT_NAME
                    AND kcu.TABLE_SCHEMA = rc.CONSTRAINT_SCHEMA
                WHERE kcu.TABLE_SCHEMA = ?
                AND kcu.TABLE_NAME = ?
                AND kcu.REFERENCED_TABLE_NAME IS NOT NULL
            ", [$database, $table]);
            
            if (!empty($foreignKeys)) {
                $this->foreignKeys[$table] = $foreignKeys;
            }
        }
    }

    private function sortTablesByDependency(): array
    {
        $sorted = [];
        $visited = [];
        $visiting = [];
        
        foreach ($this->tables as $table) {
            if (!isset($visited[$table])) {
                $this->visitTable($table, $visited, $visiting, $sorted);
            }
        }
        
        return $sorted;
    }

    private function visitTable($table, &$visited, &$visiting, &$sorted): void
    {
        // If already visited, skip completely
        if (isset($visited[$table])) {
            return;
        }
        
        // If currently being visited, circular dependency detected
        if (isset($visiting[$table])) {
            return;
        }
        
        // Mark as currently being visited
        $visiting[$table] = true;
        
        // Visit dependencies first
        if (isset($this->foreignKeys[$table])) {
            foreach ($this->foreignKeys[$table] as $fk) {
                $referencedTable = $fk->REFERENCED_TABLE_NAME;
                
                // Skip self-referencing tables and already visited tables
                if ($referencedTable !== $table && 
                    in_array($referencedTable, $this->tables) && 
                    !isset($visited[$referencedTable])) {
                    $this->visitTable($referencedTable, $visited, $visiting, $sorted);
                }
            }
        }
        
        // Mark as visited and add to sorted list
        $visited[$table] = true;
        unset($visiting[$table]);
        $sorted[] = $table;
    }

    private function generateMigrations(string $outputDir): void
    {
        $connection = config('database.default');
        $database = config("database.connections.{$connection}.database");
        
        foreach ($this->orderedTables as $table) {
            $this->info("  → Generating {$table}...");
            
            // Get table columns
            $columns = $this->getTableColumns($database, $table);
            
            // Get indexes
            $indexes = $this->getTableIndexes($database, $table);
            
            // Get foreign keys
            $foreignKeys = $this->foreignKeys[$table] ?? [];
            
            // Generate migration content
            $migrationContent = $this->generateMigrationContent($table, $columns, $indexes, $foreignKeys);
            
            // Create migration file
            $fileName = sprintf(
                '2025_01_01_%06d_create_%s_table.php',
                $this->migrationNumber++,
                $table
            );
            
            file_put_contents(
                $outputDir . '/' . $fileName,
                $migrationContent
            );
        }
    }

    private function getTableColumns(string $database, string $table): array
    {
        return DB::select("
            SELECT 
                COLUMN_NAME,
                COLUMN_TYPE,
                DATA_TYPE,
                IS_NULLABLE,
                COLUMN_DEFAULT,
                EXTRA,
                CHARACTER_MAXIMUM_LENGTH,
                NUMERIC_PRECISION,
                NUMERIC_SCALE,
                COLUMN_COMMENT
            FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = ?
            AND TABLE_NAME = ?
            ORDER BY ORDINAL_POSITION
        ", [$database, $table]);
    }

    private function getTableIndexes(string $database, string $table): array
    {
        $indexes = DB::select("
            SELECT 
                INDEX_NAME,
                COLUMN_NAME,
                NON_UNIQUE,
                SEQ_IN_INDEX
            FROM information_schema.STATISTICS
            WHERE TABLE_SCHEMA = ?
            AND TABLE_NAME = ?
            ORDER BY INDEX_NAME, SEQ_IN_INDEX
        ", [$database, $table]);
        
        // Group by index name
        $grouped = [];
        foreach ($indexes as $index) {
            if (!isset($grouped[$index->INDEX_NAME])) {
                $grouped[$index->INDEX_NAME] = [
                    'columns' => [],
                    'unique' => $index->NON_UNIQUE == 0,
                ];
            }
            $grouped[$index->INDEX_NAME]['columns'][] = $index->COLUMN_NAME;
        }
        
        return $grouped;
    }

    private function generateMigrationContent(string $table, array $columns, array $indexes, array $foreignKeys): string
    {
        $className = 'Create' . Str::studly($table) . 'Table';
        
        // Check if table has both created_at and updated_at
        $hasCreatedAt = false;
        $hasUpdatedAt = false;
        $hasDeletedAt = false;
        
        foreach ($columns as $column) {
            if ($column->COLUMN_NAME === 'created_at') $hasCreatedAt = true;
            if ($column->COLUMN_NAME === 'updated_at') $hasUpdatedAt = true;
            if ($column->COLUMN_NAME === 'deleted_at') $hasDeletedAt = true;
        }
        
        $useTimestamps = $hasCreatedAt && $hasUpdatedAt;
        
        $content = "<?php\n\n";
        $content .= "use Illuminate\\Database\\Migrations\\Migration;\n";
        $content .= "use Illuminate\\Database\\Schema\\Blueprint;\n";
        $content .= "use Illuminate\\Support\\Facades\\Schema;\n\n";
        $content .= "return new class extends Migration\n{\n";
        $content .= "    /**\n";
        $content .= "     * Run the migrations.\n";
        $content .= "     */\n";
        $content .= "    public function up(): void\n";
        $content .= "    {\n";
        $content .= "        Schema::create('{$table}', function (Blueprint \$table) {\n";
        
        // Add columns
        foreach ($columns as $column) {
            $columnDef = $this->generateColumnDefinition($column, $foreignKeys, $useTimestamps);
            if (!empty($columnDef)) {
                $content .= $columnDef;
            }
        }
        
        // Add timestamps if both created_at and updated_at exist
        if ($useTimestamps) {
            $content .= "            \$table->timestamps();\n";
        }
        
        // Add soft deletes if deleted_at exists
        if ($hasDeletedAt) {
            $content .= "            \$table->softDeletes();\n";
        }
        
        // Add indexes (skip only PRIMARY and foreign key indexes)
        $content .= "\n            // Indexes\n";
        foreach ($indexes as $indexName => $indexData) {
            if ($indexName === 'PRIMARY' || strpos($indexName, '_foreign') !== false) {
                continue;
            }
            
            $columnsStr = "'" . implode("', '", $indexData['columns']) . "'";
            
            // Generate a short custom name if the auto-generated name would be too long
            $estimatedLength = strlen($table) + strlen(implode('_', $indexData['columns'])) + 10;
            $customName = null;
            
            if ($estimatedLength > 64) {
                // Create a short name using table prefix and hash
                $customName = $table . '_' . substr(md5($indexName), 0, 10) . '_' . ($indexData['unique'] ? 'unq' : 'idx');
            }
            
            if ($indexData['unique']) {
                if (count($indexData['columns']) === 1) {
                    // Skip if already defined as unique on column
                    continue;
                }
                if ($customName) {
                    $content .= "            \$table->unique([{$columnsStr}], '{$customName}');\n";
                } else {
                    $content .= "            \$table->unique([{$columnsStr}]);\n";
                }
            } else {
                if ($customName) {
                    $content .= "            \$table->index([{$columnsStr}], '{$customName}');\n";
                } else {
                    $content .= "            \$table->index([{$columnsStr}]);\n";
                }
            }
        }
        
        $content .= "        });\n";
        $content .= "    }\n\n";
        $content .= "    /**\n";
        $content .= "     * Reverse the migrations.\n";
        $content .= "     */\n";
        $content .= "    public function down(): void\n";
        $content .= "    {\n";
        $content .= "        Schema::dropIfExists('{$table}');\n";
        $content .= "    }\n";
        $content .= "};\n";
        
        return $content;
    }

    private function generateColumnDefinition($column, array $foreignKeys, bool $useTimestamps = false): string
    {
        $name = $column->COLUMN_NAME;
        $type = $column->DATA_TYPE;
        $line = "            ";
        
        // Check if this is a foreign key column
        $isForeignKey = false;
        $referencedTable = null;
        $onDelete = 'restrict';
        $onUpdate = 'restrict';
        
        foreach ($foreignKeys as $fk) {
            if ($fk->COLUMN_NAME === $name) {
                $isForeignKey = true;
                $referencedTable = $fk->REFERENCED_TABLE_NAME;
                $onDelete = strtolower($fk->DELETE_RULE);
                $onUpdate = strtolower($fk->UPDATE_RULE);
                break;
            }
        }
        
        // Handle special columns
        if ($name === 'id' && $column->EXTRA === 'auto_increment') {
            $line .= "\$table->id();\n";
            return $line;
        }
        
        // Skip created_at and updated_at if using timestamps()
        if ($useTimestamps && ($name === 'created_at' || $name === 'updated_at')) {
            return ""; // Will be handled by timestamps()
        }
        
        // Skip deleted_at as it will be handled by softDeletes()
        if ($name === 'deleted_at') {
            return ""; // Will be handled by softDeletes()
        }
        
        // Foreign key columns
        if ($isForeignKey && preg_match('/^(.+)_id$/', $name)) {
            $nullable = $column->IS_NULLABLE === 'YES' ? '->nullable()' : '';
            $line .= "\$table->foreignId('{$name}'){$nullable}->constrained('{$referencedTable}')->onDelete('{$onDelete}');\n";
            return $line;
        }
        
        // Regular columns
        $line .= "\$table->";
        
        // Map data types
        $line .= $this->mapColumnType($column);
        
        // Add modifiers
        if ($column->IS_NULLABLE === 'YES') {
            $line .= "->nullable()";
        }
        
        if ($column->COLUMN_DEFAULT !== null && $column->COLUMN_DEFAULT !== 'NULL') {
            $default = $column->COLUMN_DEFAULT;
            
            // Handle CURRENT_TIMESTAMP for timestamp/datetime columns
            if (in_array($type, ['timestamp', 'datetime']) && 
                (strtoupper($default) === 'CURRENT_TIMESTAMP' || 
                 strtolower($default) === 'current_timestamp()' ||
                 strtolower(trim($default, "'\"")) === 'current_timestamp')) {
                $line .= "->useCurrent()";
            } elseif ($type === 'tinyint' && ($default === '1' || $default === '0')) {
                $line .= "->default(" . ($default === '1' ? 'true' : 'false') . ")";
            } elseif (is_numeric($default)) {
                $line .= "->default({$default})";
            } else {
                // Remove surrounding quotes if present
                $default = trim($default, "'\"");
                $line .= "->default('{$default}')";
            }
        }
        
        if (strpos($column->COLUMN_TYPE, 'unsigned') !== false) {
            $line .= "->unsigned()";
        }
        
        if ($column->EXTRA === 'auto_increment') {
            $line .= "->autoIncrement()";
        }
        
        // Handle ON UPDATE CURRENT_TIMESTAMP
        if (stripos($column->EXTRA, 'on update current_timestamp') !== false) {
            $line .= "->useCurrentOnUpdate()";
        }
        
        $line .= ";\n";
        
        return $line;
    }

    private function mapColumnType($column): string
    {
        $type = $column->DATA_TYPE;
        $name = $column->COLUMN_NAME;
        $columnType = $column->COLUMN_TYPE;
        
        switch ($type) {
            case 'bigint':
                if ($column->EXTRA === 'auto_increment') {
                    return "id()";
                }
                return "bigInteger('{$name}')";
                
            case 'int':
            case 'integer':
                return "integer('{$name}')";
                
            case 'tinyint':
                if ($columnType === 'tinyint(1)') {
                    return "boolean('{$name}')";
                }
                return "tinyInteger('{$name}')";
                
            case 'smallint':
                return "smallInteger('{$name}')";
                
            case 'mediumint':
                return "mediumInteger('{$name}')";
                
            case 'varchar':
                $length = $column->CHARACTER_MAXIMUM_LENGTH ?? 255;
                return "string('{$name}', {$length})";
                
            case 'char':
                $length = $column->CHARACTER_MAXIMUM_LENGTH ?? 255;
                return "char('{$name}', {$length})";
                
            case 'text':
                return "text('{$name}')";
                
            case 'mediumtext':
                return "mediumText('{$name}')";
                
            case 'longtext':
                return "longText('{$name}')";
                
            case 'decimal':
                $precision = $column->NUMERIC_PRECISION ?? 8;
                $scale = $column->NUMERIC_SCALE ?? 2;
                return "decimal('{$name}', {$precision}, {$scale})";
                
            case 'float':
                return "float('{$name}')";
                
            case 'double':
                return "double('{$name}')";
                
            case 'date':
                return "date('{$name}')";
                
            case 'datetime':
                return "dateTime('{$name}')";
                
            case 'timestamp':
                return "timestamp('{$name}')";
                
            case 'time':
                return "time('{$name}')";
                
            case 'year':
                return "year('{$name}')";
                
            case 'json':
                return "json('{$name}')";
                
            case 'enum':
                // Extract enum values from COLUMN_TYPE
                preg_match("/enum\((.*)\)/", $columnType, $matches);
                $values = $matches[1] ?? "''";
                return "enum('{$name}', [{$values}])";
                
            case 'blob':
                return "binary('{$name}')";
                
            default:
                return "string('{$name}')";
        }
    }
}
