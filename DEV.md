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

composer validate
composer update

```