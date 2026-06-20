# Privacy Scanner

Scans files and directories for secrets and personal data that shouldn't be in your codebase — API keys, database passwords, CPFs, emails, and more.

## Install

As a project dependency:

```bash
composer require lvmorales1/privacy-scanner
```

For standalone development:

```bash
composer install
```

## Usage

When installed as a dependency:

```bash
./vendor/bin/privacy-scan .
./vendor/bin/privacy-scan config/services.php
./vendor/bin/privacy-scan . --format=json
```

When running standalone:

```bash
php bin/privacy-scan .
php bin/privacy-scan config/services.php
php bin/privacy-scan . --format=json
```

## Example output

```
[CRITICAL] AWS Access Key
File:  config/services.php
Line:  12
Value: AKIA************MPLE

[HIGH] CPF (Brazilian Tax ID)
File:  storage/customers.csv
Line:  45
Value: 123.******9-09

Scanned 42 file(s) — 2 finding(s) — Risk Score: 15/100
```

Exit code is `0` when clean, `1` when findings are found.

## Configuration

Create a `privacy-scan.json` file in your project root to control when the scanner exits with `1`:

```json
{
    "fail_on_score": 1,
    "fail_on_severity": "LOW"
}
```

| Option | Default | Description |
|---|---|---|
| `fail_on_score` | `1` | Minimum per-finding risk score to trigger exit `1` |
| `fail_on_severity` | `"LOW"` | Minimum severity to trigger exit `1`. Accepted values: `LOW`, `MEDIUM`, `HIGH`, `CRITICAL` |

Both thresholds must be met for a finding to trigger exit `1`. For example, to only fail on `HIGH` or `CRITICAL` findings:

```json
{
    "fail_on_severity": "HIGH"
}
```

## Rules

Severity is derived from the risk score: **LOW** 1–3 · **MEDIUM** 4–5 · **HIGH** 6–7 · **CRITICAL** 8–10

### Secrets

| Rule | Category | Score | Severity |
|---|---|---|---|
| SSH Private Key | `ssh_private_key` | 10 | CRITICAL |
| AWS Access Key | `aws_key` | 9 | CRITICAL |
| Stripe Live Key | `stripe_key` | 9 | CRITICAL |
| Database Connection URL | `database_url` | 8 | CRITICAL |
| GitHub Token | `github_token` | 8 | CRITICAL |
| OpenAI API Key | `openai_key` | 8 | CRITICAL |
| JSON Web Token | `jwt` | 7 | HIGH |

### Personal Data

| Rule | Category | Score | Severity |
|---|---|---|---|
| CPF (Brazilian Tax ID) | `cpf` | 6 | HIGH |
| CNPJ (Brazilian Company ID) | `cnpj` | 5 | MEDIUM |
| Brazilian Phone Number | `phone_br` | 4 | MEDIUM |
| Email Address | `email` | 3 | LOW |

## Adding a new rule

Create a class in `src/Rules/Secrets/` or `src/Rules/PersonalData/`, extend `AbstractRule`, and register it in the corresponding detector.

```php
final class MyTokenRule extends AbstractRule
{
    public function getName(): string          { return 'My Token'; }
    public function getCategory(): string      { return 'my_token'; }
    public function getRiskScore(): int        { return 8; }
    protected function getType(): FindingType  { return FindingType::Secret; }
    protected function getPattern(): string    { return '/mytoken_[A-Za-z0-9]{32}/'; }
}
```

## Tests

```bash
./vendor/bin/phpunit
```
