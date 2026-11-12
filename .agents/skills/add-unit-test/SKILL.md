---
name: add-unit-test
description: Add or change PHPUnit unit tests in DuelLab - test class in tests/Unit, fixtures in tests/Unit/Fixture, a valid and an invalid case per behaviour, names that carry the whole meaning, run in the php container. Use for any task that writes, extends or fixes tests.
---

# Add or change a unit test

Follow the conventions in `AGENTS.md`. Work through the steps in order; if a step does not apply, say so in your summary.
Production code does not change in a test task. If a test exposes a bug, report it to the owner; the fix is a separate `fix(...)` commit.

## 1. Name the behaviours first

Before writing code, list the behaviours under test, each with a valid and an invalid case:

- Test through the public API only. Private methods are details that may change (`Routing::createPattern()` is tested via `dispatch()`).
- A test that records what the code does today (characterization test) asserts the current behaviour, even a surprising one.
  Write the surprise into the summary instead of "fixing" the expectation.

## 2. Files and namespaces

```
tests/Unit/<Class>Test.php          App\Tests\Unit\<Class>Test, final, extends TestCase
tests/Unit/Fixture/<Name>.php       App\Tests\Unit\Fixture\<Name>: test doubles the code under test creates itself
```

- `declare(strict_types=1);` and typed signatures, as in production code.
- The folder mirrors the namespace (`App\Tests\` → `tests/` in `autoload-dev`). No `.gitkeep`.
- No database, network or filesystem writes in unit tests.

## 3. Writing the test

- **No comments or docstrings.** The method name says the whole thing: `testGetRouteDoesNotMatchPostRequest`.
- One behaviour per test: arrange in a line or two, then the assertion.
- Fresh object in `setUp()`; no static state shared between tests.
- Same behaviour, several inputs → a `public static` provider with named rows and `#[DataProvider('name')]`.
- Exceptions: `expectException()` (and `expectExceptionMessage()` when the message is part of the contract) **before** the call.
- Code that creates objects itself (`new $class()` in the router): a fixture whose action `echo`es a short marker,
  checked with `expectOutputString()`. An empty string asserts that nothing was called.

```php
public function testNumericParameterIsPassedAsNamedInt(): void
{
    $this->routing->get('/demos/{id}', SpyController::class, 'show');

    $this->expectOutputString('show:int:7');
    $this->assertTrue($this->routing->dispatch('GET', '/demos/7'));
}
```

## 4. Verify

```bash
docker compose exec -T php php -l <each changed .php file>
docker compose exec -T php vendor/bin/phpunit --testdox
```

`phpunit.xml` fails a test on any warning, notice, deprecation or risky test (no assertion). Fix the cause, never loosen the config.
Check that a new test can fail: break the expectation once on purpose, see it red, revert.

## 5. Finish

- Report: behaviours covered (valid/invalid), anything surprising the tests recorded, the phpunit result.
- Commit with prefix `test(<scope>)` when the change only adds or changes tests.
