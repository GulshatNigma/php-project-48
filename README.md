### Hexlet tests and linter status:
[![Actions Status](https://github.com/GulshatNigma/php-project-48/workflows/hexlet-check/badge.svg)](https://github.com/GulshatNigma/php-project-48/actions)
[![PHP Composer](https://github.com/GulshatNigma/php-project-48/actions/workflows/PHP.yml/badge.svg)](https://github.com/GulshatNigma/php-project-48/actions/workflows/PHP.yml)
[![Maintainability](https://api.codeclimate.com/v1/badges/a328485cbd025259bd66/maintainability)](https://codeclimate.com/github/GulshatNigma/php-project-48/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/a328485cbd025259bd66/test_coverage)](https://codeclimate.com/github/GulshatNigma/php-project-48/test_coverage)

### Вычислитель отличий
Вычислитель отличий - программа, определяющая разницу между двумя структурами данных.<br>
Поддерживает входные данные форматов: yaml и json.<br>
Отчет генерируется в виде stylish (по умолчанию), plain и json.

## Минимальные требования
1. PHP версии 7.4.0 (и выше)
2. Composer

## Инструкция по установке и запуску
```
$ git clone git@github.com:GulshatNigma/php-project-48.git
$ cd php-project-48
$ make install
```
## Пример использования
<b>Сравнение плоских файлов (JSON, YAML):</b><br>
[![asciicast](https://asciinema.org/a/DI4KKH6kpqIUiygjobarY3t7D.svg)](https://asciinema.org/a/DI4KKH6kpqIUiygjobarY3t7D)
<br>
<br>
<b>Рекурсивное сравнение (JSON, YAML)</b><br>
[![asciicast](https://asciinema.org/a/l06Si6SeU0dEysGqCqnhXV3g4.svg)](https://asciinema.org/a/l06Si6SeU0dEysGqCqnhXV3g4)
<br>
<br>
<b>Плоский формат отчета(--format plain)</b><br>
[![asciicast](https://asciinema.org/a/oVyqIHhj7jHS6BZjKGtJN2ibR.svg)](https://asciinema.org/a/oVyqIHhj7jHS6BZjKGtJN2ibR)
<br>
<br>
<b>Формат отчета json (--format json)</b><br>
[![asciicast](https://asciinema.org/a/5JiHc58y1FpuNkM9qFzUTF3QC.svg)](https://asciinema.org/a/5JiHc58y1FpuNkM9qFzUTF3QC)
<br>
<br>
Для вывода справочной информации введите gendiff с флагом -h:<br>
```
gendiff -h
Generate diff

Usage:
  gendiff (-h|--help)
  gendiff (-v|--version)
  gendiff [--format <fmt>] <firstFile> <secondFile>

Options:
  -h --help                     Show this screen
  -v --version                  Show version
  --format <fmt>                Report format [default: stylish]
```