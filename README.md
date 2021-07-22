# HTTP Notification System

A simple service that keeps track of different topics and allows clients subscribe to these topics, receiving notification messages based on the topics they subscribe to.

This is built using

## Requirements

- PHP 7.4+
- MySQL 5.7+
- Composer

## Installation

Clone the repository by running

```bash
git clone https://github.com/kennedy-osaze/http-notification-system.git
```

Install Dependencies
```bash
composer install
```

Run service
```bash
php -S localhost:8000 -t public
```

## API Reference

#### Subscribe to a Topic

```http
POST /subscribe/{topic}
{
    "url": "http://localhost:9000"
}
```

| Payload | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `url` | `string` | **Required**. The url to recieve notifications to |

#### Publish to Message to Topic Subscribers

```http
POST /publish/{topic}
{
    "data": {
        "message": "Hello World!"
    }
}
```

| Payload | Type     | Description                       |
| :-------- | :------- | :-------------------------------- |
| `data`      | `json` | **Required**. Must be a key-value paired |


To enable workers to send notifications, run
```bash
php artisan queue:work
```
## Running Tests

To run tests, run the following command

```bash
./vendor/bin/phpunit
```

## Technologies Used

- [PHP](php.net)
- [Lumen](https://lumen.laravel.com/)
- [MySQL](https://www.mysql.com/)
