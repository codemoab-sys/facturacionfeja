<?php
declare(strict_types=1);

namespace App\Validacion;

class Validador
{
    private array $errors = [];

    public function validate(array $data, array $rules): ?array
    {
        $this->errors = [];

        foreach ($rules as $field => $fieldRules) {
            $value = $data[$field] ?? null;
            $ruleList = is_array($fieldRules) ? $fieldRules : explode('|', $fieldRules);

            foreach ($ruleList as $rule) {
                $params = [];
                if (str_contains($rule, ':')) {
                    $parts = explode(':', $rule, 2);
                    $rule = $parts[0];
                    $params = explode(',', $parts[1]);
                }

                $methodName = 'rule' . ucfirst($rule);
                if (method_exists($this, $methodName)) {
                    $this->$methodName($field, $value, $params);
                }
            }
        }

        return !empty($this->errors) ? $this->errors : null;
    }

    private function addError(string $field, string $message): void
    {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }
        $this->errors[$field][] = $message;
    }

    private function ruleRequired(string $field, mixed $value, array $params = []): void
    {
        if ($value === null || $value === '' || (is_array($value) && empty($value))) {
            $this->addError($field, 'El campo ' . $field . ' es obligatorio.');
        }
    }

    private function ruleEmail(string $field, mixed $value, array $params = []): void
    {
        if ($value !== null && $value !== '') {
            if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $this->addError($field, 'El campo ' . $field . ' debe ser un email válido.');
            }
        }
    }

    private function ruleMin(string $field, mixed $value, array $params = []): void
    {
        if ($value !== null && $value !== '') {
            $min = (int)($params[0] ?? 0);
            if (is_string($value) && mb_strlen($value) < $min) {
                $this->addError($field, 'El campo ' . $field . ' debe tener al menos ' . $min . ' caracteres.');
            }
            if (is_numeric($value) && (float)$value < $min) {
                $this->addError($field, 'El campo ' . $field . ' debe ser mayor o igual a ' . $min . '.');
            }
        }
    }

    private function ruleMax(string $field, mixed $value, array $params = []): void
    {
        if ($value !== null && $value !== '') {
            $max = (int)($params[0] ?? 0);
            if (is_string($value) && mb_strlen($value) > $max) {
                $this->addError($field, 'El campo ' . $field . ' debe tener máximo ' . $max . ' caracteres.');
            }
            if (is_numeric($value) && (float)$value > $max) {
                $this->addError($field, 'El campo ' . $field . ' debe ser menor o igual a ' . $max . '.');
            }
        }
    }

    private function ruleNumeric(string $field, mixed $value, array $params = []): void
    {
        if ($value !== null && $value !== '') {
            if (!is_numeric($value)) {
                $this->addError($field, 'El campo ' . $field . ' debe ser numérico.');
            }
        }
    }

    private function ruleIn(string $field, mixed $value, array $params = []): void
    {
        if ($value !== null && $value !== '') {
            if (!in_array((string)$value, $params, true)) {
                $this->addError($field, 'El campo ' . $field . ' debe ser uno de: ' . implode(', ', $params) . '.');
            }
        }
    }

    private function ruleUrl(string $field, mixed $value, array $params = []): void
    {
        if ($value !== null && $value !== '') {
            if (!filter_var($value, FILTER_VALIDATE_URL)) {
                $this->addError($field, 'El campo ' . $field . ' debe ser una URL válida.');
            }
        }
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function passes(): bool
    {
        return empty($this->errors);
    }

    public function fails(): bool
    {
        return !empty($this->errors);
    }
}
