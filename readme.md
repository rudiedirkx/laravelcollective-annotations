**This is a `laravelcollective/annotations` drop-in replacement**

To use this package instead of `laravelcollective/annotations` as a perfect drop-in replacement, do
this in your project:

1. `composer require rdx/laravelcollective-annotations`
2. if your project explicitly required `annotations`: `composer remove laravelcollective/annotations`

This will install `rdx/laravelcollective-annotations` and pretend it IS `laravelcollective/annotations`,
and all other packages will believe `laravelcollective/annotations` is installed, because Composer is awesome.

## UPGRADE to 9.0 (PHP Attributes instead of `doctrine/annotations`)

1. Download and set up `rector/rector`, see config below.
2. Add rule `Collective\Annotations\Rector\AnnotationsToAttributesRector` to Rector set.
3. Run Rector **with debug enabled**, see command below.
4. Manually fix skipped conversions due to complicated annotation syntax, see complicated example below.
5. Run your cs fixer to import all FQCN, see `php-cs-fixer` rule below.
6. Remove `$useAttributes` from your `AnnotationsServiceProvider`, because that doesn't exist anymore.

### Example Rector config

```php
return RectorConfig::configure()
	->withPaths([
		__DIR__ . '/app/Http/Controllers',
	])
	->withRules([
		AnnotationsToAttributesRector::class,
	])
;
```

### Run Rector command

```bash
vendor/bin/rector process -v --debug --no-diffs app/Http/
```

This will print all analyzed files, and potential errors, and might include `WARNING` lines for
skipped methods, like:

```
  WARNING: getPhaseDownload contains unconverted {} properties
```

### Too complicated annotation

```
@Get("/some/{thing}/download.{ext}", as="thing.download", where={"ext"="json|pdf|xlsx"})
```

The path isn't a problem, nor the `as=`, but the `where=` contains another inner structure, and that
won't be converted correctly, so they're broken now, so you have to fix those right after.

### php-cs-fixer rule

I assume you have `php-cs-fixer` set up. Add this rule **temporarily**:

```php
[
	'fully_qualified_strict_types' => [
		'import_symbols' => true,
		'phpdoc_tags' => [],
	],
]
```

and run `fix` **only for the Controllers dir**:

```bash
vendor/bin/php-cs-fixer fix app/Http/Controllers/
```

This will convert all the Rector added FQCN to imports, but only those, only in the `Controllers` dir.

If `php-cs-fixer` skips files and complains about "errors reported during linting", the Rector rule
isn't good enough, and something is broken:

```
Files that were not fixed due to errors reported during linting before fixing:
   1) /var/www/myproject/app/Http/Controllers/MyController.php
```

You'll have to fix those manually.
