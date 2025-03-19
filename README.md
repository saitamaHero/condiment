# Condiment
> Conditional Statement (Condiment)

Condiment is a PHP library focused on evaluating conditions.The main goal of this library is to allow programmers to evaluate conditions and manage workflows in their applications.

### Usage

```php
use Condiment\Evaluables\Evaluators\FluentEvaluator;

$evaluator = new FluentEvaluator();

// Basic condition evaluation
$evaluator->equals("Hello", "Hello")->evaluate(); // true

// Fluent API: chaining conditions with AND
$evaluator->contains("Hello! This is the Condiment library", "condiment")
          ->equals(1, 1)
          ->evaluate(); // true

// This results in: `contains AND equals`

// Using NOT and OR operators for complex evaluations
$evaluator->contains("Rice", "ice")
          ->orNotContains("water", "ter")
          ->evaluate(); // true

// This results in: `contains OR !contains`

```

## Contributing

Contributions are welcome! Please open an issue or submit a pull request.
