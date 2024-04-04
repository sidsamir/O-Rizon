
# O'Rizon Project

Welcome to the O'Rizon project, a Symfony-based application designed to offer [short description of what your project does]. This README provides a comprehensive guide on how to set up and run the project on your local development environment.

## Prerequisites

Before you begin, ensure you have the following installed on your system:
- PHP 8.2 or higher
- Composer
- Symfony 7 or higher
- Symfony CLI
- Docker and Docker Compose
- Node.js and npm

## Installation

Follow these steps to install the project:

### Clone the Repository

First, clone the project repository to your local machine using Git:

```bash
git clone git@github.com:O-clock-Fajitas/projet-o-rizon.git
cd O'Rizon
```

### Install Dependencies

Use Composer to install PHP dependencies:

```bash
composer install
```

### Start the Development Server

To use Symfony commands, ensure you have the Symfony CLI installed on your system. Then, start the local web server:

```bash
symfony serve -d
```

This command runs the server in the background. To stop the server, you can use `symfony server:stop`.

### Build Tailwind CSS

```bash
symfony console tailwind:build --watch
```

This command watches for changes in your Tailwind files and rebuilds the styles automatically.

### Set Up Docker Environment

Make sure Docker is installed on your machine. To set up the Docker environment for the project, run:

```bash
docker compose up
```


## Usage

After completing the installation steps, the O'Rizon project should be running on your local development server. You can access it by navigating to `http://localhost:8000` in your web browser (or the port specified by the Symfony server).



Thank you for installing the O'Rizon project. For more information, visit [project documentation or website].


# Mercure 

## Installation

use ```composer require mercure``` to install the Mercure component.

## Configuration

Add the following configuration to your .env file:

***Create a token here : https://jwt.io/***

```bash

```dotenv
MERCURE_URL=http://mercure/.well-known/mercure
MERCURE_PUBLIC_URL=http://mercure/.well-known/mercure
MERCURE_JWT_SECRET='{!change me!}'
MERCURE_CORS_ALLOWED_ORIGINS=*
MERCURE_JWT='{!change me!}'
```

add the following configuration to docker compose file:

```yaml
   mercure:
     image: dunglas/mercure
     restart: unless-stopped
     environment:
       SERVER_NAME: ':80'
       MERCURE_PUBLISHER_JWT_KEY: '{!change me!}'
       MERCURE_SUBSCRIBER_JWT_KEY: '{!change me!}'
       MERCURE_EXTRA_DIRECTIVES: |
         cors_origins *
     command: /usr/bin/caddy run --config /etc/caddy/Caddyfile.dev
     volumes:
       - mercure_data:/data
       - mercure_config:/config
     ports:
       - "3000:80"
```

and a compose override file:

```yaml
  mercure:
    ports:
      - "8080:8080" # expose the mercure server on port 8080   
```

## Usage

To use Mercure, you need to create a hub and a publisher. The hub is the server that handles the subscriptions and the publisher is the client that sends updates to the hub.

### Create a Hub

To create a hub, use the following command:

```bash
docker compose up 
```

This command starts the Mercure server in the background.You run ```symfony serve -d``` to start the Symfony server in the background.

### Create a Publisher

To create a publisher, use the following code:

```php
use Symfony\Component\Mercure\Publisher;
use Symfony\Component\Mercure\Update;
use Symfony\Component\HttpClient\CurlHttpClient;

$httpClient = new CurlHttpClient();
$publisher = new Publisher('http://localhost:8080/.well-known/mercure', $httpClient);

$update = new Update(
    'https://example.com/books/1',
    json_encode(['status' => 'OutOfStock'])
);

$publisher($update);
```

### Create a Subscriber

To create a subscriber, use the following code:

```php
use Symfony\Component\Mercure\Jwt\StaticTokenProvider;
use Symfony\Component\Mercure\Subscriber;
use Symfony\Component\Mercure\Update;
use Symfony\Component\HttpClient\CurlHttpClient;

$httpClient = new CurlHttpClient();
$subscriber = new Subscriber('http://localhost:8080/.well-known/mercure', $httpClient);

$hub = $subscriber->connect(new StaticTokenProvider('your-jwt-token'));

foreach ($subscriber->getUpdates($hub) as $update) {
    // handle the update
}
```

### Example of usage for project

```php
 private function sendNotification(User $recipient, string $type, array $data): void
    {
        $currentUser = $this->security->getUser();
        $notification = new Notification();
        $notification->setRecipient($recipient);
        $notification->setMessage($data['message']);
        $notification->setSender($currentUser);
        $notification->setCreatedAt(new \DateTime());
        $this->entityManager->persist($notification);
        $this->entityManager->flush();

        $update = new Update(
            "https://mondomaine.com/user/{$recipient->getId()}/notifications",
            json_encode(['type' => $type, 'data' => $data])
        );
        $this->hub->publish($update);
    }
```

## Conclusion

Mercure is a powerful tool for real-time updates in your application. It is easy to set up and use, and it can be integrated with your existing Symfony application. With Mercure, you can create real-time notifications, live updates, and more, making your application more interactive and engaging for your users.