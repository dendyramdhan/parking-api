# Parking Lot REST API

This is a parking-lot REST API built on top of Lumen 5.8

## Installation & Run

##### First, clone the repo

```bash
$ git clone https://github.com/dendyramdhan/parking-api.git
$ cd parking-api
```

##### Create .env file

```bash
$ cp .env.example .env
```

##### Edit .env file

```env
APP_debug=false
```

##### Then install dependencies

```bash
$ composer install
```

##### Serving API

```bash
$ php -S localhost:8000 -t public
```

The API will be running on `localhost:8000`.

## Routes List

| Method     | URI                     | Action                                               |
| ---------- | ----------------------- | ---------------------------------------------------- |
| `GET/HEAD` | `create`                | `App\Http\Controllers\ParksController@create`        |
| `POST`     | `regist`                | `App\Http\Controllers\ParksController@regist`        |
| `POST`     | `out`                   | `App\Http\Controllers\ParksController@out`           |
| `GET/HEAD` | `reportByWarna/{warna}` | `App\Http\Controllers\ParksController@reportByWarna` |
| `GET/HEAD` | `reportByTipe/{tipe}`   | `App\Http\Controllers\ParksController@reportByTipe`  |
