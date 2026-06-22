<?php
namespace App\Core;

class Validator
{
    private array $errors = [];
    private array $messages = [];
    private array $data = [];

    public function validate(array $data, array $rules): bool
    {
        $this->errors = [];
        $this->data = $data;

        foreach ($rules as $field => $ruleSet) {
            if (is_string($ruleSet)) {
                $ruleSet = explode('|', $ruleSet);
            }

            foreach ($ruleSet as $rule) {
                $params = [];
                if (str_contains($rule, ':')) {
                    [$rule, $paramStr] = explode(':', $rule, 2);
                    $params = explode(',', $paramStr);
                }

                $value = $data[$field] ?? null;
                $this->validateRule($field, $value, $rule, $params);
            }
        }

        return empty($this->errors);
    }

    private function validateRule(string $field, $value, string $rule, array $params): void
    {
        switch ($rule) {
            case 'required':
                if ($value === null || $value === '' || (is_array($value) && empty($value))) {
                    $this->addError($field, $rule, "{$field} is required");
                }
                break;

            case 'email':
                if ($value !== null && $value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, $rule, "{$field} must be a valid email");
                }
                break;

            case 'numeric':
                if ($value !== null && $value !== '' && !is_numeric($value)) {
                    $this->addError($field, $rule, "{$field} must be a number");
                }
                break;

            case 'integer':
                if ($value !== null && $value !== '' && !filter_var($value, FILTER_VALIDATE_INT)) {
                    $this->addError($field, $rule, "{$field} must be an integer");
                }
                break;

            case 'string':
                if ($value !== null && $value !== '' && !is_string($value)) {
                    $this->addError($field, $rule, "{$field} must be a string");
                }
                break;

            case 'min':
                $min = (int)$params[0];
                if (is_string($value)) {
                    if (strlen($value) < $min) {
                        $this->addError($field, $rule, "{$field} must be at least {$min} characters");
                    }
                } elseif (is_numeric($value) && (float)$value < $min) {
                    $this->addError($field, $rule, "{$field} must be at least {$min}");
                }
                break;

            case 'max':
                $max = (int)$params[0];
                if (is_string($value)) {
                    if (strlen($value) > $max) {
                        $this->addError($field, $rule, "{$field} must not exceed {$max} characters");
                    }
                } elseif (is_numeric($value) && (float)$value > $max) {
                    $this->addError($field, $rule, "{$field} must not exceed {$max}");
                }
                break;

            case 'confirmed':
                $confirmation = $this->data["{$field}_confirmation"] ?? null;
                if ($value !== $confirmation) {
                    $this->addError($field, $rule, "{$field} confirmation does not match");
                }
                break;

            case 'unique':
                $table = $params[0];
                $column = $params[1] ?? $field;
                $ignoreId = $params[2] ?? null;
                try {
                    $db = Database::getInstance();
                    $sql = "SELECT id FROM {$table} WHERE {$column} = :value";
                    $bindings = ['value' => $value];
                    if ($ignoreId) {
                        $pk = static::$primaryKey ?? 'id';
                        $sql .= " AND {$pk} != :ignore_id";
                        $bindings['ignore_id'] = $ignoreId;
                    }
                    $sql .= " LIMIT 1";
                    $exists = $db->fetch($sql, $bindings);
                    if ($exists) {
                        $this->addError($field, $rule, "{$field} already exists");
                    }
                } catch (\Exception $e) {
                }
                break;

            case 'exists':
                $table = $params[0];
                $column = $params[1] ?? $field;
                try {
                    $db = Database::getInstance();
                    $exists = $db->fetch(
                        "SELECT id FROM {$table} WHERE {$column} = :value LIMIT 1",
                        ['value' => $value]
                    );
                    if (!$exists) {
                        $this->addError($field, $rule, "{$field} does not exist");
                    }
                } catch (\Exception $e) {
                }
                break;
        }
    }

    private function addError(string $field, string $rule, string $message): void
    {
        $this->errors[$field][] = $message;
    }

    public function passes(): bool
    {
        return empty($this->errors);
    }

    public function fails(): bool
    {
        return !empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function getErrors(): array
    {
        $messages = [];
        foreach ($this->errors as $field => $fieldErrors) {
            foreach ($fieldErrors as $error) {
                $messages[] = $error;
            }
        }
        return $messages;
    }

    public static function sanitize(string $input): string
    {
        return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
    }

    public static function sanitizeEmail(string $email): string
    {
        return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
    }

    public static function sanitizeString(string $input): string
    {
        return preg_replace('/[^\p{L}\p{N}\s\.\,\-\_\']/u', '', trim($input));
    }
}
