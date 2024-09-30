# Develop & Publish

```bash
git init
git add .

git remote add origin git@github.com:ravuthz/laravel-crud.git
git branch -M main
git push -u origin main

git tag -a v1.0.0 -m "First Release"
git push origin v1.0.0

git tag -a v1.0.1 -m "Update Logs and ReadMe"
git push origin v1.0.1

git tag -a v1.1.0 -m "Update CRUD Resources and CLI"
git push origin v1.1.0

composer validate
composer update

```

```json
// composer.json
{
  // ...
  "minimum-stability": "dev",
  "prefer-stable": true,
  "repositories": {
    "local": {
      "type": "path",
      "url": "packages/ravuthz/laravel-crud",
      "options": {
        "symlink": true
      }
    }
  }
}
```

```bash

cd project
mkdir packages
mkdir packages/ravuthz
ln -s ~/Projects/laravel/lv11/laravel-crud packages/ravuthz/laravel-crud

composer require "ravuthz/laravel-crud" 

```
