# PHP Config Printer

[![Downloads total](https://img.shields.io/packagist/dt/migrify/php-config-printer.svg?style=flat-square)](https://packagist.org/packages/migrify/php-config-printer/stats)

Print Symfony services array with configuration to to plain PHP file format thanks to this simple php-parser wrapper

## Install

```bash
composer require migrify/php-config-printer --dev
```

## Use

```php
<?php

use Migrify\PhpConfigPrinter\YamlToPhpConverter;

class SomeClass
{
    /**
     * @var YamlToPhpConverter
     */
    private $yamlToPhpConverter;
    
    public function __construct(YamlToPhpConverter $yamlToPhpConverter)
    {
        $this->yamlToPhpConverter = $yamlToPhpConverter;
    }
    
    public function run()
    {
        $phpFileContent = $this->yamlToPhpConverter->convertYamlArray([
            'parameters' => [
                'key' => 'value',
            ],
            'services' => [
                '_defaults' => [
                    'autowire' => true,
                    'autoconfigure' => true,
                ]       
            ]       
        ]);

        // dump the $phpFileContent file
        // ... 
    }
}
```
