<?php
namespace App\Core;

class Model
{
    protected static string $table = '';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [];

    protected ?\PDOStatement $stmt = null;
    protected string $queryBuilder = '';
    protected array $bindings = [];
    protected string $builderType = 'select';

    public static function all(): array
    {
        $db = Database::getInstance();
        return $db->fetchAll("SELECT * FROM " . static::$table . " ORDER BY id DESC");
    }

    public static function find($id): ?\stdClass
    {
        $db = Database::getInstance();
        return $db->fetch(
            "SELECT * FROM " . static::$table . " WHERE " . static::$primaryKey . " = :id",
            ['id' => $id]
        );
    }

    public static function findBy(string $column, $value): ?\stdClass
    {
        $db = Database::getInstance();
        return $db->fetch(
            "SELECT * FROM " . static::$table . " WHERE {$column} = :value LIMIT 1",
            ['value' => $value]
        );
    }

    public static function create(array $data): int
    {
        $db = Database::getInstance();
        $filtered = static::filterFillable($data);
        return $db->insert(static::$table, $filtered);
    }

    public static function update($id, array $data): int
    {
        $db = Database::getInstance();
        $filtered = static::filterFillable($data);
        $pk = static::$primaryKey;
        return $db->update(static::$table, $filtered, "{$pk} = :pk", ['pk' => $id]);
    }

    public static function delete($id): int
    {
        $db = Database::getInstance();
        $pk = static::$primaryKey;
        return $db->delete(static::$table, "{$pk} = :id", ['id' => $id]);
    }

    public static function where(...$args): static
    {
        $instance = new static();
        $column = $args[0];

        if (count($args) === 2) {
            $value = $args[1];
            $operator = '=';
        } else {
            $value = $args[1];
            $operator = $args[2] ?? '=';
        }

        $instance->builderType = 'select';

        if (strtoupper($operator) === 'IS') {
            $nullCheck = $value === null ? 'NULL' : 'NOT NULL';
            $instance->queryBuilder = "SELECT * FROM " . static::$table . " WHERE {$column} IS {$nullCheck}";
        } else {
            $paramKey = str_replace('.', '_', $column) . '_' . uniqid();
            $instance->queryBuilder = "SELECT * FROM " . static::$table . " WHERE {$column} {$operator} :{$paramKey}";
            $instance->bindings[$paramKey] = $value;
        }

        return $instance;
    }

    public function orWhere(...$args): static
    {
        $column = $args[0];

        if (count($args) === 2) {
            $value = $args[1];
            $operator = '=';
        } else {
            $value = $args[1];
            $operator = $args[2] ?? '=';
        }

        if (strtoupper($operator) === 'IS') {
            $nullCheck = $value === null ? 'NULL' : 'NOT NULL';
            $this->queryBuilder .= " OR {$column} IS {$nullCheck}";
        } else {
            $paramKey = str_replace('.', '_', $column) . '_' . uniqid();
            $this->queryBuilder .= " OR {$column} {$operator} :{$paramKey}";
            $this->bindings[$paramKey] = $value;
        }

        return $this;
    }

    public function orderBy(string $column, string $direction = 'ASC'): static
    {
        $this->queryBuilder .= " ORDER BY {$column} {$direction}";
        return $this;
    }

    public function limit(int $limit): static
    {
        $this->queryBuilder .= " LIMIT {$limit}";
        return $this;
    }

    public function offset(int $offset): static
    {
        $this->queryBuilder .= " OFFSET {$offset}";
        return $this;
    }

    public function get(): array
    {
        $db = Database::getInstance();
        if (empty($this->queryBuilder)) {
            $this->queryBuilder = "SELECT * FROM " . static::$table;
        }
        return $db->fetchAll($this->queryBuilder, $this->bindings);
    }

    public function first(): ?\stdClass
    {
        $this->limit(1);
        $results = $this->get();
        return $results[0] ?? null;
    }

    public function count(): int
    {
        $db = Database::getInstance();
        $sql = str_replace("SELECT *", "SELECT COUNT(*) as count", $this->queryBuilder);
        $sql = str_replace("SELECT * ", "SELECT COUNT(*) as count ", $sql);
        if (empty($this->queryBuilder)) {
            $sql = "SELECT COUNT(*) as count FROM " . static::$table;
        }
        $result = $db->fetch($sql, $this->bindings);
        return (int)($result->count ?? 0);
    }

    public static function paginate(int $perPage = 15, int $page = 1): array
    {
        $db = Database::getInstance();
        $total = $db->fetch("SELECT COUNT(*) as count FROM " . static::$table);
        $totalCount = (int)$total->count;
        $lastPage = max(1, ceil($totalCount / $perPage));
        $page = max(1, min($page, $lastPage));
        $offset = ($page - 1) * $perPage;
        $data = $db->fetchAll(
            "SELECT * FROM " . static::$table . " ORDER BY id DESC LIMIT {$perPage} OFFSET {$offset}"
        );
        return [
            'current_page' => $page,
            'data' => $data,
            'per_page' => $perPage,
            'total' => $totalCount,
            'last_page' => $lastPage,
            'from' => $offset + 1,
            'to' => min($offset + $perPage, $totalCount),
        ];
    }

    protected static function filterFillable(array $data): array
    {
        if (empty(static::$fillable)) return $data;
        return array_intersect_key($data, array_flip(static::$fillable));
    }
}
