# Parking Lot REST API

This is a parking-lot REST API built on top of Lumen 5.8

## Installation & Run

1. First, clone the repo
   `$ git clone https://github.com/dendyramdhan/parking-api.git`
2. create .env file
   `cp .env.example .env`
3. Edit .env file
   `APP_debug=false`
4. Then install dependencies
   `composer install`
5. Serving API
   `php -S localhost:8000 -t public`

The API will be running on `localhost:8000`.

## Routes List

| Method     | URI                     | Action                                               |
| ---------- | ----------------------- | ---------------------------------------------------- |
| `GET/HEAD` | `create`                | `App\Http\Controllers\ParksController@create`        |
| `POST`     | `regist`                | `App\Http\Controllers\ParksController@regist`        |
| `POST`     | `out`                   | `App\Http\Controllers\ParksController@out`           |
| `GET/HEAD` | `reportByWarna/{warna}` | `App\Http\Controllers\ParksController@reportByWarna` |
| `GET/HEAD` | `reportByTipe/{tipe}`   | `App\Http\Controllers\ParksController@reportByTipe`  |
