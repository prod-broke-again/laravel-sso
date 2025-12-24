# 🚀 Развертывание на GitHub и Packagist

## Шаг 1: Создание репозитория на GitHub

### Вариант 1: Новый репозиторий

1. Перейдите на [GitHub.com](https://github.com) и войдите в аккаунт
2. Нажмите "+" в правом верхнем углу → "New repository"
3. Заполните форму:
   - **Repository name**: `laravel-sso` или `packages-laravel-sso`
   - **Description**: "Laravel SSO package for cross-site authentication between partner applications"
   - **Visibility**: Public (для пакетов обычно Public)
   - **Add a README file**: ❌ НЕ добавляйте (у нас уже есть)
   - **Add .gitignore**: ❌ НЕ добавляйте (у нас уже есть)
   - **Choose a license**: ❌ НЕ добавляйте (у нас уже есть)

4. Нажмите "Create repository"

### Вариант 2: Импорт существующего репозитория

Если у вас уже есть локальный репозиторий:

```bash
# Добавляем удаленный репозиторий
git remote add origin https://github.com/YOUR_USERNAME/YOUR_REPO_NAME.git

# Отправляем код
git push -u origin master
```

## Шаг 2: Загрузка кода на GitHub

### Команды для загрузки:

```bash
# Если еще не инициализирован Git
cd laravel-sso-package
git init
git add .
git commit -m "Initial commit: Laravel SSO Package"

# Добавляем удаленный репозиторий
git remote add origin https://github.com/YOUR_USERNAME/laravel-sso.git

# Отправляем код
git push -u origin master
```

### Проверка статуса:

```bash
git status
git log --oneline
git remote -v
```

## Шаг 3: Регистрация на Packagist

### Packagist.org

1. Перейдите на [Packagist.org](https://packagist.org/)
2. Войдите через GitHub аккаунт
3. Нажмите "Submit" в верхнем меню
4. Введите URL вашего GitHub репозитория:
   ```
   https://github.com/YOUR_USERNAME/laravel-sso
   ```
5. Packagist автоматически найдет `composer.json`
6. Нажмите "Submit"

### Автоматические обновления

Packagist может автоматически обновлять пакет при пуше в GitHub. Для этого:

1. В репозитории Packagist нажмите "Settings"
2. В разделе "Automated Updates" убедитесь, что включено

## Шаг 4: Тестирование установки

### Локальное тестирование:

```bash
# Создаем тестовый Laravel проект
composer create-project laravel/laravel test-sso-app
cd test-sso-app

# Добавляем локальный пакет для тестирования
# Вариант 1: Через локальный путь
composer config repositories.laravel-sso path ../laravel-sso-package
composer require packages/laravel-sso:dev-main

# Вариант 2: Через GitHub (после публикации)
composer require packages/laravel-sso
```

### Проверка установки:

```bash
php artisan vendor:publish --tag=sso-config
php artisan vendor:publish --tag=sso-migrations
php artisan migrate
php artisan route:list | grep sso
```

## Шаг 5: Создание релизов

### Создание GitHub Release:

1. Перейдите на страницу репозитория
2. Нажмите "Releases" в правой панели
3. Нажмите "Create a new release"
4. Заполните:
   - **Tag version**: `v1.0.0` (следуйте semantic versioning)
   - **Target**: `master`
   - **Release title**: `Version 1.0.0`
   - **Description**: Опишите изменения
5. Нажмите "Publish release"

### Semantic Versioning:

- `MAJOR.MINOR.PATCH`
- `MAJOR`: Несовместимые изменения
- `MINOR`: Новые функции (backward compatible)
- `PATCH`: Исправления багов (backward compatible)

## Шаг 6: CI/CD с GitHub Actions

Файл `.github/workflows/tests.yml` уже создан. Он будет:

- Запускать тесты на PHP 8.4
- Проверять код с PHPStan
- Форматировать код с Pint
- Работать при push и pull requests

### Добавление бейджей:

В README.md добавьте:

```markdown
[![Tests](https://github.com/YOUR_USERNAME/laravel-sso/actions/workflows/tests.yml/badge.svg)](https://github.com/YOUR_USERNAME/laravel-sso/actions/workflows/tests.yml)
[![Latest Version](https://img.shields.io/packagist/v/packages/laravel-sso.svg)](https://packagist.org/packages/packages/laravel-sso)
[![Total Downloads](https://img.shields.io/packagist/dt/packages/laravel-sso.svg)](https://packagist.org/packages/packages/laravel-sso)
```

## Шаг 7: Поддержка и обновления

### Обновление пакета:

```bash
# Вносите изменения
git add .
git commit -m "Add new feature"
git push origin master

# Создайте новый релиз на GitHub
# Packagist автоматически обновится
```

### Сообщество:

- Создайте issues для багрепортов
- Используйте discussions для вопросов
- Добавьте contributing guidelines
- Создайте code of conduct

## Полезные команды:

```bash
# Проверка статуса
git status
git log --oneline

# Работа с ветками
git checkout -b feature/new-feature
git merge feature/new-feature

# Теги для релизов
git tag v1.0.0
git push origin v1.0.0

# Удаленные репозитории
git remote -v
git remote set-url origin NEW_URL
```

## Безопасность:

- Никогда не коммитьте ключи API или пароли
- Используйте `.env` файлы для локальной разработки
- Настройте Dependabot для обновления зависимостей
- Регулярно обновляйте зависимости безопасности

---

🎉 **Поздравляем!** Ваш пакет теперь доступен для установки через Composer со всего мира!
